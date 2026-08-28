<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\Credenciado;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class CredenciadosDashboard extends Component
{
    #[Url(as: 'inicio', except: '')]
    public string $dataInicio = '';

    #[Url(as: 'fim', except: '')]
    public string $dataFim = '';

    #[Url(as: 'bloco', except: '')]
    public string $blocoId = '';

    #[Url(as: 'regiao', except: '')]
    public string $regiaoId = '';

    #[Url(as: 'igreja', except: '')]
    public string $igrejaId = '';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->bloco_id != 21) {
            $this->blocoId = (string) $user->bloco_id;
        }
    }

    public function updatedBlocoId(): void
    {
        if (Auth::user()->bloco_id != 21) {
            $this->blocoId = (string) Auth::user()->bloco_id;
        }

        $this->reset('regiaoId', 'igrejaId');
    }

    public function updatedRegiaoId(): void
    {
        $this->reset('igrejaId');
    }

    public function limparFiltros(): void
    {
        $this->reset('dataInicio', 'dataFim', 'regiaoId', 'igrejaId');
        $this->blocoId = Auth::user()->bloco_id != 21
            ? (string) Auth::user()->bloco_id
            : '';
    }

    private function queryBase(): Builder
    {
        $user = Auth::user();

        return Credenciado::query()
            ->when($user->bloco_id != 21, fn (Builder $query) => $query->where('credenciados.bloco_id', $user->bloco_id))
            ->when($this->blocoId !== '', fn (Builder $query) => $query->where('credenciados.bloco_id', $this->blocoId))
            ->when($this->regiaoId !== '', fn (Builder $query) => $query->where('credenciados.regiao_id', $this->regiaoId))
            ->when($this->igrejaId !== '', fn (Builder $query) => $query->where('credenciados.igreja_id', $this->igrejaId))
            ->when($this->dataInicio !== '', fn (Builder $query) => $query->whereDate('credenciados.created_at', '>=', $this->dataInicio))
            ->when($this->dataFim !== '', fn (Builder $query) => $query->whereDate('credenciados.created_at', '<=', $this->dataFim));
    }

    private function quantidadePor(string $tabela, string $foreignKey, string $nome = 'nome')
    {
        return (clone $this->queryBase())
            ->join($tabela, "{$tabela}.id", '=', "credenciados.{$foreignKey}")
            ->selectRaw("{$tabela}.id, {$tabela}.{$nome} as nome, COUNT(DISTINCT credenciados.id) as total")
            ->groupBy("{$tabela}.id", "{$tabela}.{$nome}")
            ->orderByDesc('total')
            ->orderBy("{$tabela}.{$nome}")
            ->get();
    }

    public function render()
    {
        $hoje = CarbonImmutable::today();
        $limite = $hoje->addDays(30);
        $base = $this->queryBase();

        $indicadores = [
            'total' => (clone $base)->count(),
            'com_credencial' => (clone $base)->whereHas('credencialPresidios')->count(),
            'sem_credencial' => (clone $base)->whereDoesntHave('credencialPresidios')->count(),
            'validas' => (clone $base)->whereHas('credencialPresidios', fn (Builder $query) => $query
                ->where('unidade_nao_faz', false)
                ->whereDate('data_vencimento', '>=', $hoje))->count(),
            'vencendo' => (clone $base)->whereHas('credencialPresidios', fn (Builder $query) => $query
                ->where('unidade_nao_faz', false)
                ->whereBetween('data_vencimento', [$hoje, $limite]))->count(),
            'vencidas' => (clone $base)->whereHas('credencialPresidios', fn (Builder $query) => $query
                ->where('unidade_nao_faz', false)
                ->whereDate('data_vencimento', '<', $hoje))->count(),
            'sem_validade' => (clone $base)->whereHas('credencialPresidios', fn (Builder $query) => $query
                ->where('unidade_nao_faz', false)
                ->whereNull('data_vencimento'))->count(),
            'unidade_nao_faz' => (clone $base)->whereHas('credencialPresidios', fn (Builder $query) => $query
                ->where('unidade_nao_faz', true))->count(),
        ];

        $presidios = (clone $base)
            ->join('credencial_presidios', 'credencial_presidios.credenciado_id', '=', 'credenciados.id')
            ->join('presidios', 'presidios.id', '=', 'credencial_presidios.presidio_id')
            ->selectRaw('presidios.id, presidios.nome, COUNT(DISTINCT credenciados.id) as total')
            ->groupBy('presidios.id', 'presidios.nome')
            ->orderByDesc('total')
            ->orderBy('presidios.nome')
            ->get();

        $regioesDisponiveis = Regiao::query()
            ->when($this->blocoId !== '', fn (Builder $query) => $query->where('bloco_id', $this->blocoId))
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $igrejasDisponiveis = Igreja::query()
            ->when($this->regiaoId !== '', fn (Builder $query) => $query->where('regiao_id', $this->regiaoId))
            ->when($this->regiaoId === '' && $this->blocoId !== '', fn (Builder $query) => $query->where('bloco_id', $this->blocoId))
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('livewire.universal.credenciados-dashboard', [
            'indicadores' => $indicadores,
            'blocos' => $this->quantidadePor('blocos', 'bloco_id'),
            'regioes' => $this->quantidadePor('regiaos', 'regiao_id'),
            'igrejas' => $this->quantidadePor('igrejas', 'igreja_id'),
            'cargos' => $this->quantidadePor('cargos', 'cargo_id'),
            'categorias' => $this->quantidadePor('categorias', 'categoria_id'),
            'presidios' => $presidios,
            'blocosDisponiveis' => Bloco::orderBy('nome')->get(['id', 'nome']),
            'regioesDisponiveis' => $regioesDisponiveis,
            'igrejasDisponiveis' => $igrejasDisponiveis,
            'recentes' => (clone $base)
                ->with(['bloco:id,nome', 'igreja:id,nome'])
                ->latest('credenciados.created_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
