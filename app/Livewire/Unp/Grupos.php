<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Grupo;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Grupos extends Component
{
    use WithPagination;

    // Propriedades para o formulário do modal
    public $nome, $descricao, $grupo_id;
    public $isOpen = false;

    // Propriedades para a exclusão
    public $confirmDeleteId = null;

    // Propriedade para a pesquisa (substitui o antigo 'searchTerm')
    public $search = '';

    // Adiciona a propriedade à query string da URL para que a busca seja "linkável"
    protected $queryString = ['search' => ['except' => '']];

    /**
     * Este é um "lifecycle hook" do Livewire.
     * Ele é chamado automaticamente sempre que a propriedade 'search' é atualizada.
     * Essencial para que a paginação funcione corretamente com a busca "live".
     */
    public function updatedSearch()
    {
        // Reseta a paginação para a primeira página ao digitar na busca
        $this->resetPage();
    }

    /**
     * O método render é responsável por buscar os dados e exibir a view.
     */
    public function render()
    {
        Log::info('Renderizando componente Grupos', ['search' => $this->search]);

        $grupos = Grupo::query()
            ->when($this->search, function ($query) {
                // Filtra por nome OU descrição
                $query->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('descricao', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Você pode ajustar a quantidade de itens por página aqui

        return view('livewire.unp.grupos', compact('grupos'));
    }

    /**
     * A função search() agora é chamada apenas se o usuário explicitamente
     * pressionar Enter ou clicar no botão, mas a busca principal já ocorre via `wire:model.live`.
     * Sua única responsabilidade é garantir que a paginação seja resetada.
     */
    public function search()
    {
        $this->resetPage();
    }

    // --- O restante dos seus métodos para o CRUD (create, store, edit, delete, etc.) ---
    // --- permanecem exatamente como estavam. Não há necessidade de alterá-los. ---

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
        $this->descricao = '';
        $this->grupo_id = null;
    }

    public function store()
    {
        Log::info('Store method called', ['grupo_id' => $this->grupo_id, 'nome' => $this->nome]);
        $this->validate([
            'nome' => 'required|string|min:3|max:250',
            'descricao' => 'required|string|min:3|max:6000',
        ]);

        Grupo::updateOrCreate(['id' => $this->grupo_id], [
            'nome' => $this->nome,
            'descricao' => $this->descricao
        ]);

        session()->flash(
            'message',
            $this->grupo_id ? 'Grupo atualizado com sucesso.' : 'Grupo criado com sucesso.'
        );

        $this->closeModal();
    }

    public function edit($id)
    {
        Log::info('Edit method called', ['id' => $id]);
        $grupo = Grupo::findOrFail($id);
        $this->grupo_id = $id;
        $this->nome = $grupo->nome;
        $this->descricao = $grupo->descricao;
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
            Grupo::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Grupo deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}
