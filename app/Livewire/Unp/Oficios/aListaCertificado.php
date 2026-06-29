<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\Oficios\ListaCertificado as Lista;
use App\Models\Unp\Oficios\Reeducando;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCertificado extends Component
{
    use WithPagination;

    public $lista_id, $nome, $curso_id;
    public $reeducandos = [];
    public $unidade_nome = '', $professor_nome = '', $inicio = '', $fim = '', $carga = '';
    public $cursoOptions = [], $reeducandoOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedLista;
    public $searchTerm = '';

    public function mount()
    {
        $this->cursoOptions = Curso::where('status', 'CERTIFICANDO')->orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedCursoId($cursoId)
    {
        $this->reset(['unidade_nome', 'professor_nome', 'inicio', 'fim', 'carga', 'reeducandos', 'reeducandoOptions']);
        if ($cursoId) {
            $curso = Curso::with(['presidio', 'instrutor'])->find($cursoId);
            if ($curso) {
                $this->unidade_nome = $curso->presidio->nome ?? 'N/A';
                $this->professor_nome = $curso->instrutor->nome ?? 'N/A';
                $this->inicio = $curso->inicio ? Carbon::parse($curso->inicio)->format('d/m/Y') : '';
                $this->fim = $curso->fim ? Carbon::parse($curso->fim)->format('d/m/Y') : '';
                $this->carga = $curso->carga;

                $this->reeducandoOptions = Reeducando::where('curso_id', $cursoId)
                    ->orderBy('nome')
                    ->get()
                    ->map(fn($r) => ['value' => $r->id, 'text' => $r->nome])
                    ->all();
            }
        }
    }

    public function render()
    {
        $query = Lista::with('curso.presidio')
            ->when($this->searchTerm, function ($query) {
                $query->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('curso.presidio', function ($q) {
                        $q->where('nome', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        return view('livewire.unp.oficios.lista-certificado', ['results' => $query->latest()->paginate(10)]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $data = $this->validate([
            'nome' => 'required|string|max:255',
            'curso_id' => 'required|exists:cursos,id',
            'reeducandos' => 'required|array|min:1',
        ]);
        Lista::updateOrCreate(['id' => $this->lista_id], $data);
        session()->flash('message', 'Lista para Certificados salva com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $lista = Lista::findOrFail($id);
        $this->lista_id = $id;
        $this->fill($lista);
        $this->updatedCursoId($this->curso_id);
        $this->reeducandos = $lista->reeducandos ?? [];
        $this->isOpen = true;
    }

    private function prepareListaData($id)
    {
        $lista = Lista::with(['curso.presidio', 'curso.instrutor'])->findOrFail($id);
        $lista->lista_reeducandos = Reeducando::whereIn('id', $lista->reeducandos ?? [])->orderBy('nome')->get();
        return $lista;
    }

    public function view($id)
    {
        $this->selectedLista = $this->prepareListaData($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Lista::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Registro deletado.');
            $this->confirmDeleteId = null;
        }
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
        $this->resetErrorBag();
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
}