<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\EspelhoInteligenteService as EspelhoService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Espelho Inteligente')]
class EspelhoInteligente extends Component
{
    public Cidade $cidade;

    #[Url(as: 'eleicao', except: null)]
    public ?int $eleicaoId = null;

    #[Url(as: 'cargo', except: null)]
    public ?int $cargoId = null;

    public function mount(Cidade $cidade): void
    {
        $this->cidade = $cidade;

        if ($this->eleicaoId === null) {
            $this->eleicaoId = ResultadoMunicipal::query()
                ->where('cidade_id', $cidade->id)
                ->join('politica_eleicoes', 'politica_eleicoes.id', '=', 'politica_resultados_municipais.eleicao_id')
                ->orderByDesc('politica_eleicoes.ano')
                ->orderByDesc('politica_eleicoes.turno')
                ->value('politica_resultados_municipais.eleicao_id');
        }

        $this->normalizarCargo();
    }

    public function updatedEleicaoId(): void
    {
        $this->cargoId = null;
        $this->normalizarCargo();
    }

    private function normalizarCargo(): void
    {
        if ($this->eleicaoId === null) {
            $this->cargoId = null;
            return;
        }

        $cargosDisponiveis = $this->idsCargosDisponiveis();

        if ($this->cargoId === null || ! in_array($this->cargoId, $cargosDisponiveis, true)) {
            $this->cargoId = $cargosDisponiveis[0] ?? null;
        }
    }

    private function idsCargosDisponiveis(): array
    {
        if ($this->eleicaoId === null) {
            return [];
        }

        return ResultadoMunicipal::query()
            ->where('politica_resultados_municipais.cidade_id', $this->cidade->id)
            ->where('politica_resultados_municipais.eleicao_id', $this->eleicaoId)
            ->join('politica_candidaturas', 'politica_candidaturas.id', '=', 'politica_resultados_municipais.candidatura_id')
            ->distinct()
            ->orderBy('politica_candidaturas.cargo_id')
            ->pluck('politica_candidaturas.cargo_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function render(EspelhoService $service)
    {
        $eleicaoIds = ResultadoMunicipal::query()
            ->where('cidade_id', $this->cidade->id)
            ->distinct()
            ->pluck('eleicao_id');

        $eleicoes = Eleicao::query()
            ->whereIn('id', $eleicaoIds)
            ->orderByDesc('ano')
            ->orderByDesc('turno')
            ->get();

        $cargoIds = $this->idsCargosDisponiveis();
        $cargos = Cargo::query()
            ->whereIn('id', $cargoIds)
            ->orderBy('ordem')
            ->get();

        $panorama = $service->panoramaCidade($this->cidade, $this->eleicaoId, $this->cargoId);

        return view('livewire.politica.v2.espelho-inteligente', [
            'eleicoes' => $eleicoes,
            'cargos' => $cargos,
            'panorama' => $panorama,
        ]);
    }
}
