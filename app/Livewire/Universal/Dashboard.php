<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Pastor;
use App\Models\Universal\Igreja;
use App\Models\Universal\Pessoa;
use App\Models\Universal\Regiao;
use App\Models\Universal\Bloco;
use App\Models\Universal\Banner;
use App\Models\Universal\Categoria;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    // Contadores para os cards
    public $pessoasCount;
    public $igrejasCount;
    public $pastoresCount;
    public $regioesCount;
    public $blocosCount;
    public $bannersCount;
    public $categoriasCount;
    public $pessoasPorBloco;
    public $pessoasPorIgreja;

    // Dados para listas e gráficos
    public $igrejasPorRegiao;
    public $chartPessoasPorRegiao;
    public $chartIgrejasPorBloco;

    // Mensagem de feedback
    public $message = '';

    /**
     * Chave e TTL do cache do dashboard.
     * AJUSTE: Antes, refreshDashboard() rodava ~6 queries agregadas
     * pesadas TODA VEZ que a página era aberta (mount() chamava
     * refreshDashboard() diretamente). Isso multiplicado por visitas
     * frequentes foi a principal causa do pico de CPU no InfinityFree.
     * Agora os dados ficam em cache por 5 minutos e só são recalculados
     * quando o cache expira ou o usuário clica em "Atualizar".
     */
    private const CACHE_KEY = 'dashboard.universal.data';
    private const CACHE_TTL_SECONDS = 300; // 5 minutos

    public function mount()
    {
        $this->loadFromCache();
    }

    /**
     * Botão "Atualizar": força o recálculo ignorando o cache.
     */
    public function refreshDashboard()
    {
        Cache::forget(self::CACHE_KEY);
        $this->loadFromCache();

        $this->dispatch('dashboardRefreshed');
        $this->message = 'Dashboard atualizado com sucesso!';
    }

    /**
     * Carrega os dados do cache (ou calcula e armazena, se expirado/ausente)
     * e popula as propriedades públicas do componente.
     */
    private function loadFromCache(): void
    {
        $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            return $this->buildDashboardData();
        });

        $this->pessoasPorBloco        = $data['pessoasPorBloco'];
        $this->pessoasPorIgreja       = $data['pessoasPorIgreja'];
        $this->pessoasCount           = $data['pessoasCount'];
        $this->igrejasCount           = $data['igrejasCount'];
        $this->pastoresCount          = $data['pastoresCount'];
        $this->regioesCount           = $data['regioesCount'];
        $this->blocosCount            = $data['blocosCount'];
        $this->bannersCount           = $data['bannersCount'];
        $this->categoriasCount        = $data['categoriasCount'];
        $this->igrejasPorRegiao       = $data['igrejasPorRegiao'];
        $this->chartPessoasPorRegiao  = $data['chartPessoasPorRegiao'];
        $this->chartIgrejasPorBloco   = $data['chartIgrejasPorBloco'];
    }

    /**
     * Executa de fato as queries no banco. Só é chamado quando o cache
     * está vazio/expirado (a cada 5 min, no máximo) ou após um refresh manual.
     */
    private function buildDashboardData(): array
    {
        // Pessoas por Bloco
        $pessoasPorBloco = Pessoa::select(
                'bloco_id',
                DB::raw('COUNT(*) as total_pessoas')
            )
            ->groupBy('bloco_id')
            ->with('bloco:id,nome')
            ->orderByDesc('total_pessoas')
            ->get()
            ->map(fn($item) => [
                'bloco' => $item->bloco->nome ?? 'Sem Bloco',
                'total_pessoas' => $item->total_pessoas,
            ])
            ->toArray();

        // Pessoas por Igreja
        $pessoasPorIgreja = Pessoa::select(
                'igreja_id',
                DB::raw('COUNT(*) as total_pessoas')
            )
            ->groupBy('igreja_id')
            ->with('igreja:id,nome')
            ->orderByDesc('total_pessoas')
            ->get()
            ->map(fn($item) => [
                'igreja' => $item->igreja->nome ?? 'Sem Igreja',
                'total_pessoas' => $item->total_pessoas,
            ])
            ->toArray();

        // Contagens para os cards
        $pessoasCount    = Pessoa::count();
        $igrejasCount    = Igreja::count();
        $pastoresCount   = Pastor::count();
        $regioesCount    = Regiao::count();
        $blocosCount     = Bloco::count();
        $bannersCount    = Banner::count();
        $categoriasCount = Categoria::count();

        // Lista de Igrejas por Região
        $igrejasPorRegiao = Igreja::select('regiao_id', DB::raw('COUNT(*) as total_igrejas'))
            ->groupBy('regiao_id')
            ->with('regiao:id,nome')
            ->get()
            ->map(fn($item) => [
                'regiao' => $item->regiao->nome ?? 'Sem Região',
                'total_igrejas' => $item->total_igrejas,
            ])
            ->sortBy('regiao')
            ->values()
            ->toArray();

        // Dados para Gráfico 1: Pessoas por Região
        $pessoasData = Pessoa::select('regiao_id', DB::raw('COUNT(*) as total_pessoas'))
            ->groupBy('regiao_id')
            ->with('regiao:id,nome')
            ->get();

        $chartPessoasPorRegiao = [
            'labels' => $pessoasData->pluck('regiao.nome')->map(fn($name) => $name ?? 'Sem Região')->toArray(),
            'data' => $pessoasData->pluck('total_pessoas')->toArray(),
        ];

        // Gráfico 2: Igrejas por Bloco
        $igrejasPorBlocoData = Igreja::select('bloco_id', DB::raw('COUNT(*) as total_igrejas'))
            ->groupBy('bloco_id')
            ->with('bloco:id,nome')
            ->orderBy('total_igrejas', 'desc')
            ->get();

        $chartIgrejasPorBloco = [
            'labels' => $igrejasPorBlocoData->pluck('bloco.nome')->map(fn($name) => $name ?? 'Sem Bloco')->toArray(),
            'data' => $igrejasPorBlocoData->pluck('total_igrejas')->toArray(),
        ];

        return [
            'pessoasPorBloco'       => $pessoasPorBloco,
            'pessoasPorIgreja'      => $pessoasPorIgreja,
            'pessoasCount'          => $pessoasCount,
            'igrejasCount'          => $igrejasCount,
            'pastoresCount'         => $pastoresCount,
            'regioesCount'          => $regioesCount,
            'blocosCount'           => $blocosCount,
            'bannersCount'          => $bannersCount,
            'categoriasCount'       => $categoriasCount,
            'igrejasPorRegiao'      => $igrejasPorRegiao,
            'chartPessoasPorRegiao' => $chartPessoasPorRegiao,
            'chartIgrejasPorBloco'  => $chartIgrejasPorBloco,
        ];
    }

    public function redirectTo($route)
    {
        return redirect()->route($route);
    }

    public function exportData()
    {
        $this->message = 'Exportação iniciada...';
    }

    public function render()
    {
        return view('livewire.universal.dashboard');
    }
}
