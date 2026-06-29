<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\OficioCop as Oficio;
use App\Models\Unp\Oficios\Convidado;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OficioCop extends Component
{
    use WithPagination;
    public $oficio_id, $data_oficio, $presidio_id, $evento, $unidade_id, $dia_hora_evento;
    public $convidados = [];
    public $diretor_nome = '', $presidioOptions = [], $convidadoOptions = [];
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $selectedOficio;
    public $searchTerm = '';

    public function mount()
    {
        $this->presidioOptions = Presidio::orderBy('nome')->pluck('nome', 'id')->toArray();
        $this->convidadoOptions = Convidado::orderBy('nome')->get()->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->all();
    }

    private function autoSelectCop()
    {
        $cop = Presidio::where('nome', 'Centro de Observação Penal')->first();
        if ($cop) {
            $this->presidio_id = $cop->id;
            $this->diretor_nome = $cop->diretor;
        }
    }

    public function render()
    {
        $query = Oficio::with(['presidio', 'unidade'])
            ->when($this->searchTerm, function ($query) {
                $query->where('evento', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('unidade', fn($q) => $q->where('nome', 'like', '%' . $this->searchTerm . '%'));
            });
        return view('livewire.unp.oficios.oficio-cop', ['results' => $query->latest()->paginate(10)]);
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
        $this->autoSelectCop();
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
    public function search()
    {
        $this->resetPage();
    }

    public function store()
    {
        $data = $this->validate([
            'data_oficio' => 'required|date',
            'presidio_id' => 'required|exists:presidios,id',
            'evento' => 'required|string|max:255',
            'unidade_id' => 'required|exists:presidios,id',
            'dia_hora_evento' => 'required|date',
            'convidados' => 'required|array|min:1',
        ]);
        Oficio::updateOrCreate(['id' => $this->oficio_id], $data);
        session()->flash('message', 'Ofício COP salvo com sucesso!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $oficio = Oficio::findOrFail($id);
        $this->oficio_id = $id;
        $this->fill($oficio);
        $this->data_oficio = Carbon::parse($oficio->data_oficio)->toDateString();
        $this->dia_hora_evento = Carbon::parse($oficio->dia_hora_evento)->format('Y-m-d\TH:i');
        $this->diretor_nome = $oficio->presidio->diretor ?? '';
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'unidade'])->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8');
        $oficio->numero_oficio = 'Ofício Nº ' . (300 + $oficio->id) . '/2026_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR DO ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ACESSO DE CARROS E OFICIAIS DA IGREJA';
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = 'Com os cumprimentos de estilo, a Universal nos Presídios - UNP, vem, através de seu Coordenador Geral que a este subscreve, requerer autorização de acesso no complexo penitenciário, aos membros para adentrar no complexo, <b>' . $eventoData->translatedFormat('l, \d\i\a d \d\e F \d\e Y') . ', a partir das ' . $eventoData->format('H:i\h') . '</b>, onde será realizado <b>' . $oficio->evento . '</b> na unidade ' . $oficio->unidade->nome . '.';
        $oficio->lista_convidados = Convidado::whereIn('id', $oficio->convidados)->get();
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