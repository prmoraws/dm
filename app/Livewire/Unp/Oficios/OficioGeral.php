<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\OficioGeral as Oficio;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioGeral extends Component
{
    use WithPagination;

    public $oficio_id, $data_oficio, $presidio_id, $assunto, $inicio, $meio, $fim;
    public $diretor_nome = '';
    public $presidioOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedOficio;
    public $searchTerm = ''; // ADICIONADO

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
        // CORRIGIDO: Adicionada lógica de busca
        $query = Oficio::with('presidio')
            ->when($this->searchTerm, function ($query) {
                $query->where('assunto', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('presidio', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });

        return view('livewire.unp.oficios.oficio-geral', [
            'results' => $query->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $data = $this->validate([
            'data_oficio' => 'required|date',
            'presidio_id' => 'required|exists:presidios,id',
            'assunto' => 'required|string|max:255',
            'inicio' => 'required|string',
            'meio' => 'nullable|string',
            'fim' => 'nullable|string', 
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $data);
        session()->flash('message', 'Ofício Geral salvo com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->oficio_id = $id;
        $this->fill($oficio);
        $this->data_oficio = Carbon::parse($oficio->data_oficio)->toDateString();
        $this->updatedPresidioId($this->presidio_id);
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');

        $oficio->numero_oficio = 'Ofício Nº ' . (700 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ' . mb_strtoupper($oficio->assunto, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');

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
            session()->flash('message', 'Ofício deletado com sucesso!'); // Mensagem adicionada
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