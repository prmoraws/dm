<?php

namespace App\Livewire\Evento;

use App\Models\Evento\Cesta;
use App\Models\Evento\Instituicao;
use App\Models\Evento\Terreiro;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CestasExport;

class Dashboard extends Component
{
    // Propriedades para os Cards Principais
    public $terreirosCount;
    public $convidadosCount;
    public $instituicoesCount;
    public $instituicoesConvidados;
    public $totalGeralTerreirosInstituicoes;
    public $totalConvidadosGeral;

    // Propriedades para o Card de Blocos
    public $blocosConvidados;
    public $blocosTerreirosCount;

    // Propriedades para o novo Card de Distribuição por Classe
    public $totalCestas;
    public $totalCestasTerreiros;
    public $totalCestasInstituicoes;
    public $totalCestasIURD;

    // Propriedade para o novo Gráfico
    public $chartDistribuicaoClasse;

    // Controles da UI
    public $message = '';
    public $exportLoading = false;
    public $exportFormat = 'pdf';

    private function sanitizeUtf8($value)
    {
        if (is_string($value)) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        if (is_array($value)) {
            return array_map([$this, 'sanitizeUtf8'], $value);
        }
        return $value;
    }

    public function mount()
    {
        Log::channel('dashboard')->info('Inicializando Dashboard');
        $this->cleanTempFiles(); // Chamar limpeza ao montar o componente

        try {
            // Dados para os cards de contagem geral
            $this->terreirosCount = Cache::remember('dashboard_terreiros_count', 3600, fn() => Terreiro::count());
            $this->convidadosCount = Cache::remember('dashboard_convidados_count', 3600, fn() => Terreiro::sum('convidados'));
            $this->instituicoesCount = Cache::remember('dashboard_instituicoes_count', 3600, fn() => Instituicao::count());
            $this->instituicoesConvidados = Cache::remember('dashboard_instituicoes_convidados', 3600, fn() => Instituicao::sum('convidados'));
            $this->totalGeralTerreirosInstituicoes = $this->terreirosCount + $this->instituicoesCount;
            $this->totalConvidadosGeral = $this->convidadosCount + $this->instituicoesConvidados;

            // Dados para os cards de blocos
            $this->blocosConvidados = Cache::remember('dashboard_blocos_convidados', 3600, fn() => Terreiro::select('bloco')
                ->selectRaw('SUM(convidados) as total_convidados')
                ->groupBy('bloco')->orderBy('bloco')->get()->pluck('total_convidados', 'bloco')->toArray());

            $this->blocosTerreirosCount = Cache::remember('dashboard_blocos_terreiros_count', 3600, fn() => Terreiro::select('bloco')
                ->selectRaw('COUNT(*) as total_terreiros')
                ->groupBy('bloco')->orderBy('bloco')->get()->pluck('total_terreiros', 'bloco')->toArray());

            // --- NOVA LÓGICA DE DISTRIBUIÇÃO DE CESTAS ---

            // 1. Total geral de cestas distribuídas
            $this->totalCestas = Cache::remember('dashboard_total_cestas', 3600, fn() => Cesta::sum('cestas'));

            // 2. Total de cestas para Terreiros
            $terreirosNomes = Cache::remember('dashboard_terreiros_nomes', 3600, fn() => Terreiro::pluck('nome')->toArray());
            $this->totalCestasTerreiros = Cache::remember('dashboard_cestas_sum_terreiros', 3600, fn() => Cesta::whereIn('nome', $terreirosNomes)->sum('cestas'));

            // 3. Definir a classe IURD e calcular suas cestas
            $iurdNomes = ['FAZENDA NOVA CANAÃ', 'OBREIROS - PR HELENO', 'SUITES - DNA SARA', 'SÍTIO VILAS'];
            $this->totalCestasIURD = Cache::remember('dashboard_cestas_sum_iurd', 3600, fn() => Cesta::whereIn('nome', $iurdNomes)->sum('cestas'));

            // 4. Calcular cestas para as outras Instituições (excluindo IURD)
            $instituicoesNomes = Cache::remember('dashboard_instituicoes_nomes_sem_iurd', 3600, fn() => Instituicao::whereNotIn('nome', $iurdNomes)->pluck('nome')->toArray());
            $this->totalCestasInstituicoes = Cache::remember('dashboard_cestas_sum_instituicoes', 3600, fn() => Cesta::whereIn('nome', $instituicoesNomes)->sum('cestas'));

            // 5. Preparar dados para o novo gráfico de distribuição por classe
            $this->chartDistribuicaoClasse = [
                'labels' => $this->sanitizeUtf8(['Terreiros', 'Instituições', 'IURD']),
                'data' => [
                    $this->totalCestasTerreiros,
                    $this->totalCestasInstituicoes,
                    $this->totalCestasIURD,
                ],
            ];

            Log::channel('dashboard')->info('Dados do Dashboard carregados com sucesso');
        } catch (\Exception $e) {
            $this->message = $this->sanitizeUtf8('Erro ao carregar dados: ' . $e->getMessage());
            Log::channel('dashboard')->error('Erro ao carregar Dashboard', ['exception' => $e->getMessage()]);
        }
    }

    public function redirectTo($route)
    {
        Log::channel('dashboard')->info('Redirecionando para rota', ['route' => $route]);
        return redirect()->route($route);
    }

    public function exportData()
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(120);

            $cestas = Cesta::orderBy('cestas', 'desc')->orderBy('nome', 'asc')->get();
            $basePath = public_path();
            $thumbnailDir = 'temp/thumbnails';
            $cestasDir = 'cestas';
            $tempDir = 'temp';

            \Log::channel('dashboard')->info('Iniciando exportação', [
                'basePath' => $basePath,
                'thumbnailDir' => $thumbnailDir,
                'cestasDir' => $cestasDir,
                'tempDir' => $tempDir
            ]);

            foreach ([$thumbnailDir, $tempDir] as $dir) {
                $fullPath = $basePath . '/' . $dir;
                if (!file_exists($fullPath)) {
                    \Log::channel('dashboard')->info('Criando diretório', ['path' => $fullPath]);
                    mkdir($fullPath, 0755, true);
                }
            }

            $cestas = $cestas->map(function ($cesta) use ($basePath, $cestasDir, $thumbnailDir) {
                if (!$cesta->foto) return $cesta;
                $filename = basename($cesta->foto);
                $originalPath = $basePath . '/' . $cestasDir . '/' . $filename;
                $thumbnailPath = $basePath . '/' . $thumbnailDir . '/' . $filename;

                if (!file_exists($originalPath)) {
                    $cesta->foto = null;
                    return $cesta;
                }

                if (!file_exists($thumbnailPath)) {
                    try {
                        Image::read($originalPath)->resize(200, 150, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->save($thumbnailPath, 70);
                    } catch (\Exception $e) {
                        \Log::channel('dashboard')->error('Erro ao criar thumbnail', ['error' => $e->getMessage(), 'path' => $originalPath]);
                    }
                }
                $cesta->foto = $thumbnailDir . '/' . $filename;
                return $cesta;
            });

            $pdf = Pdf::loadView('livewire.evento.relatorio-cestas-pdf', [
                'cestas' => $cestas,
                'printDate' => now()->format('d/m/Y H:i:s')
            ])->setOptions([
                'isRemoteEnabled' => true,
                'dpi' => 72,
                'enable_local_file_access' => true,
                'chroot' => $basePath,
                'enable_javascript' => false,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8'
            ]);

            $pdfContent = $pdf->output();
            $filename = 'relatorio-cestas-' . now()->format('YmdHis') . '.pdf';
            $pdfPath = $basePath . '/' . $tempDir . '/' . $filename;

            file_put_contents($pdfPath, $pdfContent);
            $url = url('/' . $tempDir . '/' . $filename);
            $this->dispatch('exportDataCompleted', url: $url);
        } catch (\Exception $e) {
            \Log::channel('dashboard')->error('Erro ao gerar PDF', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->dispatch('exportDataCompleted', error: $e->getMessage());
        }
    }

    protected function cleanTempFiles()
    {
        $tempDir = Storage::disk('public')->path('temp');
        $tempDir = str_replace('\\', '/', $tempDir);
        if (file_exists($tempDir)) {
            $files = glob($tempDir . '/*.{xlsx,pdf}', GLOB_BRACE);
            $now = now()->timestamp;
            foreach ($files as $file) {
                if (filemtime($file) < $now - 1800) { // 30 minutos
                    try {
                        unlink($file);
                        Log::channel('dashboard')->info('Arquivo temporário removido', ['file' => $file]);
                    } catch (\Exception $e) {
                        Log::channel('dashboard')->error('Erro ao remover arquivo temporário', ['file' => $file, 'exception' => $e->getMessage()]);
                    }
                }
            }
        }
    }

    public function refreshDashboard()
    {
        Log::channel('dashboard')->info('Atualizando Dashboard');
        Cache::flush();
        $this->mount();
        $this->message = $this->sanitizeUtf8('Dashboard atualizado com sucesso!');
        $this->dispatch('dashboardRefreshed');
    }

    public function render()
    {
        Log::channel('dashboard')->info('Renderizando Dashboard');
        return view('livewire.evento.dashboard')->layout('layouts.app', [
            'title' => 'Dashboard de Eventos',
        ]);
    }
}
