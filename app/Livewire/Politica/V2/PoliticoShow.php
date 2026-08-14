<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\EspelhoInteligenteService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Perfil')]
class PoliticoShow extends Component
{
    public Politico $politico;

    #[Url(as: 'candidatura', except: null)]
    public ?int $candidaturaId = null;

    public function mount(Politico $politico): void
    {
        $this->politico = $politico;

        if ($this->candidaturaId === null) {
            $this->candidaturaId = Candidatura::query()
                ->where('politico_id', $politico->id)
                ->join('politica_eleicoes', 'politica_eleicoes.id', '=', 'politica_candidaturas.eleicao_id')
                ->orderByDesc('politica_eleicoes.ano')
                ->orderByDesc('politica_eleicoes.turno')
                ->orderByDesc('politica_candidaturas.id')
                ->value('politica_candidaturas.id');
        }
    }

    public function render(EspelhoInteligenteService $espelho)
    {
        $this->politico->load([
            'acompanhamento',
            'mandatos' => fn ($query) => $query->with(['cargo', 'partido'])->orderByDesc('data_inicio'),
            'candidaturas' => fn ($query) => $query
                ->with(['eleicao', 'cargo', 'partido'])
                ->orderByDesc('eleicao_id'),
        ]);

        $candidaturaSelecionada = null;
        $desempenho = null;

        if ($this->candidaturaId !== null) {
            $candidaturaSelecionada = Candidatura::query()
                ->with(['eleicao', 'cargo', 'partido', 'politico'])
                ->where('politico_id', $this->politico->id)
                ->find($this->candidaturaId);

            if ($candidaturaSelecionada) {
                $desempenho = $espelho->desempenhoCandidatura($candidaturaSelecionada, 20);
            }
        }

        return view('livewire.politica.v2.politico-show', [
            'candidaturaSelecionada' => $candidaturaSelecionada,
            'desempenho' => $desempenho,
        ]);
    }
}
