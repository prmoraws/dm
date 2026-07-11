<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Presidio;
use App\Models\Universal\Bloco; // Importado para buscar os blocos no formulário
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Presidios extends Component
{
    use WithPagination;

    public $nome, $diretor, $contato_diretor, $adjunto, $contato_adjunto, $laborativa, $contato_laborativa, $visita, $interno;
    public $presidio_id, $searchTerm = '', $sortField = 'nome', $sortDirection = 'asc';
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedPresidio, $errorMessage = '';

    // Nova propriedade para armazenar os blocos selecionados no formulário
    public $blocos_selecionados = [];

    public function render()
    {
        // Eager loading de 'blocos' para evitar o problema de N+1 consultas na listagem
        $query = Presidio::with('blocos')
            ->when($this->searchTerm, function ($query) {
                $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('diretor', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('adjunto', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $presidios = $query->paginate(10);

        // Buscamos todos os blocos disponíveis para popular o select/checkbox do formulário
        $todosBlocos = Bloco::orderBy('nome', 'asc')->get();

        return view('livewire.unp.presidios', [
            'presidios' => $presidios,
            'todosBlocos' => $todosBlocos,
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
        $this->blocos_selecionados = []; // Reseta a seleção de blocos
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
            $presidio = Presidio::updateOrCreate(['id' => $this->presidio_id], $validatedData);

            // Sincroniza os blocos na tabela intermediária (pivot)
            $presidio->blocos()->sync($this->blocos_selecionados);

            session()->flash('message', $this->presidio_id ? 'Presídio atualizado com sucesso!' : 'Presídio cadastrado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar presídio: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $presidio = Presidio::with('blocos')->findOrFail($id);
        $this->presidio_id = $id;
        $this->fill($presidio);
        
        // Carrega os IDs dos blocos já vinculados a este presídio
        $this->blocos_selecionados = $presidio->blocos->pluck('id')->map(fn($id) => (string)$id)->toArray();
        
        $this->openModal();
    }

    public function view($id)
    {
        $this->selectedPresidio = Presidio::with('blocos')->findOrFail($id);
        $this->openViewModal();
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $presidio = Presidio::findOrFail($this->confirmDeleteId);
            // Remove os vínculos na pivot antes de deletar o presídio
            $presidio->blocos()->detach();
            $presidio->delete();
            
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