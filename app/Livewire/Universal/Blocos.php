<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Blocos extends Component
{
    use WithPagination;

    // Propriedades para o formulário do modal
    public $nome, $bloco_id;
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
        Log::info('Renderizando componente Blocos', ['search' => $this->search]);

        // Lógica de busca simplificada com o método when()
        $blocos = Bloco::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%');
            })
            ->paginate(20);

        // A variável é passada como 'blocos' para a view
        return view('livewire.universal.blocos', [
            'results' => $blocos
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
    }

    public function store()
    {
        Log::info('Store method called', ['bloco_id' => $this->bloco_id, 'nome' => $this->nome]);
        $this->validate([
            'nome' => 'required|string|min:3|max:250',
        ]);

        Bloco::updateOrCreate(['id' => $this->bloco_id], [
            'nome' => $this->nome,
        ]);

        session()->flash('message', $this->bloco_id ? 'Bloco atualizado com sucesso.' : 'Bloco criado com sucesso.');
        $this->closeModal();
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        $bloco = Bloco::findOrFail($id);
        $this->bloco_id = $id;
        $this->nome = $bloco->nome;
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
            Bloco::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Bloco deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }

    public function teste($id)
    {
        Log::info('Teste method called', ['id' => $id]);
        session()->flash('message', 'Teste clicado para o bloco ID: ' . $id);
    }
}
