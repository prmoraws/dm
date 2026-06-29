<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Igrejas extends Component
{
    use WithPagination;

    // Propriedades para o formulário do modal
    public $nome, $regiao_id, $bloco_id, $igreja_id;
    public $isOpen = false;

    // Propriedade para a exclusão
    public $confirmDeleteId = null;

    // Propriedade para a pesquisa "live" (padronizada como 'search')
    public $search = '';

    // Garante que o estado da busca seja mantido na URL
    protected $queryString = ['search' => ['except' => '']];

    /**
     * Lifecycle hook que reseta a paginação sempre que a busca é alterada.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Renderiza a view com os dados filtrados e paginados.
     */
    public function render()
    {         Log::info('Renderizando componente Igrejas', ['search' => $this->search]);

        $igrejas = Igreja::with(['regiao', 'bloco'])
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    // Adiciona a busca nos nomes das tabelas relacionadas
                    ->orWhereHas('regiao', function ($subQuery) {
                        $subQuery->where('nome', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('bloco', function ($subQuery) {
                        $subQuery->where('nome', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate(20);

        return view('livewire.universal.igrejas', [
            'results' => $igrejas, // A view espera a variável $results
            'regiaos' => Regiao::all(),
            'blocos' => Bloco::all(),
        ]);
    }

    // --- Métodos para o CRUD (Create, Read, Update, Delete) ---

    public function create()
    {
        Log::info('Create method called');
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        Log::info('CloseModal method called');
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->nome = '';
        $this->regiao_id = null;
        $this->bloco_id = null;
        $this->igreja_id = null;
    }

    public function store()
    {
        Log::info('Store method called', ['igreja_id' => $this->igreja_id, 'nome' => $this->nome]);
        $this->validate([
            'nome' => 'required|string|min:3|max:250',
            'regiao_id' => 'required|exists:regiaos,id',
            'bloco_id' => 'required|exists:blocos,id',
        ]);

        Igreja::updateOrCreate(['id' => $this->igreja_id], [
            'nome' => $this->nome,
            'regiao_id' => $this->regiao_id,
            'bloco_id' => $this->bloco_id,
        ]);

        session()->flash('message', $this->igreja_id ? 'Igreja atualizada com sucesso.' : 'Igreja criada com sucesso.');
        $this->closeModal();
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        $igreja = Igreja::findOrFail($id);
        $this->igreja_id = $id;
        $this->nome = $igreja->nome;
        $this->regiao_id = $igreja->regiao_id;
        $this->bloco_id = $igreja->bloco_id;
        $this->isOpen = true;
    }

    public function confirmDelete($id)
    {
        Log::info('ConfirmDelete method called', ['id' => $id]);
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        Log::info('Delete method called', ['confirmDeleteId' => $this->confirmDeleteId]);
        if ($this->confirmDeleteId) {
            Igreja::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Igreja deletada com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}
