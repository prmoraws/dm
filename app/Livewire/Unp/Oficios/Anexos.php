<?php

namespace App\Livewire\Unp\Oficios;

use App\Models\Unp\Oficios\Convidado;
use App\Models\Unp\Oficios\OficioAnexo;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Anexos extends Component
{
    use WithPagination;

    // Propriedades do modelo
    public $anexo_id, $titulo, $data;
    public $esposas_selected = [], $convidados_selected = [], $comunicacao_selected = [], $organizacao_selected = [];

    // Listas de convidados para os selects
    public $listasConvidados = [];

    // Propriedades da UI
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null;
    public $searchTerm = '';
    public $selectedAnexo;

    public function mount()
    {
        $convidados = Convidado::orderBy('nome')->get();
        $this->listasConvidados = [
            'esposas' => $convidados->where('classe', 'esposas')->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->values()->all(),
            'convidados' => $convidados->where('classe', 'convidados')->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->values()->all(),
            'comunicacao' => $convidados->where('classe', 'comunicação')->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->values()->all(),
            'organizacao' => $convidados->where('classe', 'organização')->map(fn($c) => ['value' => $c->id, 'text' => $c->nome])->values()->all(),
        ];
    }

    public function render()
    {
        return view('livewire.unp.oficios.anexos', [
            'results' => OficioAnexo::where('titulo', 'like', '%' . $this->searchTerm . '%')->latest()->paginate(10)
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
        $this->data = now()->toDateString();
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

    public function store()
    {
        $validatedData = $this->validate([
            'titulo' => 'required|string|min:5',
            'data' => 'required|date',
        ]);

        $validatedData['esposas'] = $this->esposas_selected;
        $validatedData['convidados'] = $this->convidados_selected;
        $validatedData['comunicacao'] = $this->comunicacao_selected;
        $validatedData['organizacao'] = $this->organizacao_selected;

        OficioAnexo::updateOrCreate(['id' => $this->anexo_id], $validatedData);
        session()->flash('message', $this->anexo_id ? 'Anexo atualizado!' : 'Anexo criado!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $anexo = OficioAnexo::findOrFail($id);
        $this->anexo_id = $id;
        $this->titulo = $anexo->titulo;
        $this->data = Carbon::parse($anexo->data)->toDateString();
        $this->esposas_selected = $anexo->esposas ?? [];
        $this->convidados_selected = $anexo->convidados ?? [];
        $this->comunicacao_selected = $anexo->comunicacao ?? [];
        $this->organizacao_selected = $anexo->organizacao ?? [];
        $this->isOpen = true;
    }

    // MÉTODO DE AJUDA ADICIONADO
    private function prepareAnexoData($id)
    {
        $anexo = OficioAnexo::findOrFail($id);
        $convidadosIds = array_merge(
            $anexo->esposas ?? [],
            $anexo->convidados ?? [],
            $anexo->comunicacao ?? [],
            $anexo->organizacao ?? []
        );
        $convidadosData = Convidado::whereIn('id', $convidadosIds)->get()->keyBy('id');

        $anexo->listas_formatadas = [
            'PASTORES/ESPOSAS' => collect($anexo->esposas ?? [])->map(fn($id) => $convidadosData->get($id)),
            'CONVIDADOS' => collect($anexo->convidados ?? [])->map(fn($id) => $convidadosData->get($id)),
            'VOLUNTÁRIOS DA COMUNICAÇÃO' => collect($anexo->comunicacao ?? [])->map(fn($id) => $convidadosData->get($id)),
            'VOLUNTÁRIOS DA ORGANIZAÇÃO' => collect($anexo->organizacao ?? [])->map(fn($id) => $convidadosData->get($id)),
        ];

        return $anexo;
    }

    // MÉTODO VIEW ATUALIZADO
    public function view($id)
    {
        $this->selectedAnexo = $this->prepareAnexoData($id);
        $this->isViewOpen = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            OficioAnexo::find($this->confirmDeleteId)->delete();
            session()->flash('message', 'Anexo deletado com sucesso.');
            $this->confirmDeleteId = null;
        }
    }
}