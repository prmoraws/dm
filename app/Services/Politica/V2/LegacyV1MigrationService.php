<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\MigracaoDados;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\Zona;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class LegacyV1MigrationService
{
    private int $chunk;

    private array $stats = [];

    public function __construct()
    {
        $this->chunk = max(100, min((int) config('politica.migracao_v1.chunk', 1000), 5000));
    }

    public function diagnostico(string $escopo = 'prioritarios'): array
    {
        $this->validarEscopo($escopo);
        $this->validarEstrutura();

        $candidateIds = $this->candidateIdsDoEscopo($escopo);
        $query = DB::table('politica_votacao_detalhada');
        if ($candidateIds !== null) {
            $query->whereIn('candidato_id', $candidateIds);
        }

        $idsComVotos = (clone $query)->distinct()->pluck('candidato_id');

        return [
            'escopo' => $escopo,
            'candidatos_legacy_configurados' => $candidateIds === null ? null : count($candidateIds),
            'candidatos_com_votos' => $idsComVotos->count(),
            'linhas_votacao_legacy' => (clone $query)->count(),
            'locais_utilizados' => (clone $query)->distinct()->count('local_votacao_id'),
            'anos' => (clone $query)->distinct()->orderBy('ano_eleicao')->pluck('ano_eleicao')->map(fn ($v) => (int) $v)->all(),
            'cargos' => (clone $query)->distinct()->orderBy('cargo')->pluck('cargo')->all(),
            'espelhos_legacy' => Schema::hasTable('politica_espelhos') ? DB::table('politica_espelhos')->count() : 0,
            'favoritos_legacy' => Schema::hasTable('cidade_candidato') ? DB::table('cidade_candidato')->count() : 0,
        ];
    }

    public function validarMigracao(string $escopo = 'prioritarios'): array
    {
        $this->validarEscopo($escopo);
        $this->validarEstrutura();

        $contextos = $this->carregarContextos($this->candidateIdsDoEscopo($escopo));
        $linhas = [];
        $ok = true;

        foreach ($contextos as $contexto) {
            $origemChave = implode(':', [
                'legacy_v1',
                (int) $contexto->candidato_id,
                (int) $contexto->ano_eleicao,
                Str::slug((string) $contexto->cargo),
            ]);
            $candidatura = Candidatura::query()->where('origem_chave', $origemChave)->first();
            $legacy = (int) $contexto->votos_total;
            $v2Candidatura = $candidatura ? (int) $candidatura->votos_total : null;
            $v2Secoes = $candidatura
                ? (int) DB::table('politica_resultados_secoes')->where('candidatura_id', $candidatura->id)->sum('votos')
                : null;
            $v2Municipios = $candidatura
                ? (int) DB::table('politica_resultados_municipais')->where('candidatura_id', $candidatura->id)->sum('votos')
                : null;

            $linhaOk = $candidatura !== null
                && $legacy === $v2Candidatura
                && $legacy === $v2Secoes
                && $legacy === $v2Municipios;
            $ok = $ok && $linhaOk;

            $linhas[] = [
                'legacy_candidato_id' => (int) $contexto->candidato_id,
                'nome' => (string) $contexto->nome,
                'ano' => (int) $contexto->ano_eleicao,
                'cargo' => (string) $contexto->cargo,
                'v1' => $legacy,
                'v2_candidatura' => $v2Candidatura,
                'v2_secoes' => $v2Secoes,
                'v2_municipios' => $v2Municipios,
                'ok' => $linhaOk,
            ];
        }

        $espelhosV1 = Schema::hasTable('politica_espelhos') ? DB::table('politica_espelhos')->count() : 0;
        $espelhosV2 = DB::table('politica_espelho_operacional')->count();
        $espelhosOk = $espelhosV1 === $espelhosV2;

        return [
            'ok' => $ok && $espelhosOk,
            'candidaturas' => $linhas,
            'espelhos' => [
                'v1' => $espelhosV1,
                'v2' => $espelhosV2,
                'ok' => $espelhosOk,
            ],
        ];
    }

    public function migrar(string $escopo = 'prioritarios'): array
    {
        $this->validarEscopo($escopo);
        $this->validarEstrutura();
        $this->stats = [
            'escopo' => $escopo,
            'politicos' => 0,
            'candidaturas' => 0,
            'zonas' => 0,
            'secoes' => 0,
            'resultados_secoes' => 0,
            'resultados_zonas' => 0,
            'resultados_municipais' => 0,
            'espelhos_operacionais' => 0,
            'favoritos_convertidos' => 0,
            'favoritos_orfaos' => 0,
            'candidaturas_municipais_multicidade' => 0,
            'locais_sem_zona_secao' => 0,
        ];

        $registro = MigracaoDados::query()->updateOrCreate(
            ['chave' => 'politica_v1_para_v2_'.$escopo],
            [
                'status' => 'executando',
                'iniciada_em' => now(),
                'concluida_em' => null,
                'ultimo_erro' => null,
            ]
        );

        try {
            $candidateIds = $this->candidateIdsDoEscopo($escopo);
            $contextos = $this->carregarContextos($candidateIds);

            if ($contextos->isNotEmpty()) {
                [$candidaturas, $eleicoes] = $this->migrarPoliticosECandidaturas($contextos);
                [$localParsed, $sectionKeys] = $this->prepararTerritorio($candidateIds, $eleicoes);
                $sectionIds = $this->resolverSectionIds($sectionKeys);

                $this->migrarResultadosSecoes($candidateIds, $candidaturas, $eleicoes, $localParsed, $sectionIds);
                $this->migrarResultadosMunicipais($candidateIds, $candidaturas);
                $this->migrarResultadosZonas(array_values($candidaturas));
                $this->atualizarTotaisCandidaturas($candidateIds, $candidaturas);
            }

            $this->migrarEspelhosOperacionais();
            $this->migrarFavoritosLegados();

            $registro->update([
                'status' => 'concluida',
                'concluida_em' => now(),
                'estatisticas' => $this->stats,
                'ultimo_erro' => null,
            ]);

            return $this->stats;
        } catch (Throwable $e) {
            $registro->update([
                'status' => 'falhou',
                'concluida_em' => now(),
                'estatisticas' => $this->stats,
                'ultimo_erro' => Str::limit($e->getMessage(), 5000, ''),
            ]);

            throw $e;
        }
    }

    private function carregarContextos(?array $candidateIds): Collection
    {
        $query = DB::table('politica_votacao_detalhada as v')
            ->join('politica_locais_votacao as l', 'l.id', '=', 'v.local_votacao_id')
            ->join('politica_candidatos as c', 'c.id', '=', 'v.candidato_id')
            ->select([
                'v.candidato_id',
                'v.ano_eleicao',
                'v.cargo',
                'c.nome',
                'c.partido',
            ])
            ->selectRaw('MIN(l.cidade_id) as cidade_id_min')
            ->selectRaw('COUNT(DISTINCT l.cidade_id) as cidades_distintas')
            ->selectRaw('SUM(v.votos_recebidos) as votos_total')
            ->groupBy('v.candidato_id', 'v.ano_eleicao', 'v.cargo', 'c.nome', 'c.partido')
            ->orderBy('v.ano_eleicao')
            ->orderBy('v.cargo')
            ->orderBy('v.candidato_id');

        if ($candidateIds !== null) {
            $query->whereIn('v.candidato_id', $candidateIds);
        }

        return $query->get();
    }

    /**
     * @return array{0: array<string,int>, 1: array<string,int>}
     */
    private function migrarPoliticosECandidaturas(Collection $contextos): array
    {
        $candidaturas = [];
        $eleicoes = [];
        $politicosAntes = Politico::query()->count();
        $candidaturasAntes = Candidatura::query()->count();

        foreach ($contextos as $contexto) {
            $cargo = $this->resolverCargo((string) $contexto->cargo);
            $tipoEleicao = $this->tipoEleicaoParaCargo($cargo->nome);
            $eleicaoKey = $this->eleicaoKey((int) $contexto->ano_eleicao, $tipoEleicao);

            if (! isset($eleicoes[$eleicaoKey])) {
                $eleicao = Eleicao::query()->firstOrCreate(
                    [
                        'ano' => (int) $contexto->ano_eleicao,
                        'turno' => 1,
                        'tipo' => $tipoEleicao,
                        'uf' => 'BA',
                    ],
                    [
                        'descricao' => sprintf(
                            'Eleições %s %d - Bahia - dados legados V1',
                            $tipoEleicao === 'municipal' ? 'Municipais' : 'Gerais',
                            (int) $contexto->ano_eleicao
                        ),
                        'status' => 'historica',
                    ]
                );
                $eleicoes[$eleicaoKey] = $eleicao->id;
            }

            $politico = $this->resolverPoliticoLegado(
                (int) $contexto->candidato_id,
                (string) $contexto->nome
            );
            $partido = $this->resolverPartidoLegado($contexto->partido);
            if ($tipoEleicao === 'municipal' && (int) $contexto->cidades_distintas > 1) {
                $this->stats['candidaturas_municipais_multicidade']++;
            }

            $cidadeId = $tipoEleicao === 'municipal' && (int) $contexto->cidades_distintas === 1
                ? (int) $contexto->cidade_id_min
                : null;

            $origemChave = implode(':', [
                'legacy_v1',
                (int) $contexto->candidato_id,
                (int) $contexto->ano_eleicao,
                Str::slug((string) $contexto->cargo),
            ]);

            $candidatura = Candidatura::query()->updateOrCreate(
                ['origem_chave' => $origemChave],
                [
                    'politico_id' => $politico->id,
                    'eleicao_id' => $eleicoes[$eleicaoKey],
                    'cargo_id' => $cargo->id,
                    'partido_id' => $partido?->id,
                    'cidade_id' => $cidadeId,
                    'uf' => 'BA',
                    'origem' => 'legacy_v1',
                    'legacy_candidato_id' => (int) $contexto->candidato_id,
                    'votos_total' => (int) $contexto->votos_total,
                    'sincronizado_em' => now(),
                ]
            );

            $candidaturas[$this->contextKey(
                (int) $contexto->candidato_id,
                (int) $contexto->ano_eleicao,
                (string) $contexto->cargo
            )] = $candidatura->id;
        }

        $this->stats['politicos'] = max(0, Politico::query()->count() - $politicosAntes);
        $this->stats['candidaturas'] = max(0, Candidatura::query()->count() - $candidaturasAntes);

        return [$candidaturas, $eleicoes];
    }

    /**
     * @return array{0: array<int,array{cidade_id:int,zona:int,secao:int}>, 1: array<string,array{eleicao_id:int,zona_id:int,numero:int}>}
     */
    private function prepararTerritorio(?array $candidateIds, array $eleicoes): array
    {
        $localParsed = [];
        $zoneRows = [];

        $query = DB::table('politica_votacao_detalhada as v')
            ->join('politica_locais_votacao as l', 'l.id', '=', 'v.local_votacao_id')
            ->select('l.id', 'l.cidade_id', 'l.endereco')
            ->distinct()
            ->orderBy('l.id');

        if ($candidateIds !== null) {
            $query->whereIn('v.candidato_id', $candidateIds);
        }

        foreach ($query->cursor() as $local) {
            $parsed = $this->parseZonaSecao((string) $local->endereco);
            if ($parsed === null || $local->cidade_id === null) {
                $this->stats['locais_sem_zona_secao']++;
                continue;
            }

            [$zona, $secao] = $parsed;
            $cidadeId = (int) $local->cidade_id;
            $localParsed[(int) $local->id] = [
                'cidade_id' => $cidadeId,
                'zona' => $zona,
                'secao' => $secao,
            ];
            $zoneRows[$cidadeId.':'.$zona] = [
                'cidade_id' => $cidadeId,
                'numero' => $zona,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $zonasAntes = Zona::query()->count();
        foreach (array_chunk(array_values($zoneRows), $this->chunk) as $rows) {
            DB::table('politica_zonas')->insertOrIgnore($rows);
        }
        $this->stats['zonas'] = max(0, Zona::query()->count() - $zonasAntes);

        $zoneIds = [];
        $cidadeIds = array_values(array_unique(array_column($localParsed, 'cidade_id')));
        foreach (array_chunk($cidadeIds, 500) as $ids) {
            foreach (Zona::query()->whereIn('cidade_id', $ids)->get(['id', 'cidade_id', 'numero']) as $zona) {
                $zoneIds[$zona->cidade_id.':'.$zona->numero] = $zona->id;
            }
        }

        $sectionRows = [];
        $legacyToSectionKey = [];
        $sectionQuery = DB::table('politica_votacao_detalhada')
            ->select('local_votacao_id', 'ano_eleicao', 'cargo')
            ->distinct()
            ->orderBy('ano_eleicao')
            ->orderBy('local_votacao_id');

        if ($candidateIds !== null) {
            $sectionQuery->whereIn('candidato_id', $candidateIds);
        }

        foreach ($sectionQuery->cursor() as $item) {
            $localId = (int) $item->local_votacao_id;
            if (! isset($localParsed[$localId])) {
                continue;
            }

            $tipoEleicao = $this->tipoEleicaoParaCargo($this->nomeCargo((string) $item->cargo));
            $eleicaoId = $eleicoes[$this->eleicaoKey((int) $item->ano_eleicao, $tipoEleicao)] ?? null;
            if ($eleicaoId === null) {
                continue;
            }

            $parsed = $localParsed[$localId];
            $zonaId = $zoneIds[$parsed['cidade_id'].':'.$parsed['zona']] ?? null;
            if ($zonaId === null) {
                continue;
            }

            $key = $eleicaoId.':'.$zonaId.':'.$parsed['secao'];
            $legacyToSectionKey[$eleicaoId.':'.$localId] = $key;
            $sectionRows[$key] = [
                'eleicao_id' => $eleicaoId,
                'zona_id' => $zonaId,
                'local_votacao_id' => $localId,
                'numero' => $parsed['secao'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $secoesAntes = DB::table('politica_secoes')->count();
        foreach (array_chunk(array_values($sectionRows), $this->chunk) as $rows) {
            DB::table('politica_secoes')->upsert(
                $rows,
                ['eleicao_id', 'zona_id', 'numero'],
                ['local_votacao_id', 'updated_at']
            );
        }
        $this->stats['secoes'] = max(0, DB::table('politica_secoes')->count() - $secoesAntes);

        $sectionIds = [];
        $eleicaoIds = array_values(array_unique(array_values($eleicoes)));
        foreach ($eleicaoIds as $eleicaoId) {
            foreach (DB::table('politica_secoes')
                ->where('eleicao_id', $eleicaoId)
                ->get(['id', 'eleicao_id', 'zona_id', 'numero']) as $secao) {
                $sectionIds[$secao->eleicao_id.':'.$secao->zona_id.':'.$secao->numero] = (int) $secao->id;
            }
        }

        $resolved = [];
        foreach ($legacyToSectionKey as $legacyKey => $sectionKey) {
            if (isset($sectionIds[$sectionKey])) {
                $resolved[$legacyKey] = $sectionIds[$sectionKey];
            }
        }

        return [$localParsed, $resolved];
    }

    private function resolverSectionIds(array $sectionKeys): array
    {
        return $sectionKeys;
    }

    private function migrarResultadosSecoes(
        ?array $candidateIds,
        array $candidaturas,
        array $eleicoes,
        array $localParsed,
        array $sectionIds
    ): void {
        $rows = [];
        $query = DB::table('politica_votacao_detalhada')
            ->select('candidato_id', 'ano_eleicao', 'cargo', 'local_votacao_id')
            ->selectRaw('SUM(votos_recebidos) as votos')
            ->groupBy('candidato_id', 'ano_eleicao', 'cargo', 'local_votacao_id')
            ->orderBy('candidato_id')
            ->orderBy('ano_eleicao')
            ->orderBy('local_votacao_id');

        if ($candidateIds !== null) {
            $query->whereIn('candidato_id', $candidateIds);
        }

        foreach ($query->cursor() as $item) {
            $contextKey = $this->contextKey((int) $item->candidato_id, (int) $item->ano_eleicao, (string) $item->cargo);
            $candidaturaId = $candidaturas[$contextKey] ?? null;
            if ($candidaturaId === null) {
                continue;
            }

            $tipoEleicao = $this->tipoEleicaoParaCargo($this->nomeCargo((string) $item->cargo));
            $eleicaoId = $eleicoes[$this->eleicaoKey((int) $item->ano_eleicao, $tipoEleicao)] ?? null;
            $legacyLocalKey = $eleicaoId.':'.(int) $item->local_votacao_id;
            $sectionId = $sectionIds[$legacyLocalKey] ?? null;
            if ($eleicaoId === null || $sectionId === null || ! isset($localParsed[(int) $item->local_votacao_id])) {
                continue;
            }

            $rows[] = [
                'eleicao_id' => $eleicaoId,
                'candidatura_id' => $candidaturaId,
                'secao_id' => $sectionId,
                'votos' => (int) $item->votos,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= $this->chunk) {
                $this->upsertResultadosSecoes($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            $this->upsertResultadosSecoes($rows);
        }
    }

    private function upsertResultadosSecoes(array $rows): void
    {
        DB::table('politica_resultados_secoes')->upsert(
            $rows,
            ['eleicao_id', 'candidatura_id', 'secao_id'],
            ['votos', 'updated_at']
        );
        $this->stats['resultados_secoes'] += count($rows);
    }

    private function migrarResultadosMunicipais(?array $candidateIds, array $candidaturas): void
    {
        $rows = [];
        $eleicaoPorCandidatura = DB::table('politica_candidaturas')
            ->whereIn('id', array_values($candidaturas))
            ->pluck('eleicao_id', 'id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $query = DB::table('politica_votacao_detalhada as v')
            ->join('politica_locais_votacao as l', 'l.id', '=', 'v.local_votacao_id')
            ->select('v.candidato_id', 'v.ano_eleicao', 'v.cargo', 'l.cidade_id')
            ->selectRaw('SUM(v.votos_recebidos) as votos')
            ->whereNotNull('l.cidade_id')
            ->groupBy('v.candidato_id', 'v.ano_eleicao', 'v.cargo', 'l.cidade_id')
            ->orderBy('v.candidato_id')
            ->orderBy('v.ano_eleicao')
            ->orderBy('l.cidade_id');

        if ($candidateIds !== null) {
            $query->whereIn('v.candidato_id', $candidateIds);
        }

        foreach ($query->cursor() as $item) {
            $contextKey = $this->contextKey((int) $item->candidato_id, (int) $item->ano_eleicao, (string) $item->cargo);
            $candidaturaId = $candidaturas[$contextKey] ?? null;
            if ($candidaturaId === null) {
                continue;
            }

            $eleicaoId = $eleicaoPorCandidatura[$candidaturaId] ?? null;
            if ($eleicaoId === null) {
                continue;
            }

            $rows[] = [
                'eleicao_id' => $eleicaoId,
                'candidatura_id' => $candidaturaId,
                'cidade_id' => (int) $item->cidade_id,
                'votos' => (int) $item->votos,
                'percentual' => null,
                'posicao' => null,
                'sincronizado_em' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= $this->chunk) {
                $this->upsertResultadosMunicipais($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            $this->upsertResultadosMunicipais($rows);
        }
    }

    private function upsertResultadosMunicipais(array $rows): void
    {
        DB::table('politica_resultados_municipais')->upsert(
            $rows,
            ['eleicao_id', 'candidatura_id', 'cidade_id'],
            ['votos', 'percentual', 'posicao', 'sincronizado_em', 'updated_at']
        );
        $this->stats['resultados_municipais'] += count($rows);
    }

    private function migrarResultadosZonas(array $candidaturaIds): void
    {
        if ($candidaturaIds === []) {
            return;
        }

        $rows = [];
        $query = DB::table('politica_resultados_secoes as rs')
            ->join('politica_secoes as s', 's.id', '=', 'rs.secao_id')
            ->select('rs.eleicao_id', 'rs.candidatura_id', 's.zona_id')
            ->selectRaw('SUM(rs.votos) as votos')
            ->whereIn('rs.candidatura_id', $candidaturaIds)
            ->groupBy('rs.eleicao_id', 'rs.candidatura_id', 's.zona_id')
            ->orderBy('rs.candidatura_id')
            ->orderBy('s.zona_id');

        foreach ($query->cursor() as $item) {
            $rows[] = [
                'eleicao_id' => (int) $item->eleicao_id,
                'candidatura_id' => (int) $item->candidatura_id,
                'zona_id' => (int) $item->zona_id,
                'votos' => (int) $item->votos,
                'percentual' => null,
                'sincronizado_em' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= $this->chunk) {
                $this->upsertResultadosZonas($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            $this->upsertResultadosZonas($rows);
        }
    }

    private function upsertResultadosZonas(array $rows): void
    {
        DB::table('politica_resultados_zonas')->upsert(
            $rows,
            ['eleicao_id', 'candidatura_id', 'zona_id'],
            ['votos', 'percentual', 'sincronizado_em', 'updated_at']
        );
        $this->stats['resultados_zonas'] += count($rows);
    }

    private function atualizarTotaisCandidaturas(?array $candidateIds, array $candidaturas): void
    {
        $query = DB::table('politica_votacao_detalhada')
            ->select('candidato_id', 'ano_eleicao', 'cargo')
            ->selectRaw('SUM(votos_recebidos) as votos')
            ->groupBy('candidato_id', 'ano_eleicao', 'cargo');

        if ($candidateIds !== null) {
            $query->whereIn('candidato_id', $candidateIds);
        }

        foreach ($query->cursor() as $item) {
            $key = $this->contextKey((int) $item->candidato_id, (int) $item->ano_eleicao, (string) $item->cargo);
            if (! isset($candidaturas[$key])) {
                continue;
            }

            DB::table('politica_candidaturas')
                ->where('id', $candidaturas[$key])
                ->update([
                    'votos_total' => (int) $item->votos,
                    'sincronizado_em' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    private function migrarEspelhosOperacionais(): void
    {
        if (! Schema::hasTable('politica_espelhos')) {
            return;
        }

        DB::table('politica_espelhos')->orderBy('id')->chunkById($this->chunk, function (Collection $espelhos): void {
            foreach ($espelhos as $espelho) {
                EspelhoOperacional::query()->updateOrCreate(
                    ['cidade_id' => (int) $espelho->cidade_id],
                    [
                        'legacy_espelho_id' => (int) $espelho->id,
                        'presidente_local' => $this->nullSeVazio($espelho->presidente_local),
                        'indicacao_bispo' => $this->nullSeVazio($espelho->indicacao_bispo),
                        'filiados_republicanos' => $espelho->filiados_republicanos !== null
                            ? (int) $espelho->filiados_republicanos
                            : null,
                        'observacoes' => $this->nullSeVazio($espelho->observacoes),
                        'dados_publicos_legados' => [
                            'prefeito_atual_nome' => $this->nullSeVazio($espelho->prefeito_atual_nome),
                            'prefeito_atual_partido' => $this->nullSeVazio($espelho->prefeito_atual_partido),
                            'prefeito_atual_votos' => $espelho->prefeito_atual_votos !== null
                                ? (int) $espelho->prefeito_atual_votos
                                : null,
                            'origem' => 'politica_espelhos_v1',
                        ],
                        'revisado_em' => $espelho->updated_at,
                    ]
                );
                $this->stats['espelhos_operacionais']++;
            }
        });
    }

    private function migrarFavoritosLegados(): void
    {
        if (! Schema::hasTable('cidade_candidato')) {
            return;
        }

        foreach (DB::table('cidade_candidato')->orderBy('cidade_id')->orderBy('candidato_id')->get() as $favorito) {
            $candidaturas = Candidatura::query()
                ->where('legacy_candidato_id', (int) $favorito->candidato_id)
                ->get();

            if ($candidaturas->isEmpty()) {
                $this->stats['favoritos_orfaos']++;
                continue;
            }

            foreach ($candidaturas as $candidatura) {
                EspelhoInteligencia::query()->updateOrCreate(
                    [
                        'cidade_id' => (int) $favorito->cidade_id,
                        'contexto_chave' => 'eleicao:'.$candidatura->eleicao_id.':candidatura:'.$candidatura->id,
                    ],
                    [
                        'eleicao_id' => $candidatura->eleicao_id,
                        'candidatura_id' => $candidatura->id,
                        'classificacao' => 'acompanhamento',
                        'prioridade' => 2,
                        'observacoes' => 'Acompanhamento migrado de cidade_candidato (Política V1).',
                        'revisado_em' => now(),
                    ]
                );
                $this->stats['favoritos_convertidos']++;
            }
        }
    }

    private function resolverPoliticoLegado(int $legacyId, string $nome): Politico
    {
        $slugPrioritario = config('politica.migracao_v1.prioritarios_legacy.'.$legacyId);
        if ($slugPrioritario) {
            $politico = Politico::query()->where('slug', $slugPrioritario)->first();
            if ($politico) {
                if (mb_strlen($politico->nome_completo) < mb_strlen($nome)) {
                    $politico->update(['nome_completo' => $nome]);
                }

                return $politico;
            }
        }

        $slug = Str::slug($nome).'-v1-'.$legacyId;

        return Politico::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'nome_completo' => $nome,
                'nome_publico' => Str::title(Str::lower($nome)),
                'ativo' => true,
            ]
        );
    }

    private function resolverPartidoLegado(?string $sigla): ?Partido
    {
        $sigla = trim((string) $sigla);
        if ($sigla === '') {
            return null;
        }

        $sigla = mb_strtoupper($sigla, 'UTF-8');

        return Partido::query()->firstOrCreate(
            ['sigla' => Str::limit($sigla, 20, '')],
            ['nome' => $sigla, 'ativo' => true]
        );
    }

    private function resolverCargo(string $cargoLegacy): Cargo
    {
        $nome = $this->nomeCargo($cargoLegacy);
        $cargo = Cargo::query()->where('nome', $nome)->first();

        if ($cargo) {
            return $cargo;
        }

        return Cargo::query()->create([
            'nome' => $nome,
            'esfera' => $this->tipoEleicaoParaCargo($nome) === 'municipal' ? 'municipal' : null,
            'abrangencia' => $this->tipoEleicaoParaCargo($nome) === 'municipal' ? 'municipio' : null,
            'ativo' => true,
        ]);
    }

    private function nomeCargo(string $cargoLegacy): string
    {
        $normalizado = mb_strtoupper(trim($cargoLegacy), 'UTF-8');
        $map = config('politica.migracao_v1.cargos', []);

        return $map[$normalizado] ?? Str::title(Str::lower($normalizado));
    }

    private function tipoEleicaoParaCargo(string $cargo): string
    {
        return in_array($cargo, ['Prefeito', 'Vereador'], true) ? 'municipal' : 'geral';
    }

    private function eleicaoKey(int $ano, string $tipo): string
    {
        return $ano.':'.$tipo.':BA:1';
    }

    private function contextKey(int $candidateId, int $ano, string $cargo): string
    {
        return $candidateId.':'.$ano.':'.mb_strtoupper(trim($cargo), 'UTF-8');
    }

    private function parseZonaSecao(string $endereco): ?array
    {
        if (! preg_match('/Zona:\s*(\d+)\s*\/\s*Se(?:ç|c)(?:ã|a)o:\s*(\d+)/iu', $endereco, $m)) {
            return null;
        }

        return [(int) $m[1], (int) $m[2]];
    }

    private function candidateIdsDoEscopo(string $escopo): ?array
    {
        if ($escopo === 'todos') {
            return null;
        }

        return array_map('intval', array_keys(config('politica.migracao_v1.prioritarios_legacy', [])));
    }

    private function validarEscopo(string $escopo): void
    {
        if (! in_array($escopo, ['prioritarios', 'todos'], true)) {
            throw new RuntimeException('Escopo inválido. Use prioritarios ou todos.');
        }
    }

    private function validarEstrutura(): void
    {
        $obrigatorias = [
            'politica_candidatos',
            'politica_cidades',
            'politica_locais_votacao',
            'politica_votacao_detalhada',
            'politica_politicos',
            'politica_candidaturas',
            'politica_zonas',
            'politica_secoes',
            'politica_resultados_secoes',
            'politica_resultados_zonas',
            'politica_resultados_municipais',
            'politica_espelho_operacional',
            'politica_migracoes_dados',
        ];

        $faltantes = array_values(array_filter($obrigatorias, fn (string $table) => ! Schema::hasTable($table)));
        if ($faltantes !== []) {
            throw new RuntimeException('Estrutura incompleta. Tabelas ausentes: '.implode(', ', $faltantes));
        }
    }

    private function nullSeVazio(mixed $value): mixed
    {
        return is_string($value) && trim($value) === '' ? null : $value;
    }
}
