<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Formatura;
use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Formaturas extends Component
{
    use WithPagination, WithFileUploads;

    public $formatura_id, $presidio_id, $curso_id, $instrutor_id, $inicio, $fim, $formatura, $lista, $conteudo, $oficio;
    
    // Propriedades de controle de estado
    public $isPresidioSelected = false;
    public $isCursoSelected = false;
    public $instrutor_nome = '';

    // Opções para os seletores
    public $presidioOptions = [];
    public $cursoOptions = [];

    // Propriedades da view
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '', $sortField = 'inicio', $sortDirection = 'desc';
    public $selectedFormatura;

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedPresidioId($presidioId)
    {
        $this->reset(['curso_id', 'instrutor_id', 'instrutor_nome', 'inicio', 'fim', 'formatura', 'lista', 'conteudo', 'oficio']);
        $this->isCursoSelected = false;
        
        if ($presidioId) {
            $this->cursoOptions = Curso::where('presidio_id', $presidioId)
                                       ->where('status', 'CERTIFICANDO')
                                       ->orderBy('nome')
                                       ->pluck('nome', 'id')
                                       ->toArray();
            $this->isPresidioSelected = true;
        } else {
            $this->cursoOptions = [];
            $this->isPresidioSelected = false;
        }
    }

    public function updatedCursoId($cursoId)
    {
        if ($cursoId) {
            $curso = Curso::with('instrutor')->find($cursoId);
            if ($curso) {
                $this->instrutor_id = $curso->instrutor_id;
                $this->instrutor_nome = $curso->instrutor->nome ?? 'Instrutor não encontrado';
                $this->inicio = Carbon::parse($curso->inicio)->toDateString();
                $this->fim = Carbon::parse($curso->fim)->toDateString();
                $this->isCursoSelected = true;
            }
        } else {
            $this->reset(['instrutor_id', 'instrutor_nome', 'inicio', 'fim']);
            $this->isCursoSelected = false;
        }
    }

    public function render()
    {
        $query = Formatura::with(['presidio', 'curso', 'instrutor'])
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'))
                      ->orWhereHas('curso', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.unp.formaturas', [
            'results' => $query->paginate(10),
        ]);
    }

    private function resetInputFields()
    {
        $this->resetExcept('presidioOptions');
        $this->isPresidioSelected = false;
        $this->isCursoSelected = false;
        $this->resetErrorBag();
    }
    
    // CORREÇÃO: Adicionados os métodos de controle que estavam faltando.
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; $this->resetInputFields(); }
    public function openViewModal() { $this->isViewOpen = true; }
    public function closeViewModal() { $this->isViewOpen = false; $this->selectedFormatura = null; }
    public function confirmDelete($id) { $this->confirmDeleteId = $id; }
    public function search() { $this->resetPage(); }

    public function store()
    {
        $validatedData = $this->validate([
            'presidio_id' => 'required|exists:presidios,id',
            'curso_id' => 'required|exists:cursos,id',
            'instrutor_id' => 'required|exists:instrutores,id',
            'inicio' => 'required|date',
            'fim' => 'required|date',
            'formatura' => 'nullable|date|after_or_equal:fim',
            'lista' => 'nullable|file|mimes:pdf|max:10240',
            'conteudo' => 'nullable|file|mimes:pdf|max:10240',
            'oficio' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            foreach (['lista', 'conteudo', 'oficio'] as $field) {
                if ($this->$field) {
                    if ($this->formatura_id) {
                        $existing = Formatura::find($this->formatura_id);
                        if ($existing && $existing->$field) Storage::disk('public_disk')->delete($existing->$field);
                    }
                    $validatedData[$field] = $this->$field->store('formaturas', 'public_disk');
                }
            }

            Formatura::updateOrCreate(['id' => $this->formatura_id], $validatedData);

            session()->flash('message', $this->formatura_id ? 'Formatura atualizada com sucesso!' : 'Formatura criada com sucesso!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar a formatura: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $formatura = Formatura::with(['presidio', 'curso', 'instrutor'])->findOrFail($id);
        $this->resetInputFields();
        
        $this->formatura_id = $id;
        $this->presidio_id = $formatura->presidio_id;
        $this->curso_id = $formatura->curso_id;
        
        $this->updatedPresidioId($this->presidio_id);
        $this->updatedCursoId($this->curso_id);

        $this->formatura = $formatura->formatura ? Carbon::parse($formatura->formatura)->toDateString() : null;
        $this->selectedFormatura = $formatura;

        $this->isPresidioSelected = true;
        $this->isCursoSelected = true;
        $this->openModal();
    }

    public function view($id)
    {
        try {
            $this->selectedFormatura = Formatura::with(['presidio', 'curso', 'instrutor'])->findOrFail($id);
            $this->openViewModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Não foi possível carregar os dados da formatura: ' . $e->getMessage());
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
        $this->resetPage();
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $formatura = Formatura::find($this->confirmDeleteId);
            if ($formatura) {
                foreach (['lista', 'conteudo', 'oficio'] as $field) {
                    if ($formatura->$field) Storage::disk('public_disk')->delete($formatura->$field);
                }
                $formatura->delete();
                session()->flash('message', 'Formatura deletada com sucesso!');
            }
            $this->confirmDeleteId = null;
        }
    }
}