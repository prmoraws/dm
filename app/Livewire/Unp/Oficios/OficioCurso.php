<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\OficioCurso as Oficio;
use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioCurso extends Component
{
    use WithPagination;

    public $oficio_id, $data_oficio, $presidio_id, $curso_id, $material;
    public $instrutor_nome = '', $diretor_nome = '';
    public $presidioOptions = [], $cursoOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedOficio;

    // CORREÇÃO: Propriedade de busca que estava faltando
    public $searchTerm = '';

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedPresidioId($presidioId)
    {
        $this->reset(['curso_id', 'instrutor_nome']);
        $this->diretor_nome = $presidioId ? Presidio::find($presidioId)->diretor : '';
        $this->cursoOptions = $presidioId ? Curso::where('presidio_id', $presidioId)->orderBy('nome')->pluck('nome', 'id')->toArray() : [];
    }

    public function updatedCursoId($cursoId)
    {
        $curso = Curso::with('instrutor')->find($cursoId);
        $this->instrutor_nome = $curso ? ($curso->instrutor->nome ?? 'Instrutor não definido') : '';
    }

    public function render()
    {
        // CORREÇÃO: Lógica de busca implementada
        $query = Oficio::with(['presidio', 'curso'])
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('curso', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'))
                    ->orWhereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.oficios.oficio-curso', [
            'results' => $query->latest()->paginate(10),
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
        $data = $this->validate([
            'data_oficio' => 'required|date',
            'presidio_id' => 'required|exists:presidios,id',
            'curso_id' => 'required|exists:cursos,id',
            'material' => 'nullable|string',
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $data);
        session()->flash('message', 'Ofício de Curso salvo com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->oficio_id = $id;
        $this->fill($oficio);
        $this->updatedPresidioId($this->presidio_id);
        $this->curso_id = $oficio->curso_id;
        $this->updatedCursoId($this->curso_id);
        $this->isOpen = true;
    }

    public function view($id)
    {
        $this->selectedOficio = $this->prepareOficioData($id);
        $this->isViewOpen = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            Oficio::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Ofício deletado.');
            $this->confirmDeleteId = null;
        }
    }

    // Adicione este método completo na sua classe OficioCurso
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'curso.instrutor'])->findOrFail($id);
        setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');

        $oficio->numero_oficio = 'Ofício Nº ' . (600 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->created_at)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SENHOR DIRETOR DO ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: SOLICITAÇÃO DE INÍCIO DE CURSO';

        $inicio = Carbon::parse($oficio->curso->inicio);
        $fim = Carbon::parse($oficio->curso->fim);
        $duracao = (int) max(1, ceil($inicio->diffInMonths($fim->addDay())));

        $oficio->paragrafo_principal = "Com os cumprimentos de estilo, a Universal nos Presídios - UNP, vem, através de seu Pastor-Coordenador Geral que a este subscreve, requerer a autorização do início do curso de " . $oficio->curso->nome . ", com carga horaria de " . $oficio->curso->carga . " o curso será realizado: " . $oficio->curso->dia_hora . " nesta unidade prisional e deve conter em sala de aula " . $oficio->curso->reeducandos . " reeducados, com o início dia " . $inicio->translatedFormat('d \d\e F \d\e Y') . " até o dia " . $fim->translatedFormat('d \d\e F \d\e Y') . ", totalizando " . str_pad($duracao, 2, '0', STR_PAD_LEFT) . " meses de duração, terminando o curso iremos fazer a formatura no dia " . Carbon::parse($oficio->curso->formatura)->translatedFormat('d \d\e F \d\e Y') . ".";

        return $oficio;
    }
}
