<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\Oficios\DadosCurso as OficioDados;
use App\Models\Unp\Oficios\InformacaoCurso;
use App\Models\Unp\Presidio;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class DadosCurso extends Component
{
    use WithPagination;

    // Campos do formulário e do modelo
    public $dados_id, $nome, $presidio_id, $curso_id, $instrutor_id, $informacao_curso_id, $inicio, $fim, $carga;

    // Propriedades de apoio para o formulário
    public $diretor_nome = '', $instrutor_nome = '', $dados_informacao = '';
    public $presidioOptions = [], $cursoOptions = [];

    // Propriedades da UI
    public $isOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';
    public $isViewOpen = false;
    public $selectedDados;

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedPresidioId($presidioId)
    {
        $this->reset(['curso_id', 'instrutor_id', 'informacao_curso_id', 'diretor_nome', 'instrutor_nome', 'inicio', 'fim', 'carga', 'dados_informacao']);
        if ($presidioId) {
            $this->diretor_nome = Presidio::find($presidioId)->diretor ?? '';
            $this->cursoOptions = Curso::where('presidio_id', $presidioId)
                ->where('status', 'CERTIFICANDO')
                ->orderBy('nome')->pluck('nome', 'id')->toArray();
        } else {
            $this->cursoOptions = [];
        }
    }

    public function updatedCursoId($cursoId)
    {
        $this->reset(['instrutor_id', 'informacao_curso_id', 'instrutor_nome', 'inicio', 'fim', 'carga', 'dados_informacao']);
        if ($cursoId) {
            $curso = Curso::with('instrutor')->find($cursoId);
            if ($curso) {
                $this->instrutor_id = $curso->instrutor_id;
                $this->instrutor_nome = $curso->instrutor->nome ?? 'N/A';
                $this->inicio = $curso->inicio ? Carbon::parse($curso->inicio)->toDateString() : '';
                $this->fim = $curso->fim ? Carbon::parse($curso->fim)->toDateString() : '';
                $this->carga = $curso->carga;

                $informacao = InformacaoCurso::where('nome', $curso->nome)->first();
                if ($informacao) {
                    $this->informacao_curso_id = $informacao->id;
                    $this->dados_informacao = $informacao->informacao;
                }
            }
        }
    }

    public function render()
    {
        // CORRIGIDO: Adicionada lógica de busca
        $query = OficioDados::with(['presidio', 'curso'])
            ->when($this->searchTerm, function ($q) {
                $q->where('nome', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('curso', fn($cq) => $cq->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.oficios.dados-curso', [
            'results' => $query->latest()->paginate(10)
        ]);
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
        $this->resetErrorBag();
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
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedDados = null;
    }

    public function store()
    {
        $data = $this->validate([
            'nome' => 'required|string|max:255',
            'presidio_id' => 'required|exists:presidios,id',
            'curso_id' => 'required|exists:cursos,id',
            'instrutor_id' => 'required|exists:instrutores,id',
            'informacao_curso_id' => 'required|exists:informacao_cursos,id',
            'inicio' => 'required|date',
            'fim' => 'required|date',
            'carga' => 'required|string',
        ]);
        OficioDados::updateOrCreate(['id' => $this->dados_id], $data);
        session()->flash('message', 'Dados de curso salvos com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $dados = OficioDados::findOrFail($id);
        $this->dados_id = $id;
        $this->fill($dados);
        $this->updatedPresidioId($this->presidio_id);
        $this->curso_id = $dados->curso_id;
        $this->updatedCursoId($this->curso_id);
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareDadosData($id)
    {
        return OficioDados::with(['presidio', 'curso.instrutor', 'informacaoCurso'])->findOrFail($id);
    }

    // MÉTODO VIEW ATUALIZADO
    public function view($id)
    {
        $this->selectedDados = $this->prepareDadosData($id);
        $this->isViewOpen = true;
    }


    public function delete()
    {
        if ($this->confirmDeleteId) {
            OficioDados::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Registro deletado.');
            $this->confirmDeleteId = null;
        }
    }
}