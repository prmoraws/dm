<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\OficioCredencial as Oficio;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioCredencial extends Component
{
    use WithPagination;

    public $oficio_id, $presidio_id, $nome, $cpf, $rg;
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
        if ($presidioId) {
            $presidio = Presidio::find($presidioId);
            $this->diretor_nome = $presidio->diretor ?? '';
        } else {
            $this->diretor_nome = '';
        }
    }

    public function render()
    {
        $query = Oficio::with('presidio')
            ->when($this->searchTerm, fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%')
                ->orWhereHas('presidio', fn($pq) => $pq->where('nome', 'like', '%' . $this->searchTerm . '%')));

        return view('livewire.unp.oficios.oficio-credencial', [
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
        // Nota: A regra de validação do CPF foi corrigida para remover a concatenação indevida.
        $validatedData = $this->validate([
            'presidio_id' => 'required|exists:presidios,id',
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'rg' => 'nullable|string|max:20',
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $validatedData);
        session()->flash('message', $this->oficio_id ? 'Ofício atualizado!' : 'Ofício criado!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->oficio_id = $id;
        $this->fill($oficio);
        $this->updatedPresidioId($this->presidio_id);
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');
        $oficio->numero_oficio = 'Ofício Nº ' . (200 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->created_at)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR. DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
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
            session()->flash('message', 'Ofício deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}