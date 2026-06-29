<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Curso;
use App\Models\Unp\Formatura;
use App\Models\Unp\Grupo;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Contadores para os cards
    public $cursosCount;
    public $instrutoresCount;
    public $gruposCount;
    public $formaturasCount;
    public $presidiosCount;
    public $cursosAtivos;

    // Dados para gráficos
    public $chartCursosPorPresidio;
    public $chartFormaturasPorMes;

    // Mensagem de feedback
    public $message = '';

    public function mount()
    {
        $this->refreshDashboard();
    }

    /**
     * Centraliza a lógica de carregamento de dados para o dashboard.
     */
    public function refreshDashboard()
    {
        // Contagem para os cards
        $this->cursosCount = Curso::count();
        $this->instrutoresCount = Instrutor::count();
        $this->gruposCount = Grupo::count();
        $this->formaturasCount = Formatura::count();
        $this->presidiosCount = Presidio::count();

        // Lista de cursos ativos para o card
        $this->cursosAtivos = Curso::with('presidio:id,nome')
            ->where('status', 'Cursando')
            ->orderBy('fim', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($curso) {
                $fim = Carbon::parse($curso->fim);
                $now = Carbon::now();
                $alertColor = null;
                if ($fim->isPast()) {
                    $alertColor = 'gray'; // Finalizado, mas ainda listado como 'Cursando'
                } elseif ($fim->diffInDays($now) <= 30) {
                    $alertColor = 'red'; // Menos de 30 dias para terminar
                } elseif ($fim->diffInDays($now) <= 60) {
                    $alertColor = 'yellow'; // Entre 30 e 60 dias
                }
                return [
                    'nome' => $curso->nome,
                    'presidio' => $curso->presidio->nome ?? 'N/A',
                    'fim' => $fim->format('d/m/Y'),
                    'alert_color' => $alertColor,
                ];
            })
            ->toArray();

        // Dados para Gráfico: Cursos por Presídio
        // CORREÇÃO: Utilizando a relação correta 'presidio'
        $cursosPorPresidio = Curso::join('presidios', 'cursos.presidio_id', '=', 'presidios.id')
            ->select('presidios.nome as presidio_nome', DB::raw('COUNT(cursos.id) as total_cursos'))
            ->groupBy('presidios.nome')
            ->orderBy('total_cursos', 'desc')
            ->get();

        $this->chartCursosPorPresidio = [
            'labels' => $cursosPorPresidio->pluck('presidio_nome')->toArray(),
            'data' => $cursosPorPresidio->pluck('total_cursos')->toArray(),
        ];

        // Dados para Gráfico: Formaturas por Mês
        $formaturasPorMes = Formatura::selectRaw('DATE_FORMAT(formatura, "%Y-%m") as mes, COUNT(*) as total_formaturas')
            ->whereNotNull('formatura')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $this->chartFormaturasPorMes = [
            'labels' => $formaturasPorMes->pluck('mes')->toArray(),
            'data' => $formaturasPorMes->pluck('total_formaturas')->toArray(),
        ];

        // Dispara evento para o JavaScript reconstruir os gráficos
        $this->dispatch('dashboardRefreshed');
        $this->message = 'Dashboard atualizado com sucesso!';
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
        return view('livewire.unp.dashboard');
    }
}
