<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\Oficios\Reeducando;
use Livewire\Component;
use Livewire\WithPagination;

class Reeducandos extends Component
{
    use WithPagination;

    public $reeducando_id, $nome, $documento, $curso_id;
    public $cursoOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';
    public $selectedReeducando;

    public function mount()
    {
        $this->cursoOptions = Curso::where('status', 'CERTIFICANDO')
            ->orderBy('nome')
            ->pluck('nome', 'id')->toArray();
    }

    public function render()
    {
        $query = Reeducando::with('curso')
            ->when($this->searchTerm, function ($q) {
                $q->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('documento', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('curso', fn($c) => $c->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.oficios.reeducandos', [
            'results' => $query->latest()->paginate(15),
        ]);
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
    }
    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        Reeducando::updateOrCreate(['id' => $this->reeducando_id], $validatedData);
        session()->flash('message', 'Reeducando salvo com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $reeducando = Reeducando::findOrFail($id);
        $this->reeducando_id = $id;
        $this->fill($reeducando);
        $this->isOpen = true;
    }

    public function view($id)
    {
        $this->selectedReeducando = Reeducando::with('curso')->findOrFail($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Reeducando::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Reeducando deletado com sucesso!');
            $this->confirmDeleteId = null;
        }
    }
}
