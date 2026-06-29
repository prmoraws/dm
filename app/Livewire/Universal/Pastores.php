<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Pastor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Pastores extends Component
{
    use WithPagination;

    // Propriedades do Componente
    public $pastor_id, $sede, $pastor, $telefone, $esposa, $tel_epos;
    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $selectedPastor;

    public $search = '';
    public $errorMessage = '';

    protected $queryString = ['search' => ['except' => '']];

    protected $rules = [
        'sede' => 'required|string|max:250',
        'pastor' => 'required|string|max:250',
        'telefone' => 'required|string|max:20',
        'esposa' => 'required|string|max:250',
        'tel_epos' => 'required|string|max:20',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        Log::info('Renderizando componente Pastores', ['search' => $this->search]);

        $pastores = Pastor::query()
            ->when($this->search, function ($query) {
                $query->where('sede', 'like', '%' . $this->search . '%')
                    ->orWhere('pastor', 'like', '%' . $this->search . '%')
                    ->orWhere('esposa', 'like', '%' . $this->search . '%');
            })
            ->latest('id')
            ->paginate(10);

        return view('livewire.universal.pastores', [
            'results' => $pastores,
        ]);
    }

    // --- Métodos de Controle dos Modais ---

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedPastor = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'pastor_id',
            'sede',
            'pastor',
            'telefone',
            'esposa',
            'tel_epos',
            'isOpen',
            'isViewOpen',
            'confirmDeleteId',
            'errorMessage',
            'selectedPastor'
        ]);
    }

    // --- Ações de CRUD ---

    public function store()
    {
        $validatedData = $this->validate();

        try {
            Pastor::updateOrCreate(['id' => $this->pastor_id], $validatedData);
            session()->flash('message', $this->pastor_id ? 'Pastor atualizado com sucesso.' : 'Pastor criado com sucesso.');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->errorMessage = 'Erro ao salvar o registro.';
            Log::error('Erro ao salvar pastor: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $pastor = Pastor::findOrFail($id);
            $this->pastor_id = $id;
            $this->sede = $pastor->sede;
            $this->pastor = $pastor->pastor;
            $this->telefone = $pastor->telefone;
            $this->esposa = $pastor->esposa;
            $this->tel_epos = $pastor->tel_epos;
            $this->isOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar dados para edição.');
            Log::error('Erro ao editar pastor: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        try {
            $this->selectedPastor = Pastor::findOrFail($id);
            $this->isViewOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar os detalhes do pastor.');
            Log::error('Erro ao visualizar pastor: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            try {
                Pastor::findOrFail($this->confirmDeleteId)->delete();
                session()->flash('message', 'Pastor deletado com sucesso.');
            } catch (\Exception $e) {
                session()->flash('error', 'Erro ao deletar o pastor.');
                Log::error('Erro ao deletar pastor: ' . $e->getMessage());
            } finally {
                $this->confirmDeleteId = null;
            }
        }
    }
}
