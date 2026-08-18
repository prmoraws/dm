<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HistoricoAcompanhadosService
{
    public function __construct(private readonly PoliticaHistoricoOficialService $historico)
    {
    }

    /** @return array<string,mixed> */
    public function resumo(string $politicoSlug = '', string $cargo = '', ?int $ano = null): array
    {
        $politicos = Politico::query()
            ->whereIn('slug', $this->historico->slugs())
            ->with([
                'acompanhamento',
                'candidaturas' => fn ($query) => $query
                    ->with(['eleicao', 'cargo', 'partido'])
                    ->withCount(['resultadosMunicipais', 'resultadosZonas']),
                'mandatos' => fn ($query) => $query
                    ->with(['cargo', 'partido'])
                    ->orderByDesc('ano_inicio')
                    ->orderByDesc('data_inicio'),
            ])
            ->get()
            ->sortBy(fn (Politico $politico) => array_search($politico->slug, $this->historico->slugs(), true))
            ->values();

        $opcoes = $this->opcoes($politicos);

        if ($politicoSlug !== '') {
            $politicos = $politicos->where('slug', $politicoSlug)->values();
        }

        $candidaturasFiltradas = collect();
        foreach ($politicos as $politico) {
            foreach ($politico->candidaturas as $candidatura) {
                if ($cargo !== '' && $candidatura->cargo?->nome !== $cargo) {
                    continue;
                }
                if ($ano !== null && (int) $candidatura->eleicao?->ano !== $ano) {
                    continue;
                }
                $candidaturasFiltradas->push($candidatura);
            }
        }

        $melhoresMunicipios = $this->melhoresMunicipios($candidaturasFiltradas->pluck('id')->all());
        $cards = $politicos->map(fn (Politico $politico) => $this->montarPolitico($politico, $cargo, $ano, $melhoresMunicipios))
            ->filter(fn (array $item) => $item['eleicoes'] !== [] || $item['mandatos'] !== [] || ($cargo === '' && $ano === null))
            ->values();

        $eleicoes = collect($cards)->flatMap(fn (array $item) => $item['eleicoes']);
        $mandatos = collect($cards)->flatMap(fn (array $item) => $item['mandatos']);

        return [
            'cards' => $cards,
            'metricas' => [
                'politicos' => $cards->count(),
                'eleicoes' => $eleicoes->count(),
                'com_resultado' => $eleicoes->where('tem_resultado', true)->count(),
                'eleitos' => $eleicoes->where('eleito', true)->count(),
                'mandatos' => $mandatos->count(),
            ],
            'opcoes' => $opcoes,
            'filtros' => [
                'politico' => $politicoSlug,
                'cargo' => $cargo,
                'ano' => $ano,
            ],
        ];
    }

    /** @param Collection<int,Politico> $politicos */
    private function opcoes(Collection $politicos): array
    {
        $anos = $politicos
            ->flatMap(fn (Politico $politico) => $politico->candidaturas->pluck('eleicao.ano'))
            ->filter()
            ->map(fn ($ano) => (int) $ano)
            ->unique()
            ->sortDesc()
            ->values();

        $cargos = $politicos
            ->flatMap(fn (Politico $politico) => $politico->candidaturas->pluck('cargo.nome')
                ->merge($politico->mandatos->pluck('cargo.nome')))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return [
            'politicos' => $politicos->map(fn (Politico $politico) => [
                'slug' => $politico->slug,
                'nome' => $politico->nome_publico,
            ])->values(),
            'anos' => $anos,
            'cargos' => $cargos,
        ];
    }

    /** @param Collection<int,ResultadoMunicipal> $melhoresMunicipios */
    private function montarPolitico(Politico $politico, string $cargo, ?int $ano, Collection $melhoresMunicipios): array
    {
        $candidaturas = $politico->candidaturas
            ->filter(function (Candidatura $candidatura) use ($cargo, $ano) {
                if ($cargo !== '' && $candidatura->cargo?->nome !== $cargo) {
                    return false;
                }
                if ($ano !== null && (int) $candidatura->eleicao?->ano !== $ano) {
                    return false;
                }

                return true;
            })
            ->sortBy(fn (Candidatura $candidatura) => sprintf('%04d-%02d-%010d', (int) $candidatura->eleicao?->ano, (int) $candidatura->eleicao?->turno, $candidatura->id))
            ->values();

        $anterioresPorCargo = [];
        $eleicoesAsc = [];
        $maiorVotacao = 0;

        foreach ($candidaturas as $candidatura) {
            $temResultado = $this->temResultado($candidatura);
            $votos = $temResultado ? (int) $candidatura->votos_total : null;
            $cargoNome = $candidatura->cargo?->nome ?: 'Candidatura';
            $anterior = $anterioresPorCargo[$cargoNome] ?? null;
            $delta = null;
            $deltaPercentual = null;

            if ($votos !== null && $anterior !== null && $anterior['votos'] !== null) {
                $delta = $votos - $anterior['votos'];
                if ($anterior['votos'] > 0) {
                    $deltaPercentual = round(($delta / $anterior['votos']) * 100, 1);
                }
            }

            if ($votos !== null) {
                $anterioresPorCargo[$cargoNome] = [
                    'ano' => (int) $candidatura->eleicao?->ano,
                    'votos' => $votos,
                ];
                $maiorVotacao = max($maiorVotacao, $votos);
            }

            $top = $melhoresMunicipios->get($candidatura->id);
            $eleicoesAsc[] = [
                'id' => $candidatura->id,
                'ano' => (int) ($candidatura->eleicao?->ano ?? 0),
                'turno' => (int) ($candidatura->eleicao?->turno ?? 1),
                'cargo' => $cargoNome,
                'partido' => $candidatura->partido?->sigla,
                'numero' => $candidatura->numero_urna,
                'nome_urna' => $candidatura->nome_urna,
                'votos' => $votos,
                'tem_resultado' => $temResultado,
                'situacao' => $this->situacao($candidatura, $temResultado),
                'eleito' => (bool) $candidatura->eleito,
                'registro_oficial' => $candidatura->isRegistroOficialTse(),
                'delta' => $delta,
                'delta_percentual' => $deltaPercentual,
                'delta_ano_base' => $anterior['ano'] ?? null,
                'municipios' => (int) ($candidatura->resultados_municipais_count ?? 0),
                'zonas' => (int) ($candidatura->resultados_zonas_count ?? 0),
                'melhor_municipio' => $top?->cidade?->nome,
                'melhor_municipio_votos' => $top ? (int) $top->votos : null,
                'melhor_municipio_percentual' => ($top && $votos && $votos > 0)
                    ? round(((int) $top->votos / $votos) * 100, 1)
                    : null,
            ];
        }

        $eleicoes = collect($eleicoesAsc)
            ->map(function (array $item) use ($maiorVotacao) {
                $item['barra_percentual'] = ($item['votos'] !== null && $maiorVotacao > 0)
                    ? max(2, round(($item['votos'] / $maiorVotacao) * 100, 1))
                    : 0;

                return $item;
            })
            ->sortByDesc(fn (array $item) => sprintf('%04d-%02d-%010d', $item['ano'], $item['turno'], $item['id']))
            ->values()
            ->all();

        $mandatos = $politico->mandatos
            ->filter(function ($mandato) use ($cargo, $ano) {
                if ($cargo !== '' && $mandato->cargo?->nome !== $cargo) {
                    return false;
                }
                if ($ano !== null) {
                    $inicio = (int) ($mandato->ano_inicio ?: $mandato->data_inicio?->year ?: 0);
                    $fim = (int) ($mandato->ano_fim ?: $mandato->data_fim?->year ?: $inicio);
                    if ($inicio && ($ano < $inicio || $ano > max($inicio, $fim))) {
                        return false;
                    }
                }

                return true;
            })
            ->map(fn ($mandato) => [
                'id' => $mandato->id,
                'tipo' => $mandato->tipo ?: 'mandato',
                'cargo' => $mandato->cargo?->nome ?: 'Mandato',
                'partido' => $mandato->partido?->sigla,
                'periodo' => $mandato->periodoExibicao(),
                'situacao' => $mandato->situacao,
                'detalhes' => $mandato->detalhes,
                'fonte' => $mandato->fonteLabel(),
                'fonte_url' => $mandato->fonte_url,
                'fonte_oficial' => (bool) $mandato->fonte_oficial,
            ])
            ->values()
            ->all();

        $eleicoesCollection = collect($eleicoes);
        $ultimaComResultado = $eleicoesCollection->first(fn (array $item) => $item['tem_resultado']);
        $mandatoAtual = collect($mandatos)->first(fn (array $item) => Str::lower((string) $item['situacao']) === 'ativo');

        return [
            'id' => $politico->id,
            'slug' => $politico->slug,
            'nome' => $politico->nome_publico,
            'foto_url' => $politico->foto_url,
            'grupo' => $politico->acompanhamento?->grupo,
            'eleicoes' => $eleicoes,
            'mandatos' => $mandatos,
            'resumo' => [
                'eleicoes' => $eleicoesCollection->count(),
                'com_resultado' => $eleicoesCollection->where('tem_resultado', true)->count(),
                'eleitos' => $eleicoesCollection->where('eleito', true)->count(),
                'mandatos' => count($mandatos),
                'ultima_votacao' => $ultimaComResultado['votos'] ?? null,
                'ultima_votacao_ano' => $ultimaComResultado['ano'] ?? null,
                'mandato_atual' => $mandatoAtual,
            ],
        ];
    }

    private function temResultado(Candidatura $candidatura): bool
    {
        return (($candidatura->resultados_municipais_count ?? 0) > 0)
            || (($candidatura->resultados_zonas_count ?? 0) > 0)
            || filled($candidatura->situacao_eleicao);
    }

    private function situacao(Candidatura $candidatura, bool $temResultado): string
    {
        if ($temResultado && filled($candidatura->situacao_eleicao)) {
            return (string) $candidatura->situacao_eleicao;
        }

        if ($candidatura->isRegistroOficialTse()) {
            return $candidatura->situacaoRegistroExibicao();
        }

        return $temResultado ? 'Resultado disponível' : 'Sem resultado eleitoral';
    }

    /** @param array<int,int> $candidaturaIds @return Collection<int,ResultadoMunicipal> */
    private function melhoresMunicipios(array $candidaturaIds): Collection
    {
        if ($candidaturaIds === []) {
            return collect();
        }

        $maximos = DB::table('politica_resultados_municipais')
            ->selectRaw('candidatura_id, MAX(votos) AS max_votos')
            ->whereIn('candidatura_id', $candidaturaIds)
            ->groupBy('candidatura_id');

        return ResultadoMunicipal::query()
            ->joinSub($maximos, 'maximos', function ($join) {
                $join->on('politica_resultados_municipais.candidatura_id', '=', 'maximos.candidatura_id')
                    ->on('politica_resultados_municipais.votos', '=', 'maximos.max_votos');
            })
            ->with('cidade')
            ->orderBy('politica_resultados_municipais.id')
            ->get(['politica_resultados_municipais.*'])
            ->unique('candidatura_id')
            ->keyBy('candidatura_id');
    }
}
