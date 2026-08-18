<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoInteligencia;
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

    #[Url(as: 'favorito', except: null)]
    public ?int $candidaturaRelatorioId = null;

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
        $this->normalizarCandidaturaRelatorio();
    }

    public function updatedEleicaoId(): void
    {
        $this->resetPage('rankingPage');
        $this->cargoId = null;
        $this->normalizarCargo();
        $this->normalizarEscopo(true);
        $this->normalizarCandidaturaRelatorio();
    }

    public function updatedCargoId(): void
    {
        $this->resetPage('rankingPage');
        $this->normalizarEscopo(true);
        $this->normalizarCandidaturaRelatorio();
    }

    public function updatedEscopoCandidatos(): void
    {
        $this->resetPage('rankingPage');

        if (! in_array($this->escopoCandidatos, ['auto', 'republicanos', 'todos'], true)) {
            $this->escopoCandidatos = 'auto';
        }

        $this->normalizarEscopo();
        $this->normalizarCandidaturaRelatorio();
    }

    public function updatedCandidaturaRelatorioId(): void
    {
        $this->normalizarCandidaturaRelatorio();
    }

    public function marcarFavoritoRelatorio(): void
    {
        $this->normalizarCandidaturaRelatorio();

        if (! $this->candidaturaRelatorioId) {
            session()->flash('politica_espelho_relatorio_erro', 'Selecione um candidato válido no recorte atual.');
            return;
        }

        $candidatura = Candidatura::query()->find($this->candidaturaRelatorioId);
        if (! $candidatura) {
            session()->flash('politica_espelho_relatorio_erro', 'Candidatura não localizada.');
            return;
        }

        EspelhoInteligencia::query()
            ->where('cidade_id', $this->cidade->id)
            ->where('classificacao', 'favorito')
            ->where('candidatura_id', '<>', $candidatura->id)
            ->whereHas('candidatura', fn ($q) => $q
                ->where('eleicao_id', $candidatura->eleicao_id)
                ->where('cargo_id', $candidatura->cargo_id))
            ->update(['classificacao' => 'acompanhamento', 'prioridade' => 2]);

        EspelhoInteligencia::query()->updateOrCreate(
            [
                'cidade_id' => $this->cidade->id,
                'contexto_chave' => 'eleicao:'.$candidatura->eleicao_id.':candidatura:'.$candidatura->id,
            ],
            [
                'eleicao_id' => $candidatura->eleicao_id,
                'candidatura_id' => $candidatura->id,
                'responsavel_user_id' => auth()->id(),
                'classificacao' => 'favorito',
                'prioridade' => 1,
                'revisado_em' => now(),
            ]
        );

        session()->flash('politica_espelho_relatorio_ok', 'Candidato marcado como favorito deste espelho.');
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

    private function candidaturasRecorteQuery()
    {
        $query = Candidatura::query()
            ->select('politica_candidaturas.*')
            ->selectRaw('COALESCE(rm.votos, 0) as votos_no_municipio')
            ->selectRaw('rm.percentual as percentual_no_municipio')
            ->selectRaw('rm.posicao as posicao_no_municipio')
            ->selectRaw('rm.id as resultado_municipal_id')
            ->leftJoin('politica_resultados_municipais as rm', function ($join): void {
                $join->on('rm.candidatura_id', '=', 'politica_candidaturas.id')
                    ->where('rm.cidade_id', '=', $this->cidade->id);

                if ($this->eleicaoId !== null) {
                    $join->where('rm.eleicao_id', '=', $this->eleicaoId);
                }
            })
            ->with(['politico', 'partido', 'cargo']);

        if ($this->eleicaoId === null || $this->cargoId === null) {
            return $query->whereRaw('1 = 0');
        }

        if ($this->eleicaoId !== null) {
            $query->where('politica_candidaturas.eleicao_id', $this->eleicaoId);
        }

        if ($this->cargoId !== null) {
            $query->where('politica_candidaturas.cargo_id', $this->cargoId);
        }

        $cargoSelecionado = $this->cargoSelecionado();
        if (in_array($cargoSelecionado?->nome, ['Prefeito', 'Vereador'], true)) {
            $query->where('politica_candidaturas.cidade_id', $this->cidade->id);
        }

        $partidoFiltro = $this->partidoFiltro();
        if ($partidoFiltro !== null) {
            $legacyIdsDoPartido = collect(config('politica.migracao_v1.partidos_legacy', []))
                ->filter(fn ($sigla) => mb_strtoupper((string) $sigla, 'UTF-8') === $partidoFiltro)
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->all();

            $query->where(function ($q) use ($partidoFiltro, $legacyIdsDoPartido): void {
                $q->whereHas('partido', fn ($partido) => $partido->where('sigla', $partidoFiltro));

                if ($legacyIdsDoPartido !== []) {
                    $q->orWhereIn('politica_candidaturas.legacy_candidato_id', $legacyIdsDoPartido);
                }
            });
        }

        return $query;
    }

    private function normalizarCandidaturaRelatorio(): void
    {
        $ids = (clone $this->candidaturasRecorteQuery())
            ->get()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($this->candidaturaRelatorioId !== null && $ids->contains((int) $this->candidaturaRelatorioId)) {
            $this->candidaturaRelatorioId = (int) $this->candidaturaRelatorioId;
            return;
        }

        $this->candidaturaRelatorioId = null;

        if ($ids->isEmpty()) {
            return;
        }

        $favorito = EspelhoInteligencia::query()
            ->where('cidade_id', $this->cidade->id)
            ->whereIn('candidatura_id', $ids->all())
            ->whereIn('classificacao', ['favorito', 'acompanhamento'])
            ->orderByRaw("CASE WHEN classificacao = 'favorito' THEN 0 ELSE 1 END")
            ->orderBy('prioridade')
            ->value('candidatura_id');

        if ($favorito) {
            $this->candidaturaRelatorioId = (int) $favorito;
        }
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

        $rankingQuery = $this->candidaturasRecorteQuery();
        $ranking = (clone $rankingQuery)
            ->orderByDesc('votos_no_municipio')
            ->orderBy('politica_candidaturas.id')
            ->paginate(
                max(10, min((int) config('politica.espelho.ranking_por_pagina', 25), 100)),
                ['*'],
                'rankingPage'
            );

        $candidatosRelatorio = (clone $rankingQuery)
            ->orderByDesc('votos_no_municipio')
            ->orderBy('politica_candidaturas.id')
            ->limit(500)
            ->get();

        $candidaturaRelatorio = $this->candidaturaRelatorioId
            ? $candidatosRelatorio->firstWhere('id', $this->candidaturaRelatorioId)
            : null;

        $favoritoRelatorio = $this->candidaturaRelatorioId
            ? EspelhoInteligencia::query()
                ->where('cidade_id', $this->cidade->id)
                ->where('candidatura_id', $this->candidaturaRelatorioId)
                ->first()
            : null;

        return view('livewire.politica.v2.espelho-inteligente', [
            'eleicoes' => $eleicoes,
            'cargos' => $cargos,
            'panorama' => $panorama,
            'cargoSelecionado' => $this->cargoSelecionado(),
            'usaFiltroRepublicanos' => $this->cargoUsaFiltroRepublicanos(),
            'partidoFiltro' => $partidoFiltro,
            'ranking' => $ranking,
            'candidatosRelatorio' => $candidatosRelatorio,
            'candidaturaRelatorio' => $candidaturaRelatorio,
            'favoritoRelatorio' => $favoritoRelatorio,
        ]);
    }
}
