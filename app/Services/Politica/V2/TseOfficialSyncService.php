<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\ResultadoZona;
use App\Models\Politica\V2\TseImportacao;
use App\Models\Politica\V2\Zona;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class TseOfficialSyncService
{
    public function __construct(
        private readonly TseCsvReader $reader,
        private readonly TseOpenDataDownloader $downloader,
        private readonly PoliticaStorageService $storage,
    ) {}

    public function arquivo(string $tipo, int $ano, ?string $arquivo = null, bool $forcarDownload = false): array
    {
        if ($arquivo !== null && $arquivo !== '') {
            $real = realpath($arquivo);
            if ($real === false || ! is_file($real)) {
                throw new RuntimeException("Arquivo informado não existe: {$arquivo}");
            }

            return [
                'path' => $real,
                'changed' => null,
                'status' => null,
                'url' => $this->downloader->url($tipo, $ano),
                'manual' => true,
            ];
        }

        return $this->downloader->download($tipo, $ano, $forcarDownload) + ['manual' => false];
    }

    public function limparArquivoBaixado(array $arquivoInfo): bool
    {
        if (($arquivoInfo['manual'] ?? false) === true) {
            return false;
        }

        $path = $arquivoInfo['path'] ?? null;

        return is_string($path) && $path !== '' ? $this->downloader->cleanup($path) : false;
    }

    public function diagnosticarCandidaturas(int $ano, string $uf, string $escopo, string $arquivo): array
    {
        $stats = $this->baseStats($ano, $uf, 'candidaturas', $escopo, $arquivo);
        $cargos = [];
        $partidos = [];
        $seen = [];

        foreach ($this->candidateRows($ano, $uf, $arquivo) as $row) {
            $stats['linhas_lidas']++;
            $cargo = $this->cargoCanonico($row['DS_CARGO'] ?? null);
            if (! $cargo || ! $this->rowCandidateMatchesUf($row, $cargo, $uf) || ! $this->rowCandidateInScope($row, $cargo, $escopo)) {
                $stats['ignorados']++;
                continue;
            }

            $candidateKey = ($this->value($row, 'CD_ELEICAO') ?: $ano).'|'.($this->value($row, 'SQ_CANDIDATO') ?: '');
            if (isset($seen[$candidateKey])) {
                $stats['ignorados']++;
                continue;
            }
            $seen[$candidateKey] = true;

            $stats['linhas_selecionadas']++;
            $cargos[$cargo] = ($cargos[$cargo] ?? 0) + 1;
            $sigla = strtoupper(trim((string) ($row['SG_PARTIDO'] ?? '')));
            if ($sigla !== '') {
                $partidos[$sigla] = ($partidos[$sigla] ?? 0) + 1;
            }
        }

        ksort($cargos);
        arsort($partidos);
        $stats['cargos'] = $cargos;
        $stats['partidos'] = array_slice($partidos, 0, 20, true);
        $stats['estimativa_banco_bytes'] = $this->storage->estimateCandidates($stats['linhas_selecionadas']);
        $stats['estimativa_banco'] = $this->storage->human($stats['estimativa_banco_bytes']);

        return $stats;
    }

    public function sincronizarCandidaturas(int $ano, string $uf, string $escopo, string $arquivo, bool $ignorarLimite = false): array
    {
        $this->storage->assertCurrentBelowLimit($ignorarLimite);
        $importacao = $this->beginImport($ano, $uf, 'candidaturas', $escopo, $arquivo);
        $stats = $this->baseStats($ano, $uf, 'candidaturas', $escopo, $arquivo);
        $cityMap = $this->cityMap();
        $prioritarios = $this->prioritarioAliases();
        $seen = [];

        try {
            foreach ($this->candidateRows($ano, $uf, $arquivo) as $row) {
                $stats['linhas_lidas']++;
                $cargoNome = $this->cargoCanonico($row['DS_CARGO'] ?? null);
                if (! $cargoNome || ! $this->rowCandidateMatchesUf($row, $cargoNome, $uf) || ! $this->rowCandidateInScope($row, $cargoNome, $escopo)) {
                    $stats['ignorados']++;
                    continue;
                }

                $candidateKey = ($this->value($row, 'CD_ELEICAO') ?: $ano).'|'.($this->value($row, 'SQ_CANDIDATO') ?: '');
                if (isset($seen[$candidateKey])) {
                    $stats['ignorados']++;
                    continue;
                }
                $seen[$candidateKey] = true;

                $stats['linhas_selecionadas']++;
                $cargo = $this->resolverCargo($cargoNome, $row['CD_CARGO'] ?? null);
                $eleicao = $this->resolverEleicao($ano, $uf, $cargoNome, $row);
                $partido = $this->resolverPartido($row);
                $cidade = $this->resolverCidadeCandidatura($cargoNome, $row, $cityMap);
                $politico = $this->resolverPolitico($row, $cargo, $eleicao, $cidade, $prioritarios);

                $sq = $this->value($row, 'SQ_CANDIDATO');
                if ($sq === null) {
                    $stats['ignorados']++;
                    continue;
                }

                $existing = Candidatura::query()
                    ->where('eleicao_id', $eleicao->id)
                    ->where('tse_sq_candidato', $sq)
                    ->first();

                if (! $existing) {
                    $existing = Candidatura::query()
                        ->where('eleicao_id', $eleicao->id)
                        ->where('politico_id', $politico->id)
                        ->where('cargo_id', $cargo->id)
                        ->when($cidade, fn ($q) => $q->where('cidade_id', $cidade->id))
                        ->first();
                }

                $payload = [
                    'politico_id' => $politico->id,
                    'eleicao_id' => $eleicao->id,
                    'cargo_id' => $cargo->id,
                    'partido_id' => $partido?->id,
                    'tse_sq_candidato' => $sq,
                    'numero_urna' => $this->value($row, 'NR_CANDIDATO'),
                    'nome_urna' => $this->value($row, 'NM_URNA_CANDIDATO') ?: $politico->nome_publico,
                    'uf' => $cargoNome === 'Presidente' ? 'BR' : $uf,
                    'cidade_id' => $cidade?->id,
                    'origem' => 'tse_dados_abertos',
                    // Se a candidatura nasceu da V1, preservamos a chave legada para auditoria;
                    // o identificador oficial passa a ser tse_sq_candidato.
                    'origem_chave' => $existing?->legacy_candidato_id
                        ? $existing->origem_chave
                        : implode(':', ['tse', $ano, $eleicao->tse_eleicao_codigo ?: $eleicao->id, $sq]),
                    'situacao_registro' => $this->value($row, 'DS_SITUACAO_CANDIDATURA')
                        ?: $this->value($row, 'DS_DETALHE_SITUACAO_CAND'),
                    'coligacao' => $this->value($row, 'NM_COLIGACAO') ?: $this->value($row, 'DS_COMPOSICAO_COLIGACAO'),
                    'federacao' => $this->value($row, 'SG_FEDERACAO') ?: $this->value($row, 'NM_FEDERACAO'),
                    'sincronizado_em' => now(),
                ];

                if ($existing) {
                    $existing->fill($payload)->save();
                    $stats['atualizados']++;
                } else {
                    Candidatura::query()->create($payload);
                    $stats['inseridos']++;
                }
            }

            $stats['estimativa_banco_bytes'] = $this->storage->estimateCandidates($stats['linhas_selecionadas']);
            $stats['estimativa_banco'] = $this->storage->human($stats['estimativa_banco_bytes']);
            $this->finishImport($importacao, $stats);
            Cache::forget('politica:v2:dashboard:resumo');

            return $stats;
        } catch (Throwable $e) {
            $this->failImport($importacao, $stats, $e);
            throw $e;
        }
    }

    public function diagnosticarResultados(int $ano, string $uf, string $escopo, string $arquivo): array
    {
        $stats = $this->baseStats($ano, $uf, 'resultados', $escopo, $arquivo);
        $sqSet = $this->candidateSqSet($ano, $escopo);
        if ($sqSet === []) {
            throw new RuntimeException("Nenhuma candidatura TSE do escopo {$escopo} foi encontrada para {$ano}. Sincronize candidaturas primeiro.");
        }
        $municipios = [];
        $municipalRows = [];
        $zoneRows = [];

        foreach ($this->reader->rows($arquivo) as $row) {
            $stats['linhas_lidas']++;
            $sq = $this->value($row, 'SQ_CANDIDATO');
            if ($sq === null || ! isset($sqSet[$sq])) {
                $stats['ignorados']++;
                continue;
            }

            $cargo = $this->cargoCanonico($row['DS_CARGO'] ?? null);
            $rowUf = strtoupper((string) ($row['SG_UF'] ?? ''));
            if ($cargo !== 'Presidente' && $rowUf !== strtoupper($uf)) {
                $stats['ignorados']++;
                continue;
            }

            $stats['linhas_selecionadas']++;
            if ($rowUf === strtoupper($uf)) {
                $municipio = $this->value($row, 'NM_MUNICIPIO');
                if ($municipio) {
                    $municipioKey = $this->normalize($municipio);
                    $municipios[$municipioKey] = true;
                    $municipalRows[$sq.'|'.$municipioKey] = true;

                    $zona = (int) ($row['NR_ZONA'] ?? 0);
                    if ($zona > 0) {
                        $zoneRows[$sq.'|'.$municipioKey.'|'.$zona] = true;
                    }
                }
            }
        }

        $stats['municipios_com_resultado'] = count($municipios);
        $stats['resultados_municipais_estimados'] = count($municipalRows);
        $stats['resultados_zonas_estimados'] = count($zoneRows);
        $stats['estimativa_banco_bytes'] = $this->storage->estimateResults(count($municipalRows), count($zoneRows));
        $stats['estimativa_banco'] = $this->storage->human($stats['estimativa_banco_bytes']);

        return $stats;
    }

    public function sincronizarResultados(int $ano, string $uf, string $escopo, string $arquivo, bool $ignorarLimite = false): array
    {
        $templates = $this->candidateTemplatesBySq($ano, $escopo);
        if ($templates === []) {
            throw new RuntimeException("Nenhuma candidatura TSE do escopo {$escopo} foi encontrada para {$ano}. Sincronize candidaturas primeiro.");
        }

        $this->storage->assertCurrentBelowLimit($ignorarLimite);
        $importacao = $this->beginImport($ano, $uf, 'resultados', $escopo, $arquivo);
        $stats = $this->baseStats($ano, $uf, 'resultados', $escopo, $arquivo);
        $cityMap = $this->cityMap();
        $zones = [];
        $municipal = [];
        $zonal = [];
        $totals = [];
        $situacoes = [];
        $candidaturasAfetadas = [];

        try {
            foreach ($this->reader->rows($arquivo) as $row) {
                $stats['linhas_lidas']++;
                $sq = $this->value($row, 'SQ_CANDIDATO');
                if ($sq === null || ! isset($templates[$sq])) {
                    $stats['ignorados']++;
                    continue;
                }

                $cargoNome = $this->cargoCanonico($row['DS_CARGO'] ?? null);
                if (! $cargoNome) {
                    $stats['ignorados']++;
                    continue;
                }

                $rowUf = strtoupper((string) ($row['SG_UF'] ?? ''));
                if ($cargoNome !== 'Presidente' && $rowUf !== strtoupper($uf)) {
                    $stats['ignorados']++;
                    continue;
                }

                $template = $templates[$sq];
                $candidatura = $this->resolverCandidaturaResultado($ano, $uf, $cargoNome, $row, $template);
                $votes = max(0, (int) ($row['QT_VOTOS_NOMINAIS'] ?? $row['QT_VOTOS'] ?? 0));
                $stats['linhas_selecionadas']++;
                $candidaturasAfetadas[$candidatura->id] = true;
                $totals[$candidatura->id] = ($totals[$candidatura->id] ?? 0) + $votes;

                $situacao = $this->value($row, 'DS_SIT_TOT_TURNO');
                if ($situacao) {
                    $situacoes[$candidatura->id] = $situacao;
                }

                // O total presidencial é nacional; a camada territorial continua restrita à Bahia.
                if ($rowUf !== strtoupper($uf)) {
                    continue;
                }

                $cidade = $this->resolverCidadeResultado($row, $cityMap);
                if (! $cidade) {
                    $stats['municipios_nao_resolvidos'] = ($stats['municipios_nao_resolvidos'] ?? 0) + 1;
                    continue;
                }

                $municipalKey = implode(':', [$candidatura->eleicao_id, $candidatura->id, $cidade->id]);
                if (! isset($municipal[$municipalKey])) {
                    $municipal[$municipalKey] = [
                        'eleicao_id' => $candidatura->eleicao_id,
                        'candidatura_id' => $candidatura->id,
                        'cidade_id' => $cidade->id,
                        'votos' => 0,
                    ];
                }
                $municipal[$municipalKey]['votos'] += $votes;

                $numeroZona = (int) ($row['NR_ZONA'] ?? 0);
                if ($numeroZona > 0) {
                    $zoneCacheKey = $cidade->id.':'.$numeroZona;
                    if (! isset($zones[$zoneCacheKey])) {
                        $zona = Zona::query()->firstOrCreate(
                            ['cidade_id' => $cidade->id, 'numero' => $numeroZona],
                            ['tse_codigo' => (string) $numeroZona]
                        );
                        $zones[$zoneCacheKey] = $zona->id;
                    }

                    $zonaId = $zones[$zoneCacheKey];
                    $zonalKey = implode(':', [$candidatura->eleicao_id, $candidatura->id, $zonaId]);
                    if (! isset($zonal[$zonalKey])) {
                        $zonal[$zonalKey] = [
                            'eleicao_id' => $candidatura->eleicao_id,
                            'candidatura_id' => $candidatura->id,
                            'zona_id' => $zonaId,
                            'votos' => 0,
                        ];
                    }
                    $zonal[$zonalKey]['votos'] += $votes;
                }
            }

            $affectedIds = array_map('intval', array_keys($candidaturasAfetadas));
            $existingMunicipalRows = $affectedIds === [] ? 0 : ResultadoMunicipal::query()->whereIn('candidatura_id', $affectedIds)->count();
            $existingZoneRows = $affectedIds === [] ? 0 : ResultadoZona::query()->whereIn('candidatura_id', $affectedIds)->count();

            $stats['estimativa_banco_bytes'] = $this->storage->estimateResults(count($municipal), count($zonal));
            $stats['estimativa_banco'] = $this->storage->human($stats['estimativa_banco_bytes']);
            $stats['resultados_existentes_substituidos'] = $existingMunicipalRows + $existingZoneRows;
            $existingEstimate = $this->storage->estimateResults($existingMunicipalRows, $existingZoneRows);
            $estimatedGrowth = max(0, $stats['estimativa_banco_bytes'] - $existingEstimate);
            $stats['crescimento_estimado_bytes'] = $estimatedGrowth;
            $stats['crescimento_estimado'] = $this->storage->human($estimatedGrowth);
            $storageAssessment = $this->storage->assertCanGrow($estimatedGrowth, $ignorarLimite);
            $stats['politica_antes'] = $this->storage->human($storageAssessment['politica_bytes']);
            $stats['politica_projetada'] = $this->storage->human($storageAssessment['projected_politica_bytes']);

            DB::transaction(function () use ($affectedIds, $municipal, $zonal, $totals, $situacoes): void {
                if ($affectedIds !== []) {
                    ResultadoMunicipal::query()->whereIn('candidatura_id', $affectedIds)->delete();
                    ResultadoZona::query()->whereIn('candidatura_id', $affectedIds)->delete();
                }

                $now = now();
                foreach (array_chunk(array_values($municipal), 1000) as $chunk) {
                    $rows = array_map(fn (array $r) => $r + [
                        'sincronizado_em' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], $chunk);
                    DB::table('politica_resultados_municipais')->insert($rows);
                }

                foreach (array_chunk(array_values($zonal), 1000) as $chunk) {
                    $rows = array_map(fn (array $r) => $r + [
                        'sincronizado_em' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], $chunk);
                    DB::table('politica_resultados_zonas')->insert($rows);
                }

                foreach ($totals as $candidaturaId => $votos) {
                    $situacao = $situacoes[$candidaturaId] ?? null;
                    Candidatura::query()->whereKey($candidaturaId)->update([
                        'votos_total' => $votos,
                        'situacao_eleicao' => $situacao,
                        'eleito' => $this->isEleito($situacao),
                        'sincronizado_em' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });

            $stats['resultados_municipais'] = count($municipal);
            $stats['resultados_zonas'] = count($zonal);
            $stats['candidaturas_atualizadas'] = count($totals);
            $stats['inseridos'] = count($municipal) + count($zonal);
            $this->finishImport($importacao, $stats);
            Cache::forget('politica:v2:dashboard:resumo');

            return $stats;
        } catch (Throwable $e) {
            $this->failImport($importacao, $stats, $e);
            throw $e;
        }
    }

    private function candidateRows(int $ano, string $uf, string $arquivo): iterable
    {
        $uf = strtoupper($uf);
        $selector = function (string $name) use ($ano, $uf): bool {
            $base = strtoupper(basename($name));
            if (! str_ends_with($base, '.CSV')) {
                return false;
            }

            return str_contains($base, "_{$uf}.CSV")
                || str_contains($base, '_BRASIL.CSV')
                || str_contains($base, '_BR.CSV')
                || $base === strtoupper("consulta_cand_{$ano}.csv");
        };

        return $this->reader->rows($arquivo, $selector);
    }

    private function rowCandidateMatchesUf(array $row, string $cargo, string $uf): bool
    {
        $rowUf = strtoupper(trim((string) ($row['SG_UF'] ?? '')));
        if ($cargo === 'Presidente') {
            return in_array($rowUf, ['BR', 'BRASIL'], true);
        }

        return $rowUf === strtoupper($uf);
    }

    private function rowCandidateInScope(array $row, string $cargo, string $escopo): bool
    {
        $escopo = strtolower($escopo);
        if ($escopo === 'todos') {
            return true;
        }

        $isPrioritario = $this->prioritarioSlugParaRow($row) !== null;
        if ($escopo === 'prioritarios') {
            return $isPrioritario;
        }

        $allCargos = (array) config('politica.tse.scope.todos', ['Governador', 'Presidente']);
        if (in_array($cargo, $allCargos, true)) {
            return true;
        }

        $partyOnlyCargos = (array) config('politica.tse.scope.somente_partido', [
            'Vereador', 'Prefeito', 'Deputado Estadual', 'Deputado Federal', 'Senador',
        ]);
        if (! in_array($cargo, $partyOnlyCargos, true)) {
            return $isPrioritario;
        }

        $prioridade = $this->normalize((string) config('politica.tse.scope.partido_prioritario', 'REPUBLICANOS'));
        $partido = $this->normalize((string) ($row['SG_PARTIDO'] ?? ''));

        return $partido === $prioridade || $isPrioritario;
    }

    private function resolverCargo(string $cargoNome, mixed $codigo): Cargo
    {
        $cargo = Cargo::query()->where('nome', $cargoNome)->first();
        $codigoNormalizado = $this->normalizeCode($codigo, 4);

        if ($cargo) {
            if (! $cargo->tse_codigo && $codigoNormalizado) {
                $cargo->tse_codigo = $codigoNormalizado;
                $cargo->save();
            }
            return $cargo;
        }

        return Cargo::query()->create([
            'tse_codigo' => $codigoNormalizado,
            'nome' => $cargoNome,
            'esfera' => in_array($cargoNome, ['Presidente', 'Senador', 'Deputado Federal'], true) ? 'federal'
                : (in_array($cargoNome, ['Governador', 'Deputado Estadual'], true) ? 'estadual' : 'municipal'),
            'abrangencia' => $cargoNome === 'Presidente' ? 'nacional'
                : (in_array($cargoNome, ['Prefeito', 'Vereador'], true) ? 'municipio' : 'uf'),
            'ativo' => true,
        ]);
    }

    private function resolverEleicao(int $ano, string $uf, string $cargo, array $row): Eleicao
    {
        $tipo = in_array($cargo, ['Prefeito', 'Vereador'], true) ? 'municipal' : 'geral';
        $turno = max(1, (int) ($row['NR_TURNO'] ?? 1));
        $codigo = $this->value($row, 'CD_ELEICAO');

        $eleicao = Eleicao::query()
            ->where('ano', $ano)
            ->where('turno', $turno)
            ->where('tipo', $tipo)
            ->where('uf', strtoupper($uf))
            ->first();

        if (! $eleicao && $codigo) {
            $eleicao = Eleicao::query()->where('tse_eleicao_codigo', $codigo)->first();
        }

        $data = $this->parseDate($this->value($row, 'DT_ELEICAO'));
        $payload = [
            'ano' => $ano,
            'turno' => $turno,
            'tipo' => $tipo,
            'descricao' => $this->value($row, 'DS_ELEICAO') ?: sprintf('Eleições %d - %dº turno', $ano, $turno),
            'data_eleicao' => $data,
            'uf' => strtoupper($uf),
            'status' => $ano < (int) date('Y') ? 'historica' : 'candidaturas',
        ];
        if ($codigo) {
            $payload['tse_eleicao_codigo'] = $codigo;
        }

        if ($eleicao) {
            $eleicao->fill(array_filter($payload, fn ($v) => $v !== null))->save();
            return $eleicao;
        }

        return Eleicao::query()->create($payload);
    }

    private function resolverPartido(array $row): ?Partido
    {
        $sigla = strtoupper(trim((string) ($row['SG_PARTIDO'] ?? '')));
        if ($sigla === '' || $sigla === '#NULO#' || $sigla === '-1') {
            return null;
        }

        $numero = $this->positiveInt($row['NR_PARTIDO'] ?? null);
        $partido = Partido::query()->where('sigla', $sigla)->first();
        if (! $partido && $numero) {
            $partido = Partido::query()->where('numero', $numero)->first();
        }
        $payload = [
            'numero' => $numero,
            'nome' => $this->value($row, 'NM_PARTIDO') ?: $sigla,
            'ativo' => true,
        ];

        if ($partido) {
            if ($partido->sigla !== $sigla && ! Partido::query()->where('sigla', $sigla)->exists()) {
                $partido->sigla = $sigla;
            }
            $partido->fill(array_filter($payload, fn ($v) => $v !== null))->save();
            return $partido;
        }

        return Partido::query()->create(['sigla' => $sigla] + $payload);
    }

    private function resolverPolitico(array $row, Cargo $cargo, Eleicao $eleicao, ?Cidade $cidade, array $prioritarios): Politico
    {
        $nomeCompleto = $this->value($row, 'NM_CANDIDATO') ?: $this->value($row, 'NM_URNA_CANDIDATO') ?: 'Candidato TSE';
        $nomePublico = $this->value($row, 'NM_URNA_CANDIDATO') ?: $nomeCompleto;
        $nascimento = $this->parseDate($this->value($row, 'DT_NASCIMENTO'));
        $hash = hash('sha256', $this->normalize($nomeCompleto).'|'.($nascimento?->format('Y-m-d') ?? ''));

        $slugPrioritario = $this->prioritarioSlugParaRow($row);
        if ($slugPrioritario && isset($prioritarios[$slugPrioritario])) {
            $politico = $prioritarios[$slugPrioritario];
        } else {
            $politico = Politico::query()->where('identidade_publica_hash', $hash)->first();
        }

        if (! $politico) {
            $politico = Candidatura::query()
                ->where('eleicao_id', $eleicao->id)
                ->where('cargo_id', $cargo->id)
                ->when($cidade, fn ($q) => $q->where('cidade_id', $cidade->id))
                ->with('politico')
                ->get()
                ->first(fn (Candidatura $cand) => $this->normalize((string) $cand->politico?->nome_completo) === $this->normalize($nomeCompleto))
                ?->politico;
        }

        if (! $politico) {
            $slug = Str::slug($nomePublico) ?: 'candidato';
            if (Politico::query()->where('slug', $slug)->exists()) {
                $slug .= '-'.($this->value($row, 'SQ_CANDIDATO') ?: substr($hash, 0, 10));
            }

            $politico = Politico::query()->create([
                'nome_completo' => $nomeCompleto,
                'nome_publico' => $nomePublico,
                'slug' => $slug,
                'identidade_publica_hash' => $hash,
                'data_nascimento' => $nascimento,
                'uf_nascimento' => $this->ufValue($row['SG_UF_NASCIMENTO'] ?? null),
                'ativo' => true,
            ]);
        } else {
            $politico->fill([
                'nome_completo' => $nomeCompleto,
                'identidade_publica_hash' => $politico->identidade_publica_hash ?: $hash,
                'data_nascimento' => $politico->data_nascimento ?: $nascimento,
                'uf_nascimento' => $politico->uf_nascimento ?: $this->ufValue($row['SG_UF_NASCIMENTO'] ?? null),
                'ativo' => true,
            ])->save();
        }

        return $politico;
    }

    private function resolverCidadeCandidatura(string $cargo, array $row, array &$cityMap): ?Cidade
    {
        if (! in_array($cargo, ['Prefeito', 'Vereador'], true)) {
            return null;
        }

        $name = $this->value($row, 'NM_UE') ?: $this->value($row, 'NM_MUNICIPIO');
        $cidade = $name ? ($cityMap[$this->normalize($name)] ?? null) : null;
        if ($cidade && ! $cidade->tse_codigo) {
            $codigo = $this->value($row, 'SG_UE') ?: $this->value($row, 'CD_MUNICIPIO');
            if ($codigo && $codigo !== '-1') {
                $cidade->tse_codigo = $codigo;
                $cidade->save();
            }
        }

        return $cidade;
    }

    private function resolverCidadeResultado(array $row, array &$cityMap): ?Cidade
    {
        $codigo = $this->value($row, 'CD_MUNICIPIO');
        if ($codigo && isset($cityMap['tse:'.$codigo])) {
            return $cityMap['tse:'.$codigo];
        }

        $name = $this->value($row, 'NM_MUNICIPIO');
        $cidade = $name ? ($cityMap[$this->normalize($name)] ?? null) : null;
        if ($cidade && $codigo && $codigo !== '-1') {
            if (! $cidade->tse_codigo) {
                $cidade->tse_codigo = $codigo;
                $cidade->save();
            }
            $cityMap['tse:'.$codigo] = $cidade;
        }

        return $cidade;
    }

    private function resolverCandidaturaResultado(int $ano, string $uf, string $cargoNome, array $row, Candidatura $template): Candidatura
    {
        $eleicao = $this->resolverEleicao($ano, $uf, $cargoNome, $row);
        if ((int) $template->eleicao_id === (int) $eleicao->id) {
            return $template;
        }

        $existing = Candidatura::query()
            ->where('eleicao_id', $eleicao->id)
            ->where('tse_sq_candidato', $template->tse_sq_candidato)
            ->first();
        if ($existing) {
            return $existing;
        }

        return Candidatura::query()->create([
            'politico_id' => $template->politico_id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $template->cargo_id,
            'partido_id' => $template->partido_id,
            'tse_sq_candidato' => $template->tse_sq_candidato,
            'numero_urna' => $template->numero_urna,
            'nome_urna' => $template->nome_urna,
            'uf' => $template->uf,
            'cidade_id' => $template->cidade_id,
            'origem' => 'tse_dados_abertos',
            'origem_chave' => implode(':', ['tse', $ano, $eleicao->tse_eleicao_codigo ?: $eleicao->id, $template->tse_sq_candidato]),
            'situacao_registro' => $template->situacao_registro,
            'coligacao' => $template->coligacao,
            'federacao' => $template->federacao,
            'segundo_turno' => true,
            'sincronizado_em' => now(),
        ]);
    }

    private function candidateSqSet(int $ano, string $escopo): array
    {
        return array_fill_keys(array_keys($this->candidateTemplatesBySq($ano, $escopo)), true);
    }

    /** @return array<string,Candidatura> */
    private function candidateTemplatesBySq(int $ano, string $escopo): array
    {
        $query = Candidatura::query()
            ->with(['cargo', 'partido', 'eleicao', 'politico.acompanhamento'])
            ->whereNotNull('tse_sq_candidato')
            ->whereHas('eleicao', fn ($q) => $q->where('ano', $ano));

        $items = $query->get();
        $map = [];
        foreach ($items as $item) {
            if ($escopo === 'todos' || $this->candidaturaInScope($item, $escopo)) {
                $map[(string) $item->tse_sq_candidato] ??= $item;
            }
        }

        return $map;
    }

    private function candidaturaInScope(Candidatura $candidatura, string $escopo): bool
    {
        if ($escopo === 'prioritarios') {
            return $candidatura->politico?->acompanhamento?->ativo === true;
        }

        $cargo = $candidatura->cargo?->nome;
        if (in_array($cargo, (array) config('politica.tse.scope.todos', ['Governador', 'Presidente']), true)) {
            return true;
        }

        if (in_array($cargo, (array) config('politica.tse.scope.somente_partido', [
            'Vereador', 'Prefeito', 'Deputado Estadual', 'Deputado Federal', 'Senador',
        ]), true)) {
            return $this->normalize((string) $candidatura->partido?->sigla)
                === $this->normalize((string) config('politica.tse.scope.partido_prioritario', 'REPUBLICANOS'))
                || $candidatura->politico?->acompanhamento?->ativo === true;
        }

        return false;
    }

    private function cityMap(): array
    {
        $map = [];
        foreach (Cidade::query()->whereNotNull('ibge_code')->get() as $cidade) {
            $map[$this->normalize($cidade->nome)] = $cidade;
            if ($cidade->tse_codigo) {
                $map['tse:'.$cidade->tse_codigo] = $cidade;
            }
        }

        return $map;
    }

    /** @return array<string,Politico> */
    private function prioritarioAliases(): array
    {
        $slugs = array_keys((array) config('politica.tse.prioritarios_aliases', []));
        if ($slugs === []) {
            return [];
        }

        return Politico::query()->whereIn('slug', $slugs)->get()->keyBy('slug')->all();
    }

    private function prioritarioSlugParaRow(array $row): ?string
    {
        $candidateNames = array_filter([
            $this->normalize((string) ($row['NM_CANDIDATO'] ?? '')),
            $this->normalize((string) ($row['NM_URNA_CANDIDATO'] ?? '')),
        ]);

        foreach ((array) config('politica.tse.prioritarios_aliases', []) as $slug => $aliases) {
            foreach ((array) $aliases as $alias) {
                if (in_array($this->normalize((string) $alias), $candidateNames, true)) {
                    return (string) $slug;
                }
            }
        }

        return null;
    }

    private function cargoCanonico(mixed $cargo): ?string
    {
        $map = [
            'PRESIDENTE' => 'Presidente',
            'GOVERNADOR' => 'Governador',
            'SENADOR' => 'Senador',
            'DEPUTADO FEDERAL' => 'Deputado Federal',
            'DEPUTADO ESTADUAL' => 'Deputado Estadual',
            'DEPUTADO DISTRITAL' => 'Deputado Estadual',
            'PREFEITO' => 'Prefeito',
            'VEREADOR' => 'Vereador',
        ];

        return $map[$this->normalize((string) $cargo)] ?? null;
    }

    private function normalize(string $value): string
    {
        $value = strtoupper(Str::ascii(trim($value)));
        $normalized = preg_replace('/[^A-Z0-9]+/', ' ', $value);

        return trim($normalized ?? $value);
    }

    private function normalizeCode(mixed $value, int $pad): ?string
    {
        if ($value === null || trim((string) $value) === '' || (string) $value === '-1') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);
        return $digits === '' ? null : str_pad($digits, $pad, '0', STR_PAD_LEFT);
    }

    private function positiveInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }
        $int = (int) $value;
        return $int > 0 ? $int : null;
    }

    private function value(array $row, string $key): ?string
    {
        $value = trim((string) ($row[$key] ?? ''));
        return $value === '' || $value === '-1' || strtoupper($value) === '#NULO#' ? null : $value;
    }

    private function ufValue(mixed $value): ?string
    {
        $uf = strtoupper(trim((string) $value));
        return preg_match('/^[A-Z]{2}$/', $uf) ? $uf : null;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->startOfDay();
            } catch (Throwable) {
                // tenta o próximo formato
            }
        }

        return null;
    }

    private function isEleito(?string $situacao): bool
    {
        if (! $situacao) {
            return false;
        }
        $normalized = $this->normalize($situacao);
        return str_contains($normalized, 'ELEITO') && ! str_contains($normalized, 'NAO ELEITO');
    }

    private function baseStats(int $ano, string $uf, string $tipo, string $escopo, string $arquivo): array
    {
        return [
            'ano' => $ano,
            'uf' => strtoupper($uf),
            'tipo' => $tipo,
            'escopo' => $escopo,
            'arquivo' => basename($arquivo),
            'linhas_lidas' => 0,
            'linhas_selecionadas' => 0,
            'inseridos' => 0,
            'atualizados' => 0,
            'ignorados' => 0,
        ];
    }

    private function beginImport(int $ano, string $uf, string $tipo, string $escopo, string $arquivo): TseImportacao
    {
        return TseImportacao::query()->create([
            'execucao' => (string) Str::uuid(),
            'ano' => $ano,
            'uf' => strtoupper($uf),
            'tipo' => $tipo,
            'escopo' => $escopo,
            'status' => 'executando',
            'arquivo' => $arquivo,
            'sha256' => is_file($arquivo) ? hash_file('sha256', $arquivo) : null,
            'iniciada_em' => now(),
        ]);
    }

    private function finishImport(TseImportacao $importacao, array $stats): void
    {
        $importacao->update([
            'status' => 'concluida',
            'linhas_lidas' => $stats['linhas_lidas'] ?? 0,
            'linhas_selecionadas' => $stats['linhas_selecionadas'] ?? 0,
            'inseridos' => $stats['inseridos'] ?? 0,
            'atualizados' => $stats['atualizados'] ?? 0,
            'ignorados' => $stats['ignorados'] ?? 0,
            'concluida_em' => now(),
            'meta' => $stats,
        ]);
    }

    private function failImport(TseImportacao $importacao, array $stats, Throwable $e): void
    {
        $importacao->update([
            'status' => 'falhou',
            'linhas_lidas' => $stats['linhas_lidas'] ?? 0,
            'linhas_selecionadas' => $stats['linhas_selecionadas'] ?? 0,
            'inseridos' => $stats['inseridos'] ?? 0,
            'atualizados' => $stats['atualizados'] ?? 0,
            'ignorados' => $stats['ignorados'] ?? 0,
            'concluida_em' => now(),
            'ultimo_erro' => Str::limit($e->getMessage(), 5000, ''),
            'meta' => $stats,
        ]);
    }
}
