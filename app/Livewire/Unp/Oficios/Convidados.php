<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\Convidado;
use Livewire\Component;
use Livewire\WithPagination;

class Convidados extends Component
{
    use WithPagination;

    // Propriedades do modelo
    public $convidado_id, $nome, $cpf, $rg, $classe;

    // Propriedades da UI
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';
    public $selectedConvidado;
    public $classeOptions = ['esposas' => 'Esposas', 'convidados' => 'Convidados', 'comunicação' => 'Comunicação', 'organização' => 'Organização'];

    public function render()
    {
        $query = Convidado::query()
            ->when($this->searchTerm, function ($q) {
                $q->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('cpf', 'like', '%' . $this->searchTerm . '%');
            });

        return view('livewire.unp.oficios.convidados', [
            'results' => $query->latest()->paginate(10)
        ]);
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }
    public function openModal()
    {
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function openViewModal()
    {
        $this->isViewOpen = true;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedConvidado = null;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'nome' => 'required|string|min:3',
            'cpf' => 'required|string|unique:convidados,cpf,' . $this->convidado_id,
            'rg' => 'nullable|string',
            'classe' => 'required|in:esposas,convidados,comunicação,organização',
        ]);

        Convidado::updateOrCreate(['id' => $this->convidado_id], $validatedData);
        session()->flash('message', $this->convidado_id ? 'Convidado atualizado!' : 'Convidado criado!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $convidado = Convidado::findOrFail($id);
        $this->convidado_id = $id;
        $this->fill($convidado);
        $this->openModal();
    }

    public function view($id)
    {
        $this->selectedConvidado = Convidado::findOrFail($id);
        $this->openViewModal();
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Convidado::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Convidado deletado com sucesso!');
            $this->confirmDeleteId = null;
        }
    }
}
