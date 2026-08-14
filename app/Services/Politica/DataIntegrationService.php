<?php

namespace App\Services\Politica;

use App\Models\Politica\Cidade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class DataIntegrationService
{
    protected string $ibgeApiUrl;
    protected string $ibgeMalhasUrl;
    protected int $ibgeUfCode;
    protected int $expectedMunicipalities;

    public function __construct()
    {
        $this->ibgeApiUrl = rtrim((string) config(
            'politica.data_sources.ibge.url',
            'https://servicodados.ibge.gov.br/api/v1/'
        ), '/') . '/';

        $this->ibgeMalhasUrl = rtrim((string) config(
            'politica.data_sources.ibge.malhas_url',
            'https://servicodados.ibge.gov.br/api/v3/malhas/municipios'
        ), '/');

        $this->ibgeUfCode = (int) config('politica.data_sources.ibge.uf_code', 29);
        $this->expectedMunicipalities = (int) config('politica.data_sources.ibge.expected_municipalities', 417);
    }

    /**
     * Sincroniza dados básicos das cidades via API do IBGE.
     */
    public function syncCityData(): void
    {
        Log::info('Iniciando sincronização de dados das cidades...');

        Cidade::query()
            ->whereNotNull('ibge_code')
            ->each(function (Cidade $cidade): void {
                try {
                    $response = Http::get("{$this->ibgeApiUrl}localidades/municipios/{$cidade->ibge_code}/agregados");
                    if ($response->successful()) {
                        $cidade->touch();
                    }
                } catch (\Throwable $e) {
                    Log::error("Erro ao sincronizar dados para a cidade {$cidade->nome}: {$e->getMessage()}");
                }
            });

        Log::info('Sincronização de cidades concluída.');
    }

    /**
     * Reconcilia a tabela local com a lista oficial de municípios da UF no IBGE.
     *
     * Não apaga registros legados/duplicados: apenas retira deles o ibge_code. Isso preserva
     * FKs históricas e permite revisão posterior sem contaminar o mapa oficial.
     */
    public function reconcileOfficialMunicipalities(): array
    {
        $url = "{$this->ibgeApiUrl}localidades/estados/{$this->ibgeUfCode}/municipios";

        $response = Http::retry(3, 500)
            ->timeout(30)
            ->acceptJson()
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("IBGE retornou HTTP {$response->status()} ao consultar municípios oficiais.");
        }

        $oficiais = collect($response->json())
            ->filter(fn ($item) => is_array($item) && isset($item['id'], $item['nome']))
            ->map(fn ($item) => [
                'id' => (int) $item['id'],
                'nome' => (string) $item['nome'],
            ])
            ->values();

        if ($oficiais->count() !== $this->expectedMunicipalities) {
            throw new RuntimeException(sprintf(
                'A lista do IBGE retornou %d município(s); eram esperados %d. Nenhuma alteração foi feita.',
                $oficiais->count(),
                $this->expectedMunicipalities
            ));
        }

        $aliases = collect((array) config('politica.data_sources.ibge.name_aliases', []))
            ->mapWithKeys(fn ($destino, $origem) => [
                $this->municipalityKey((string) $origem) => $this->municipalityKey((string) $destino),
            ]);

        $locais = Cidade::query()->orderBy('id')->get();
        $locaisPorNome = $locais->groupBy(function (Cidade $cidade) use ($aliases): string {
            $key = $this->municipalityKey($cidade->nome);
            return (string) ($aliases->get($key) ?: $key);
        });

        $atribuicoes = [];
        $naoEncontrados = [];

        foreach ($oficiais as $oficial) {
            $key = $this->municipalityKey($oficial['nome']);
            $candidatos = $locaisPorNome->get($key, collect());

            if ($candidatos->isEmpty()) {
                $naoEncontrados[] = $oficial;
                continue;
            }

            // Preserva o registro histórico que já era tratado como município oficial.
            // Duplicatas auxiliares geralmente vieram sem ibge_code e ficam fora do mapa.
            $cidade = $candidatos
                ->sort(function (Cidade $a, Cidade $b): int {
                    $aSemCodigo = $a->ibge_code === null ? 1 : 0;
                    $bSemCodigo = $b->ibge_code === null ? 1 : 0;
                    return [$aSemCodigo, $a->id] <=> [$bSemCodigo, $b->id];
                })
                ->first();

            $atribuicoes[] = [
                'cidade_id' => $cidade->id,
                'codigo_atual' => $cidade->ibge_code !== null ? (int) $cidade->ibge_code : null,
                'codigo_oficial' => $oficial['id'],
                'nome_atual' => $cidade->nome,
                'nome_oficial' => mb_strtoupper($oficial['nome'], 'UTF-8'),
            ];
        }

        if ($naoEncontrados !== []) {
            throw new RuntimeException(
                'Há municípios oficiais do IBGE sem correspondência local: ' .
                collect($naoEncontrados)->pluck('nome')->implode(', ') .
                '. Nenhuma alteração foi feita.'
            );
        }

        $idsCanonicos = collect($atribuicoes)->pluck('cidade_id')->map(fn ($id) => (int) $id)->all();
        $idsAlterados = collect($atribuicoes)
            ->filter(fn ($item) => $item['codigo_atual'] !== $item['codigo_oficial'])
            ->pluck('cidade_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        DB::transaction(function () use ($atribuicoes): void {
            // Evita colisões temporárias da UNIQUE KEY enquanto códigos incorretos são realocados.
            Cidade::query()->whereNotNull('ibge_code')->update(['ibge_code' => null]);

            foreach ($atribuicoes as $item) {
                Cidade::query()
                    ->whereKey($item['cidade_id'])
                    ->update([
                        'nome' => $item['nome_oficial'],
                        'ibge_code' => $item['codigo_oficial'],
                    ]);
            }
        });

        Cache::forget('politica:v2:dashboard:resumo');

        $extras = Cidade::query()
            ->whereNotIn('id', $idsCanonicos)
            ->orderBy('nome')
            ->pluck('nome')
            ->all();

        $totalOficialLocal = Cidade::query()->whereNotNull('ibge_code')->count();
        if ($totalOficialLocal !== $this->expectedMunicipalities) {
            throw new RuntimeException(
                "Reconciliação terminou com {$totalOficialLocal} municípios oficiais locais; esperado: {$this->expectedMunicipalities}."
            );
        }

        return [
            'oficiais' => $oficiais->count(),
            'codigos_corrigidos' => count($idsAlterados),
            'changed_ids' => $idsAlterados,
            'extras' => $extras,
        ];
    }

    public function syncCityCoordinates(Command $command, array $forceCityIds = []): void
    {
        $forceCityIds = collect($forceCityIds)->map(fn ($id) => (int) $id)->unique()->values()->all();

        $command->info('Buscando coordenadas geográficas no IBGE (Malhas v3)...');

        $cidades = Cidade::query()
            ->whereNotNull('ibge_code')
            ->where(function ($query) use ($forceCityIds): void {
                $query->whereNull('latitude')->orWhereNull('longitude');
                if ($forceCityIds !== []) {
                    $query->orWhereIn('id', $forceCityIds);
                }
            })
            ->orderBy('nome')
            ->get();

        if ($cidades->isEmpty()) {
            $command->info('Todos os municípios oficiais já possuem coordenadas compatíveis com seus códigos IBGE.');
            return;
        }

        if ($forceCityIds !== []) {
            $command->line(sprintf(
                '%d município(s) terão coordenadas recalculadas porque o código IBGE foi corrigido.',
                $cidades->whereIn('id', $forceCityIds)->count()
            ));
        }

        $progressBar = $command->getOutput()->createProgressBar($cidades->count());
        $progressBar->start();

        foreach ($cidades as $cidade) {
            try {
                $response = Http::retry(2, 300)
                    ->timeout(20)
                    ->acceptJson()
                    ->get("{$this->ibgeMalhasUrl}/{$cidade->ibge_code}", [
                        'formato' => 'application/vnd.geo+json',
                        'qualidade' => 'minima',
                    ]);

                if (! $response->successful()) {
                    $command->getOutput()->writeln("\n<fg=yellow>Aviso: IBGE retornou HTTP {$response->status()} para {$cidade->nome}.</>");
                    $progressBar->advance();
                    continue;
                }

                $centro = $this->centroGeoJson($response->json());
                if ($centro === null) {
                    $command->getOutput()->writeln("\n<fg=yellow>Aviso: malha sem coordenadas utilizáveis para {$cidade->nome}.</>");
                    $progressBar->advance();
                    continue;
                }

                [$longitude, $latitude] = $centro;
                $cidade->update([
                    'latitude' => round($latitude, 8),
                    'longitude' => round($longitude, 8),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Falha ao buscar coordenadas no IBGE.', [
                    'cidade_id' => $cidade->id,
                    'ibge_code' => $cidade->ibge_code,
                    'erro' => $e->getMessage(),
                ]);
                $command->getOutput()->writeln("\n<fg=red>Falha para {$cidade->nome}: {$e->getMessage()}</>");
            }

            $progressBar->advance();
            usleep(120000);
        }

        $progressBar->finish();
        $restantes = Cidade::query()
            ->whereNotNull('ibge_code')
            ->where(fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'))
            ->count();

        $command->newLine(2);
        $command->info($restantes === 0
            ? 'Coordenadas concluídas para todos os municípios oficiais.'
            : "Busca concluída; {$restantes} município(s) ainda sem coordenadas.");
    }

    private function municipalityKey(string $nome): string
    {
        $ascii = Str::upper(Str::ascii(trim($nome)));
        return (string) preg_replace('/[^A-Z0-9]+/', '', $ascii);
    }

    /**
     * Calcula um ponto central leve a partir do envelope da malha GeoJSON.
     */
    private function centroGeoJson(mixed $geoJson): ?array
    {
        $coordenadas = data_get($geoJson, 'features.0.geometry.coordinates')
            ?? data_get($geoJson, 'geometry.coordinates')
            ?? data_get($geoJson, 'coordinates');

        if (! is_array($coordenadas)) {
            return null;
        }

        $minLng = $minLat = INF;
        $maxLng = $maxLat = -INF;
        $encontrou = false;

        $percorrer = function (array $node) use (&$percorrer, &$minLng, &$minLat, &$maxLng, &$maxLat, &$encontrou): void {
            if (isset($node[0], $node[1]) && is_numeric($node[0]) && is_numeric($node[1])) {
                $lng = (float) $node[0];
                $lat = (float) $node[1];
                $minLng = min($minLng, $lng);
                $maxLng = max($maxLng, $lng);
                $minLat = min($minLat, $lat);
                $maxLat = max($maxLat, $lat);
                $encontrou = true;
                return;
            }

            foreach ($node as $item) {
                if (is_array($item)) {
                    $percorrer($item);
                }
            }
        };

        $percorrer($coordenadas);

        if (! $encontrou) {
            return null;
        }

        return [($minLng + $maxLng) / 2, ($minLat + $maxLat) / 2];
    }
}
