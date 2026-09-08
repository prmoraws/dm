<?php

namespace App\Livewire\Unp;

use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpPresenca;
use App\Models\Unp\CursoUnpTurma;
use Livewire\Component;

class CursoUnpDashboard extends Component
{
    public function render()
    {
        $situacoes = CursoUnpMatricula::query()
            ->selectRaw('situacao, COUNT(*) as total')
            ->groupBy('situacao')
            ->pluck('total', 'situacao');

        return view('livewire.unp.curso-unp-dashboard', [
            'totalCaptacoes' => CursoUnpCaptacao::query()->count(),
            'captacoesPendentes' => CursoUnpCaptacao::query()->where('status', 'pendente')->count(),
            'totalTurmas' => CursoUnpTurma::query()->count(),
            'turmasAtivas' => CursoUnpTurma::query()->whereIn('status', ['aberta', 'em_andamento'])->count(),
            'totalMatriculas' => CursoUnpMatricula::query()->count(),
            'presentesHoje' => CursoUnpPresenca::query()->whereDate('data_aula', today())->where('situacao', 'presente')->count(),
            'situacoes' => $situacoes,
            'turmasRecentes' => CursoUnpTurma::query()->withCount('matriculas')->orderByDesc('data_inicio')->limit(5)->get(),
            'captacoesRecentes' => CursoUnpCaptacao::query()->with('igreja')->latest()->limit(5)->get(),
        ]);
    }
}
