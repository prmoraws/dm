<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Presidio;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;

class Presidios extends Component
{
    use WithPagination;

    public $nome, $diretor, $contato_diretor, $adjunto, $contato_adjunto, $laborativa, $contato_laborativa, $visita, $interno;
    public $presidio_id, $searchTerm = '', $sortField = 'nome', $sortDirection = 'asc';
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedPresidio, $errorMessage = '';

    public function render()
    {
        $query = Presidio::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('diretor', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('adjunto', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $presidios = $query->paginate(10); // Paginação ajustada para 10

        return view('livewire.unp.presidios', [
            'presidios' => $presidios,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }
    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
    public function openViewModal()
    {
        $this->isViewOpen = true;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedPresidio = null;
    }

    private function resetInputFields()
    {
        $this->presidio_id = null;
        $this->nome = '';
        $this->diretor = '';
        $this->contato_diretor = '';
        $this->adjunto = '';
        $this->contato_adjunto = '';
        $this->laborativa = '';
        $this->contato_laborativa = '';
        $this->visita = '';
        $this->interno = '';
        $this->resetErrorBag();
    }

    public function save()
    {
        $validatedData = $this->validate([
            'nome' => 'required|string|max:255',
            'diretor' => 'required|string|max:255',
            'contato_diretor' => 'required|string|max:255',
            'adjunto' => 'nullable|string|max:255',
            'contato_adjunto' => 'nullable|string|max:255',
            'laborativa' => 'nullable|string|max:255',
            'contato_laborativa' => 'nullable|string|max:255',
            'visita' => 'nullable|string',
            'interno' => 'nullable|string',
        ]);

        try {
            Presidio::updateOrCreate(['id' => $this->presidio_id], $validatedData);

            session()->flash('message', $this->presidio_id ? 'Presídio atualizado com sucesso!' : 'Presídio cadastrado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar presídio: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $presidio = Presidio::findOrFail($id);
        $this->presidio_id = $id;
        $this->fill($presidio);
        $this->openModal();
    }

    public function view($id)
    {
        $this->selectedPresidio = Presidio::findOrFail($id);
        $this->openViewModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Presidio::findOrFail($this->confirmDeleteId)->delete();
            session()->flash('message', 'Presídio excluído com sucesso!');
            $this->confirmDeleteId = null;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function search()
    {
        $this->resetPage();
    }
}
