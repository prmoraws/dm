<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\Convidado;
use App\Models\Unp\Oficios\OficioTrabalho as Oficio;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioTrabalho extends Component
{
    use WithPagination;

    public $oficio_id, $data_oficio, $presidio_id, $dia_hora_evento, $materiais;
    public $evangelistas = [];
    public $diretor_nome = '', $presidioOptions = [], $evangelistaOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedOficio;
    public $searchTerm = '';

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
        $this->evangelistaOptions = Convidado::orderBy('nome')->get()->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->all();
    }
    public function updatedPresidioId($id)
    {
        $this->diretor_nome = $id ? Presidio::find($id)->diretor : '';
    }
    public function render()
    {
        $query = Oficio::with('presidio')
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.oficios.oficio-trabalho', ['results' => $query->latest()->paginate(10)]);
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
        $this->data_oficio = now()->toDateString();
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
            'dia_hora_evento' => 'required|date',
            'evangelistas' => 'required|array|min:1',
            'materiais' => 'nullable|string',
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $data);
        session()->flash('message', 'Ofício de Trabalho salvo com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->oficio_id = $id;
        $this->fill($oficio);
        $this->data_oficio = Carbon::parse($oficio->data_oficio)->toDateString();
        $this->dia_hora_evento = Carbon::parse($oficio->dia_hora_evento)->format('Y-m-d\TH:i');
        $this->updatedPresidioId($this->presidio_id);
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8');
        $oficio->numero_oficio = 'Ofício Nº ' . (250 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ACESSO PARA TRABALHO DE EVANGELISMO';
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $oficio->evento_formatado = Carbon::parse($oficio->dia_hora_evento)->translatedFormat('l, \d\i\a d \d\e F, \à\s H:i\h');
        $oficio->lista_evangelistas = Convidado::whereIn('id', $oficio->evangelistas)->get();

        return $oficio;
    }

    // MÉTODO VIEW ATUALIZADO
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
}