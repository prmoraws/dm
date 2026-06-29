<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Cargo;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Cargos extends Component
{
    use WithPagination;

    // Propriedades para o formulário do modal
    public $nome, $cargo_id;
    public $isOpen = false;

    // Propriedade para a exclusão
    public $confirmDeleteId = null;

    // Propriedade para a pesquisa "live"
    public $search = '';

    // Garante que o estado da busca seja mantido na URL
    protected $queryString = ['search' => ['except' => '']];

    /**
     * Lifecycle hook que reseta a paginação sempre que a busca é alterada.
     * Essencial para a busca "live" funcionar corretamente.
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
        Log::info('Renderizando componente Cargos', ['search' => $this->search]);

        // A busca é feita de forma condicional usando o when()
        $cargos = Cargo::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.unp.cargos', compact('cargos'));
    }

    // --- Métodos para o CRUD (Create, Read, Update, Delete) ---
    // Nenhuma alteração necessária aqui. Eles permanecem como estão.

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
        $this->cargo_id = null;
    }

    public function store()
    {
        Log::info('Store method called', ['cargo_id' => $this->cargo_id, 'nome' => $this->nome]);
        $this->validate([
            'nome' => 'required|string|min:3|max:250',
        ]);

        Cargo::updateOrCreate(['id' => $this->cargo_id], [
            'nome' => $this->nome
        ]);

        session()->flash(
            'message',
            $this->cargo_id ? 'Cargo atualizado com sucesso.' : 'Cargo criado com sucesso.'
        );

        $this->closeModal();
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        $cargo = Cargo::findOrFail($id);
        $this->cargo_id = $id;
        $this->nome = $cargo->nome;
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
            Cargo::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Cargo deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}
