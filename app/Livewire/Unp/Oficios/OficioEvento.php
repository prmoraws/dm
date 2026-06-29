<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\OficioEvento as Oficio;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioEvento extends Component
{
    use WithPagination;

    public $oficio_id, $data_oficio, $presidio_id, $assunto, $dia_hora_evento, $descricao, $materiais;
    public $diretor_nome = '';
    public $presidioOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';
    public $selectedOficio;

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
    }

    public function updatedPresidioId($presidioId)
    {
        $this->diretor_nome = $presidioId ? (Presidio::find($presidioId)->diretor ?? '') : '';
    }

    public function render()
    {
        $query = Oficio::with('presidio')
            ->when($this->searchTerm, fn($q) => $q->where('assunto', 'like', '%' . $this->searchTerm . '%')
                ->orWhereHas('presidio', fn($pq) => $pq->where('nome', 'like', '%' . $this->searchTerm . '%')));

        return view('livewire.unp.oficios.oficio-evento', [
            'results' => $query->latest()->paginate(10),
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
            'data_oficio' => 'required|date',
            'presidio_id' => 'required|exists:presidios,id',
            'assunto' => 'required|string|max:255',
            'dia_hora_evento' => 'required|date',
            'descricao' => 'required|string',
            'materiais' => 'nullable|string',
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $validatedData);
        session()->flash('message', 'Ofício de Evento salvo com sucesso!');
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
        // setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');
        $oficio->numero_oficio = 'Ofício Nº ' . (150 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ' . mb_strtoupper($oficio->assunto, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = $oficio->assunto . ', ' . $eventoData->translatedFormat('l, \d\i\a d \d\e F \d\e Y, \à\s H:i\h');

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
            session()->flash('message', 'Ofício deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}