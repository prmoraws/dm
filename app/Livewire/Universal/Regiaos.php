<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\Regiao;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Regiaos extends Component
{
    use WithPagination;

    // Propriedades para o formulário do modal
    public $nome, $bloco_id, $regiao_id;
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
    {
        Log::info('Renderizando componente Regiões', ['search' => $this->search]);

        $regioes = Regiao::with('bloco')
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                    // Adiciona a busca no nome do bloco relacionado
                    ->orWhereHas('bloco', function ($subQuery) {
                        $subQuery->where('nome', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate(20);

        return view('livewire.universal.regiaos', [
            'results' => $regioes, // A view espera a variável $results
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
        $this->bloco_id = null;
        $this->regiao_id = null;
    }

    public function store()
    {
        Log::info('Store method called', ['regiao_id' => $this->regiao_id, 'bloco_id' => $this->bloco_id, 'nome' => $this->nome]);
        $this->validate([
            'nome' => 'required|string|min:3|max:250',
            'bloco_id' => 'required|exists:blocos,id',
        ]);

        Regiao::updateOrCreate(['id' => $this->regiao_id], [
            'nome' => $this->nome,
            'bloco_id' => $this->bloco_id,
        ]);

        session()->flash('message', $this->regiao_id ? 'Região atualizada com sucesso.' : 'Região criada com sucesso.');
        $this->closeModal();
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        $regiao = Regiao::findOrFail($id);
        $this->regiao_id = $id;
        $this->nome = $regiao->nome;
        $this->bloco_id = $regiao->bloco_id;
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
            Regiao::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Região deletada com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}
