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

    public string $anexoPresidio = '';
    public string $anexoOficio = '';
    #[\Livewire\Attributes\Locked]
    public int $anexoSelecaoVersao = 0;
    #[\Livewire\Attributes\Locked]
    public string $anexoTituloAutomatico = '';

    public function opcoesOrigemAnexo(): array
    {
        return [
            'presidios' => \App\Models\Unp\Presidio::orderBy('nome')->get(['id', 'nome']),
            'oficios' => $this->anexoPresidio
                ? app(\App\Services\Unp\ConvidadosEmMassa::class)->oficios((int) $this->anexoPresidio)
                : [],
        ];
    }

    private function limpaConvidadosOrigem(): void
    {
        $this->esposas_selected = [];
        $this->convidados_selected = [];
        $this->comunicacao_selected = [];
        $this->organizacao_selected = [];
        if ($this->titulo === $this->anexoTituloAutomatico) {
            $this->titulo = '';
        }
        $this->anexoTituloAutomatico = '';
        $this->anexoSelecaoVersao++;
    }

    public function updatedAnexoPresidio(): void
    {
        if ($this->anexo_id) {
            return;
        }
        $this->anexoOficio = '';
        $this->limpaConvidadosOrigem();
        $this->resetErrorBag();
    }

    public function updatedAnexoOficio(): void
    {
        if ($this->anexo_id) {
            return;
        }
        $this->limpaConvidadosOrigem();
        $this->resetErrorBag();
        if ($this->anexoOficio === '') {
            return;
        }
        $this->validate([
            'anexoPresidio' => 'required|integer|exists:presidios,id',
            'anexoOficio' => 'required|string|max:80',
        ]);
        $service = app(\App\Services\Unp\ConvidadosEmMassa::class);
        try {
            $convidados = $service->convidados((int) $this->anexoPresidio, $this->anexoOficio);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'anexoOficio' => 'Selecione um ofício deste presídio.',
            ]);
        }
        $this->mount();
        foreach ([
            'esposas' => 'esposas_selected',
            'convidados' => 'convidados_selected',
            'comunicação' => 'comunicacao_selected',
            'organização' => 'organizacao_selected',
        ] as $classe => $campo) {
            $this->{$campo} = $convidados->where('classe', $classe)->pluck('id')->map(fn ($id) => (int) $id)->unique()->values()->all();
        }
        $opcao = collect($service->oficios((int) $this->anexoPresidio))->firstWhere('value', $this->anexoOficio);
        $presidio = \App\Models\Unp\Presidio::findOrFail((int) $this->anexoPresidio);
        $automatico = mb_substr('Lista de convidados — ' . $presidio->nome . ' — ' . $opcao['text'], 0, 255);
        if (!$this->titulo) {
            $this->titulo = $automatico;
            $this->anexoTituloAutomatico = $automatico;
        }
    }

    private function validaOrigemAnexo(): void
    {
        if ($this->anexo_id || ($this->anexoPresidio === '' && $this->anexoOficio === '')) {
            return;
        }
        $this->validate([
            'anexoPresidio' => 'required|integer|exists:presidios,id',
            'anexoOficio' => 'required|string|max:80',
        ], ['anexoOficio.required' => 'Selecione o ofício para carregar a lista.']);
        try {
            app(\App\Services\Unp\ConvidadosEmMassa::class)->convidados((int) $this->anexoPresidio, $this->anexoOficio);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'anexoOficio' => 'O ofício foi excluído ou não pertence ao presídio selecionado.',
            ]);
        }
    }


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
        $this->validaOrigemAnexo();
        $validatedData = $this->validate([
            'titulo' => 'required|string|min:5|max:255',
            'esposas_selected' => 'array',
            'esposas_selected.*' => 'integer|distinct|exists:convidados,id',
            'convidados_selected' => 'array',
            'convidados_selected.*' => 'integer|distinct|exists:convidados,id',
            'comunicacao_selected' => 'array',
            'comunicacao_selected.*' => 'integer|distinct|exists:convidados,id',
            'organizacao_selected' => 'array',
            'organizacao_selected.*' => 'integer|distinct|exists:convidados,id',
            'data' => 'required|date',
        ]);

        unset($validatedData['esposas_selected'], $validatedData['convidados_selected'], $validatedData['comunicacao_selected'], $validatedData['organizacao_selected']);

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
        $this->anexoPresidio = '';
        $this->anexoOficio = '';
        $this->anexoTituloAutomatico = '';
        $this->anexoSelecaoVersao++;
        $this->resetErrorBag();
        $this->mount();
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