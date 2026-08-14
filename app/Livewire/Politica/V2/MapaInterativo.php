<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\ResultadoMunicipal;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Mapa Eleitoral')]
class MapaInterativo extends Component
{
    #[Url(as: 'eleicao', except: null)]
    public ?int $eleicaoId = null;

    #[Url(as: 'cargo', except: null)]
    public ?int $cargoId = null;

    #[Url(as: 'candidatura', except: null)]
    public ?int $candidaturaId = null;

    public function mount(): void
    {
        if ($this->eleicaoId === null) {
            $this->eleicaoId = ResultadoMunicipal::query()
                ->join('politica_eleicoes', 'politica_eleicoes.id', '=', 'politica_resultados_municipais.eleicao_id')
                ->orderByDesc('politica_eleicoes.ano')
                ->orderByDesc('politica_eleicoes.turno')
                ->value('politica_resultados_municipais.eleicao_id');
        }

        $this->normalizarFiltros();
    }

    public function updatedEleicaoId(): void
    {
        $this->cargoId = null;
        $this->candidaturaId = null;
        $this->normalizarFiltros();
    }

    public function updatedCargoId(): void
    {
        $this->candidaturaId = null;
        $this->normalizarCandidatura();
    }

    public function updatedCandidaturaId(): void
    {
        $this->normalizarCandidatura();
    }

    private function normalizarFiltros(): void
    {
        if ($this->eleicaoId === null) {
            $this->cargoId = null;
            $this->candidaturaId = null;
            return;
        }

        $cargoIds = $this->cargoIdsDisponiveis();
        if ($this->cargoId === null || ! in_array($this->cargoId, $cargoIds, true)) {
            $this->cargoId = $cargoIds[0] ?? null;
        }

        $this->normalizarCandidatura();
    }

    private function normalizarCandidatura(): void
    {
        if ($this->eleicaoId === null || $this->cargoId === null) {
            $this->candidaturaId = null;
            return;
        }

        if ($this->candidaturaId !== null
            && ! $this->candidaturasDisponiveisQuery()->whereKey($this->candidaturaId)->exists()) {
            $this->candidaturaId = null;
        }
    }

    private function cargoIdsDisponiveis(): array
    {
        if ($this->eleicaoId === null) {
            return [];
        }

        return ResultadoMunicipal::query()
            ->where('politica_resultados_municipais.eleicao_id', $this->eleicaoId)
            ->join('politica_candidaturas', 'politica_candidaturas.id', '=', 'politica_resultados_municipais.candidatura_id')
            ->distinct()
            ->pluck('politica_candidaturas.cargo_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function candidaturasDisponiveisQuery()
    {
        return Candidatura::query()
            ->select('politica_candidaturas.*')
            ->with(['politico:id,nome_publico,slug', 'partido:id,sigla'])
            ->where('politica_candidaturas.eleicao_id', $this->eleicaoId)
            ->where('politica_candidaturas.cargo_id', $this->cargoId)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('politica_resultados_municipais')
                    ->whereColumn('politica_resultados_municipais.candidatura_id', 'politica_candidaturas.id');
            })
            ->orderByDesc('votos_total')
            ->orderBy('nome_urna');
    }

    private function mapaData(): array
    {
        $cidades = Cidade::query()
            ->whereNotNull('ibge_code')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('nome')
            ->get(['id', 'nome', 'latitude', 'longitude', 'populacao']);

        $resultados = collect();
        if ($this->candidaturaId !== null) {
            $resultados = ResultadoMunicipal::query()
                ->where('candidatura_id', $this->candidaturaId)
                ->get(['cidade_id', 'votos', 'percentual', 'posicao'])
                ->keyBy('cidade_id');
        }

        $maxVotos = max(1, (int) $resultados->max('votos'));

        return $cidades->map(function (Cidade $cidade) use ($resultados, $maxVotos) {
            $resultado = $resultados->get($cidade->id);
            $votos = $resultado ? (int) $resultado->votos : 0;

            return [
                'id' => $cidade->id,
                'nome' => $cidade->nome,
                'lat' => (float) $cidade->latitude,
                'lng' => (float) $cidade->longitude,
                'populacao' => $cidade->populacao !== null ? (int) $cidade->populacao : null,
                'votos' => $votos,
                'percentual' => $resultado?->percentual !== null ? (float) $resultado->percentual : null,
                'posicao' => $resultado?->posicao,
                'intensidade' => $votos > 0 ? round($votos / $maxVotos, 5) : 0,
                'url' => route('politica.espelho.inteligente', $cidade),
            ];
        })->values()->all();
    }

    public function render()
    {
        $eleicaoIds = ResultadoMunicipal::query()->distinct()->pluck('eleicao_id');
        $eleicoes = Eleicao::query()
            ->whereIn('id', $eleicaoIds)
            ->orderByDesc('ano')
            ->orderByDesc('turno')
            ->get();

        $cargos = Cargo::query()
            ->whereIn('id', $this->cargoIdsDisponiveis())
            ->orderBy('ordem')
            ->get();

        $candidaturas = collect();
        $totalCandidaturas = 0;
        if ($this->eleicaoId && $this->cargoId) {
            $queryCandidaturas = $this->candidaturasDisponiveisQuery();
            $totalCandidaturas = (clone $queryCandidaturas)->count();
            $candidaturas = $queryCandidaturas
                ->limit(max(25, min((int) config('politica.mapa.max_candidaturas_seletor', 150), 500)))
                ->get();
        }

        $candidaturaSelecionada = null;
        if ($this->candidaturaId) {
            $candidaturaSelecionada = Candidatura::query()
                ->with(['politico:id,nome_publico,slug', 'partido:id,sigla'])
                ->find($this->candidaturaId);

            if ($candidaturaSelecionada && ! $candidaturas->contains('id', $candidaturaSelecionada->id)) {
                $candidaturas->prepend($candidaturaSelecionada);
            }
        }

        $mapaData = $this->mapaData();
        $municipiosOficiais = Cidade::query()->whereNotNull('ibge_code')->count();
        $municipiosSemCoordenadas = Cidade::query()
            ->whereNotNull('ibge_code')
            ->where(fn ($q) => $q->whereNull('latitude')->orWhereNull('longitude'))
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        return view('livewire.politica.v2.mapa-interativo', [
            'eleicoes' => $eleicoes,
            'cargos' => $cargos,
            'candidaturas' => $candidaturas,
            'candidaturaSelecionada' => $candidaturaSelecionada,
            'totalCandidaturas' => $totalCandidaturas,
            'mapaData' => $mapaData,
            'municipiosOficiais' => $municipiosOficiais,
            'municipiosMapeados' => count($mapaData),
            'municipiosSemCoordenadas' => $municipiosSemCoordenadas,
        ]);
    }
}
