<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\InformacaoCurso as Informacao;
use Livewire\Component;
use Livewire\WithPagination;

class InformacaoCurso extends Component
{
    use WithPagination;

    public $informacao_id, $nome, $informacao;
    public $isOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';

    // CORREÇÃO: Propriedades para o modal de visualização
    public $isViewOpen = false;
    public $selectedInfo;

    public function render()
    {
        $query = Informacao::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('informacao', 'like', '%' . $this->searchTerm . '%');
            });

        return view('livewire.unp.oficios.informacao-curso', [
            'results' => $query->latest()->paginate(15)
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'nome' => 'required|string|max:255|unique:informacao_cursos,nome,' . $this->informacao_id,
            'informacao' => 'required|string',
        ]);

        Informacao::updateOrCreate(['id' => $this->informacao_id], [
            'nome' => $this->nome,
            'informacao' => $this->informacao,
        ]);

        session()->flash('message', 'Informação salva com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $info = Informacao::findOrFail($id);
        $this->informacao_id = $id;
        $this->nome = $info->nome;
        $this->informacao = $info->informacao;
        $this->openModal();
    }

    // CORREÇÃO: Método para abrir o modal de visualização
    public function view($id)
    {
        $this->selectedInfo = Informacao::findOrFail($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Informacao::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Informação deletada com sucesso!');
            $this->confirmDeleteId = null;
        }
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->resetErrorBag();
    }
    public function openModal()
    {
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedInfo = null;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
}
