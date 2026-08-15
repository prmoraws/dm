<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\EspelhoInteligenteService as EspelhoService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Política — Espelho Inteligente')]
class EspelhoInteligente extends Component
{
    use WithPagination;
    public Cidade $cidade;

    #[Url(as: 'eleicao', except: null)]
    public ?int $eleicaoId = null;

    #[Url(as: 'cargo', except: null)]
    public ?int $cargoId = null;

    #[Url(as: 'escopo', except: 'auto')]
    public string $escopoCandidatos = 'auto';

    public function mount(Cidade $cidade): void
    {
        $this->cidade = $cidade;

        if ($this->eleicaoId === null) {
            $this->eleicaoId = Eleicao::query()
                ->whereIn('id', $this->eleicaoIdsDisponiveis())
                ->orderByDesc('ano')
                ->orderByDesc('turno')
                ->value('id');
        }

        $this->normalizarCargo();
        $this->normalizarEscopo();
    }

    public function updatedEleicaoId(): void
    {
        $this->resetPage('rankingPage');
        $this->cargoId = null;
        $this->normalizarCargo();
        $this->normalizarEscopo(true);
    }

    public function updatedCargoId(): void
    {
        $this->resetPage('rankingPage');
        $this->normalizarEscopo(true);
    }

    public function updatedEscopoCandidatos(): void
    {
        $this->resetPage('rankingPage');

        if (! in_array($this->escopoCandidatos, ['auto', 'republicanos', 'todos'], true)) {
            $this->escopoCandidatos = 'auto';
        }

        $this->normalizarEscopo();
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

    private function eleicaoIdsDisponiveis(): array
    {
        return Candidatura::query()
            ->where(function ($query): void {
                // Cargos estaduais/federais têm cidade_id nulo; cargos municipais pertencem à cidade.
                $query->whereNull('cidade_id')
                    ->orWhere('cidade_id', $this->cidade->id);
            })
            ->distinct()
            ->pluck('eleicao_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function idsCargosDisponiveis(): array
    {
        if ($this->eleicaoId === null) {
            return [];
        }

        return Candidatura::query()
            ->where('eleicao_id', $this->eleicaoId)
            ->where(function ($query): void {
                $query->whereNull('cidade_id')
                    ->orWhere('cidade_id', $this->cidade->id);
            })
            ->distinct()
            ->pluck('cargo_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function cargoSelecionado(): ?Cargo
    {
        return $this->cargoId ? Cargo::query()->find($this->cargoId) : null;
    }

    private function cargoUsaFiltroRepublicanos(): bool
    {
        return in_array(
            $this->cargoSelecionado()?->nome,
            config('politica.espelho.cargos_partido_prioritario', []),
            true
        );
    }

    private function normalizarEscopo(bool $forcarPadrao = false): void
    {
        if ($forcarPadrao) {
            $this->escopoCandidatos = 'auto';
        }

        if (! $this->cargoUsaFiltroRepublicanos()) {
            // Presidente e governador sempre exibem todos; prefeito segue o filtro partidário configurado.
            $this->escopoCandidatos = 'todos';
            return;
        }

        if (! in_array($this->escopoCandidatos, ['auto', 'republicanos', 'todos'], true)) {
            $this->escopoCandidatos = 'auto';
        }
    }

    private function partidoFiltro(): ?string
    {
        if (! $this->cargoUsaFiltroRepublicanos()) {
            return null;
        }

        return $this->escopoCandidatos === 'todos'
            ? null
            : mb_strtoupper((string) config('politica.espelho.partido_prioritario', 'REPUBLICANOS'), 'UTF-8');
    }

    public function render(EspelhoService $service)
    {
        $eleicoes = Eleicao::query()
            ->whereIn('id', $this->eleicaoIdsDisponiveis())
            ->orderByDesc('ano')
            ->orderByDesc('turno')
            ->get();

        $cargoIds = $this->idsCargosDisponiveis();
        $cargos = Cargo::query()
            ->whereIn('id', $cargoIds)
            ->orderBy('ordem')
            ->get();

        $partidoFiltro = $this->partidoFiltro();
        $panorama = $service->panoramaCidade(
            $this->cidade,
            $this->eleicaoId,
            $this->cargoId,
            $partidoFiltro
        );

        // O ranking parte das candidaturas, e não dos resultados. Assim candidatos com 0 voto
        // no município também aparecem, como deve ocorrer em um espelho completo do cargo/partido.
        $rankingQuery = Candidatura::query()
            ->select('politica_candidaturas.*')
            ->selectRaw('COALESCE(rm.votos, 0) as votos_no_municipio')
            ->selectRaw('rm.percentual as percentual_no_municipio')
            ->selectRaw('rm.posicao as posicao_no_municipio')
            ->leftJoin('politica_resultados_municipais as rm', function ($join): void {
                $join->on('rm.candidatura_id', '=', 'politica_candidaturas.id')
                    ->where('rm.cidade_id', '=', $this->cidade->id);

                if ($this->eleicaoId !== null) {
                    $join->where('rm.eleicao_id', '=', $this->eleicaoId);
                }
            })
            ->with(['politico', 'partido', 'cargo']);

        if ($this->eleicaoId !== null) {
            $rankingQuery->where('politica_candidaturas.eleicao_id', $this->eleicaoId);
        }

        if ($this->cargoId !== null) {
            $rankingQuery->where('politica_candidaturas.cargo_id', $this->cargoId);
        }

        $cargoSelecionado = $this->cargoSelecionado();
        if (in_array($cargoSelecionado?->nome, ['Prefeito', 'Vereador'], true)) {
            // Candidaturas municipais pertencem ao próprio município.
            $rankingQuery->where('politica_candidaturas.cidade_id', $this->cidade->id);
        }

        if ($partidoFiltro !== null) {
            $legacyIdsDoPartido = collect(config('politica.migracao_v1.partidos_legacy', []))
                ->filter(fn ($sigla) => mb_strtoupper((string) $sigla, 'UTF-8') === $partidoFiltro)
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->all();

            $rankingQuery->where(function ($q) use ($partidoFiltro, $legacyIdsDoPartido): void {
                $q->whereHas('partido', fn ($partido) => $partido->where('sigla', $partidoFiltro));

                if ($legacyIdsDoPartido !== []) {
                    // Compatibilidade enquanto a correção do legado ainda não foi reprocessada no banco.
                    $q->orWhereIn('politica_candidaturas.legacy_candidato_id', $legacyIdsDoPartido);
                }
            });
        }

        $ranking = $rankingQuery
            ->orderByDesc('votos_no_municipio')
            ->orderBy('politica_candidaturas.id')
            ->paginate(
                max(10, min((int) config('politica.espelho.ranking_por_pagina', 25), 100)),
                ['*'],
                'rankingPage'
            );

        return view('livewire.politica.v2.espelho-inteligente', [
            'eleicoes' => $eleicoes,
            'cargos' => $cargos,
            'panorama' => $panorama,
            'cargoSelecionado' => $this->cargoSelecionado(),
            'usaFiltroRepublicanos' => $this->cargoUsaFiltroRepublicanos(),
            'partidoFiltro' => $partidoFiltro,
            'ranking' => $ranking,
        ]);
    }
}
