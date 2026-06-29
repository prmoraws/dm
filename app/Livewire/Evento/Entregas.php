<?php

namespace App\Livewire\Evento;

use App\Models\Evento\Cesta;
use App\Models\Evento\Instituicao;
use App\Models\Evento\Terreiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Entregas extends Component
{
    use WithPagination;

    // Propriedades do Componente
    public $search = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';
    public $selectedEntidade = null;
    public $errorMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'nome'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        Log::info('Componente Entregas inicializado');
    }

    public function updatedSearch()
    {
        Log::info('Pesquisa atualizada', ['search' => $this->search]);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
        Log::info('Ordenação aplicada', ['field' => $this->sortField, 'direction' => $this->sortDirection]);
    }

    public function viewDetails($id, $tipo)
    {
        Log::info('Visualização de detalhes solicitada', ['id' => $id, 'tipo' => $tipo]);
        try {
            $entidade = $tipo === 'terreiro' ? Terreiro::findOrFail($id) : Instituicao::findOrFail($id);
            $cesta = Cesta::where('nome', $entidade->nome)->first();

            $this->selectedEntidade = [
                'nome' => strtoupper($entidade->nome),
                'cestas' => $cesta->cestas ?? null,
                'foto' => $cesta->foto ?? null,
                'observacao' => $cesta->observacao ?? null,
                'convidados' => $entidade->convidados,
                'tipo' => $tipo,
            ];
            Log::info('Entidade carregada para visualização', ['entidade' => $this->selectedEntidade]);
        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao carregar os detalhes: ' . $e->getMessage();
            Log::error('Erro ao visualizar entidade', ['id' => $id, 'tipo' => $tipo, 'exception' => $e->getMessage()]);
        }
    }

    public function closeModal()
    {
        Log::info('Modal de visualização fechado');
        $this->selectedEntidade = null;
        $this->errorMessage = '';
    }

    public function render()
    {
        $search = $this->search;

        $terreirosQuery = Terreiro::query()
            ->select(
                'terreiros.id',
                'terreiros.nome',
                'terreiros.convidados',
                'cestas.cestas',
                DB::raw("'terreiro' as tipo")
            )
            ->leftJoin('cestas', 'terreiros.nome', '=', 'cestas.nome');

        $instituicoesQuery = Instituicao::query()
            ->select(
                'instituicoes.id',
                'instituicoes.nome',
                'instituicoes.convidados',
                'cestas.cestas',
                DB::raw("'instituicao' as tipo")
            )
            ->leftJoin('cestas', 'instituicoes.nome', '=', 'cestas.nome')
            ->union($terreirosQuery);

        // A união deve ser tratada como uma subquery para aplicar where e order by
        $results = DB::table($instituicoesQuery, 'union_table')
            ->when($search, function ($query) use ($search) {
                $query->where('nome', 'like', '%' . $search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.evento.entregas', [
            'results' => $results,
        ])->layout('layouts.app');
    }
}
