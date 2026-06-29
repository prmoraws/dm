<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Cursos extends Component
{
    use WithPagination;

    // Propriedades do componente
    public $curso_id, $nome, $presidio_id, $dia_hora, $instrutor_id, $carga, $reeducandos, $inicio, $fim, $formatura, $status;
    public $presidioOptions = [];
    public $instrutorOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $selectedCurso;

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
        $this->instrutorOptions = Instrutor::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function render()
    {
        $query = Curso::with(['presidio', 'instrutor'])
            ->when($this->searchTerm, function ($query) {
                $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.cursos', [
            'results' => $query->orderByRaw("
        CASE
            WHEN status = 'PENDENTE' THEN 1
            WHEN status = 'CERTIFICANDO' THEN 2
            WHEN status = 'CURSANDO' THEN 3
            ELSE 4
        END, status ASC, nome ASC
    ")->paginate(10),
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
        $this->selectedCurso = null;
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
    public function search()
    {
        $this->resetPage();
    }

    private function resetInputFields()
    {
        $this->curso_id = null;
        $this->nome = '';
        $this->presidio_id = '';
        $this->dia_hora = '';
        $this->instrutor_id = '';
        $this->carga = '';
        $this->reeducandos = 0;
        $this->inicio = '';
        $this->fim = '';
        $this->formatura = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        // CORREÇÃO: Removida a linha de validação para a propriedade 'professor' que não existe mais.
        $validatedData = $this->validate([
            'nome' => 'required|string|max:255',
            'presidio_id' => 'required|exists:presidios,id',
            'instrutor_id' => 'required|exists:instrutores,id',
            'dia_hora' => 'required|string|max:255',
            'carga' => 'required|string|max:255',
            'reeducandos' => 'required|integer|min:0',
            'inicio' => 'required|date',
            'fim' => 'required|date|after_or_equal:inicio',
            'formatura' => 'nullable|date|after_or_equal:fim',
            'status' => 'nullable|string|max:50',
        ]);

        try {
            Curso::updateOrCreate(['id' => $this->curso_id], $validatedData);

            session()->flash('message', $this->curso_id ? 'Curso atualizado com sucesso!' : 'Curso criado com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar o curso: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        $this->curso_id = $id;
        $this->fill($curso);

        $this->inicio = Carbon::parse($curso->inicio)->toDateString();
        $this->fim = Carbon::parse($curso->fim)->toDateString();
        $this->formatura = $curso->formatura ? Carbon::parse($curso->formatura)->toDateString() : null;

        $this->openModal();
    }

    public function view($id)
    {
        $this->selectedCurso = Curso::with(['presidio', 'instrutor'])->findOrFail($id);
        $this->openViewModal();
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Curso::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Curso deletado com sucesso!');
            $this->confirmDeleteId = null;
        }
    }
}
