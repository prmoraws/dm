<?php

namespace App\Livewire\Universal;

use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

class TdaDashboard extends Component
{
    private function base(): Builder
    {
        return CadastroTda::query();
    }

    private function quantidade(string $campo, mixed $valor = true): int
    {
        return (clone $this->base())->where($campo, $valor)->count();
    }

    private function porCondicao(): Collection
    {
        $rotulos = [
            'membro' => 'Membros',
            'cpo' => 'CPO',
            'colaborador' => 'Colaboradores',
            'obreiro' => 'Obreiros',
            'levita' => 'Levitas',
            'auxiliar' => 'Auxiliares',
        ];

        $totais = (clone $this->base())
            ->selectRaw('LOWER(condicao_atual) as chave, COUNT(*) as total')
            ->groupByRaw('LOWER(condicao_atual)')
            ->pluck('total', 'chave');

        return collect($rotulos)->map(fn (string $rotulo, string $chave) => [
            'chave' => $chave,
            'nome' => $rotulo,
            'total' => (int) ($totais[$chave] ?? 0),
        ])->values();
    }

    private function porBloco(): Collection
    {
        return (clone $this->base())
            ->leftJoin('blocos', 'blocos.id', '=', 'cadastro_tdas.bloco_id')
            ->selectRaw("cadastro_tdas.bloco_id, COALESCE(blocos.nome, 'Sem bloco') as nome, COUNT(*) as total")
            ->groupBy('cadastro_tdas.bloco_id', 'blocos.nome')
            ->orderByDesc('total')
            ->get();
    }

    private function porRegiao(): Collection
    {
        return (clone $this->base())
            ->leftJoin('regiaos', 'regiaos.id', '=', 'cadastro_tdas.regiao_id')
            ->leftJoin('blocos', 'blocos.id', '=', 'cadastro_tdas.bloco_id')
            ->selectRaw("cadastro_tdas.regiao_id, COALESCE(regiaos.nome, 'Sem região') as nome, COALESCE(blocos.nome, 'Sem bloco') as bloco, COUNT(*) as total")
            ->groupBy('cadastro_tdas.regiao_id', 'regiaos.nome', 'blocos.nome')
            ->orderByDesc('total')
            ->get();
    }

    private function porIgreja(): Collection
    {
        return (clone $this->base())
            ->leftJoin('igrejas', 'igrejas.id', '=', 'cadastro_tdas.igreja_id')
            ->leftJoin('regiaos', 'regiaos.id', '=', 'cadastro_tdas.regiao_id')
            ->leftJoin('blocos', 'blocos.id', '=', 'cadastro_tdas.bloco_id')
            ->selectRaw("cadastro_tdas.igreja_id, COALESCE(igrejas.nome, 'Sem igreja') as nome, COALESCE(regiaos.nome, 'Sem região') as regiao, COALESCE(blocos.nome, 'Sem bloco') as bloco, COUNT(*) as total")
            ->groupBy('cadastro_tdas.igreja_id', 'igrejas.nome', 'regiaos.nome', 'blocos.nome')
            ->orderByDesc('total')
            ->get();
    }

    public function render()
    {
        $total = $this->base()->count();
        $casados = (clone $this->base())->whereRaw("LOWER(estado_civil) LIKE 'casad%'")->count();
        $solteiros = (clone $this->base())->whereRaw("LOWER(estado_civil) LIKE 'solteir%'")->count();
        $intellimen = (clone $this->base())->where(function (Builder $query) {
            $query->where('intellimen_reunioes', true)->orWhere('intellimen_desafios', true);
        })->count();

        return view('livewire.universal.tda-dashboard', [
            'total' => $total,
            'pendentes' => CaptacaoTda::where('status', 'pendente')->count(),
            'condicoes' => $this->porCondicao(),
            'destaques' => collect([
                ['nome' => 'Com Espírito Santo', 'total' => $this->quantidade('batizado_espirito_santo')],
                ['nome' => 'Mulheres', 'total' => $this->quantidade('sexo', 'feminino')],
                ['nome' => 'Homens', 'total' => $this->quantidade('sexo', 'masculino')],
                ['nome' => 'Casados', 'total' => $casados],
                ['nome' => 'Solteiros', 'total' => $solteiros],
                ['nome' => 'IntelliMen', 'total' => $intellimen],
                ['nome' => 'Godllywood', 'total' => $this->quantidade('godllywood_autoajuda')],
            ]),
            'blocos' => $this->porBloco(),
            'regioes' => $this->porRegiao(),
            'igrejas' => $this->porIgreja(),
        ]);
    }
}
