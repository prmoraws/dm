<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Filiacao;
use App\Models\Politica\V2\Mandato;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use Illuminate\Support\Collection;

class PoliticaHistoricoOficialService
{
    /** @return array<int,string> */
    public function slugs(): array
    {
        return array_values(array_filter((array) config('politica.tse.historico_especial.slugs', [])));
    }

    /** @return array<int,int> */
    public function anos(): array
    {
        $anos = array_map('intval', (array) config('politica.tse.historico_especial.anos', []));
        $anos = array_values(array_unique(array_filter($anos, fn (int $ano) => $ano >= 1990 && $ano <= ((int) date('Y') + 1))));
        sort($anos);

        return $anos;
    }

    public function isEspecial(Politico|string $politico): bool
    {
        $slug = $politico instanceof Politico ? $politico->slug : $politico;

        return in_array($slug, $this->slugs(), true);
    }

    public function sincronizarMandatosInstitucionais(): array
    {
        $config = (array) config('politica.tse.historico_especial.mandatos', []);
        $obsoletos = (array) config('politica.tse.historico_especial.mandatos_obsoletos', []);
        $stats = [
            'politicos' => 0,
            'registros_configurados' => 0,
            'inseridos' => 0,
            'atualizados' => 0,
            'removidos' => 0,
            'ignorados' => 0,
        ];

        foreach ($this->slugs() as $slug) {
            $politico = Politico::query()->where('slug', $slug)->first();
            if (! $politico) {
                $stats['ignorados']++;
                continue;
            }

            $stats['politicos']++;
            $stats['removidos'] += $this->removerMandatosObsoletos(
                $politico,
                (array) ($obsoletos[$slug] ?? [])
            );

            foreach ((array) ($config[$slug] ?? []) as $item) {
                $stats['registros_configurados']++;
                $cargoNome = trim((string) ($item['cargo'] ?? ''));
                if ($cargoNome === '') {
                    $stats['ignorados']++;
                    continue;
                }

                $cargo = Cargo::query()->firstOrCreate(
                    ['nome' => $cargoNome],
                    [
                        'esfera' => $item['esfera'] ?? null,
                        'abrangencia' => ($item['esfera'] ?? null) === 'municipal' ? 'municipio' : 'uf',
                        'ordem' => 900,
                        'ativo' => true,
                    ]
                );

                $partido = $this->partidoPorSigla($item['partido'] ?? null);

                $match = [
                    'politico_id' => $politico->id,
                    'cargo_id' => $cargo->id,
                    'ano_inicio' => isset($item['ano_inicio']) ? (int) $item['ano_inicio'] : null,
                    'ano_fim' => isset($item['ano_fim']) ? (int) $item['ano_fim'] : null,
                    'fonte' => $item['fonte'] ?? null,
                ];

                $payload = [
                    'partido_id' => $partido?->id,
                    'fonte_id' => $item['fonte_id'] ?? null,
                    'tipo' => $item['tipo'] ?? 'mandato',
                    'legislatura' => $item['legislatura'] ?? null,
                    'esfera' => $item['esfera'] ?? null,
                    'uf' => $item['uf'] ?? null,
                    'periodo_texto' => $item['periodo_texto'] ?? null,
                    'data_inicio' => $item['data_inicio'] ?? null,
                    'data_fim' => $item['data_fim'] ?? null,
                    'situacao' => $item['situacao'] ?? 'concluido',
                    'detalhes' => $item['detalhes'] ?? null,
                    'fonte_url' => $item['fonte_url'] ?? null,
                    'fonte_oficial' => true,
                ];

                $existing = Mandato::query()->where($match)->first();
                if ($existing) {
                    $existing->fill($payload)->save();
                    $stats['atualizados']++;
                } else {
                    Mandato::query()->create($match + $payload);
                    $stats['inseridos']++;
                }
            }
        }

        return $stats;
    }

    public function sincronizarFiliacoesInstitucionais(): array
    {
        $config = (array) config('politica.tse.historico_especial.filiacoes', []);
        $stats = [
            'politicos' => 0,
            'registros_configurados' => 0,
            'inseridos' => 0,
            'atualizados' => 0,
            'ignorados' => 0,
        ];

        foreach ($this->slugs() as $slug) {
            $itens = (array) ($config[$slug] ?? []);
            if ($itens === []) {
                continue;
            }

            $politico = Politico::query()->where('slug', $slug)->first();
            if (! $politico) {
                $stats['ignorados'] += count($itens);
                continue;
            }

            $stats['politicos']++;

            foreach ($itens as $item) {
                $stats['registros_configurados']++;
                $partido = $this->partidoPorSigla($item['partido'] ?? null);
                if (! $partido) {
                    $stats['ignorados']++;
                    continue;
                }

                $match = [
                    'politico_id' => $politico->id,
                    'partido_id' => $partido->id,
                    'ano_inicio' => isset($item['ano_inicio']) ? (int) $item['ano_inicio'] : null,
                    'ano_fim' => isset($item['ano_fim']) ? (int) $item['ano_fim'] : null,
                    'fonte' => $item['fonte'] ?? null,
                ];

                $payload = [
                    'data_inicio' => $item['data_inicio'] ?? null,
                    'data_fim' => $item['data_fim'] ?? null,
                    'periodo_texto' => $item['periodo_texto'] ?? null,
                    'observacoes' => $item['observacoes'] ?? null,
                    'fonte_id' => $item['fonte_id'] ?? null,
                    'fonte_url' => $item['fonte_url'] ?? null,
                    'fonte_oficial' => true,
                ];

                $existing = Filiacao::query()->where($match)->first();
                if ($existing) {
                    $existing->fill($payload)->save();
                    $stats['atualizados']++;
                } else {
                    Filiacao::query()->create($match + $payload);
                    $stats['inseridos']++;
                }
            }
        }

        return $stats;
    }

    public function notaFiliacoes(Politico $politico): ?string
    {
        $nota = config('politica.tse.historico_especial.filiacoes_notas.'.$politico->slug);

        return is_string($nota) && trim($nota) !== '' ? trim($nota) : null;
    }

    /** @param array<int,array<string,mixed>> $itens */
    private function removerMandatosObsoletos(Politico $politico, array $itens): int
    {
        $removidos = 0;

        foreach ($itens as $item) {
            $cargoNome = trim((string) ($item['cargo'] ?? ''));
            if ($cargoNome === '') {
                continue;
            }

            $cargo = Cargo::query()->where('nome', $cargoNome)->first();
            if (! $cargo) {
                continue;
            }

            $query = Mandato::query()
                ->where('politico_id', $politico->id)
                ->where('cargo_id', $cargo->id)
                ->where('ano_inicio', isset($item['ano_inicio']) ? (int) $item['ano_inicio'] : null)
                ->where('ano_fim', isset($item['ano_fim']) ? (int) $item['ano_fim'] : null);

            if (array_key_exists('fonte', $item)) {
                $query->where('fonte', $item['fonte']);
            }

            $removidos += $query->delete();
        }

        return $removidos;
    }

    private function partidoPorSigla(mixed $valor): ?Partido
    {
        $sigla = strtoupper(trim((string) $valor));
        if ($sigla === '') {
            return null;
        }

        return Partido::query()->firstOrCreate(
            ['sigla' => $sigla],
            ['nome' => $sigla, 'ativo' => true]
        );
    }

    /** @return Collection<int,array<string,mixed>> */
    public function linhaDoTempo(Politico $politico): Collection
    {
        if (! $this->isEspecial($politico)) {
            return collect();
        }

        $eventos = collect();

        foreach ($politico->candidaturas as $candidatura) {
            $ano = (int) ($candidatura->eleicao?->ano ?? 0);
            $temResultado = (($candidatura->resultados_municipais_count ?? 0) > 0)
                || (($candidatura->resultados_zonas_count ?? 0) > 0)
                || filled($candidatura->situacao_eleicao);

            $eventos->push([
                'tipo' => 'eleicao',
                'ano' => $ano,
                'ordem' => 20,
                'titulo' => trim(($candidatura->cargo?->nome ?: 'Candidatura').($candidatura->partido?->sigla ? ' · '.$candidatura->partido->sigla : '')),
                'periodo' => $ano ? (string) $ano : '—',
                'votos' => $temResultado ? (int) $candidatura->votos_total : null,
                'resultado' => $candidatura->situacao_eleicao,
                'eleito' => (bool) $candidatura->eleito,
                'detalhes' => $candidatura->nome_urna ? 'Nome de urna: '.$candidatura->nome_urna : null,
                'fonte' => $candidatura->isRegistroOficialTse() ? 'Dados Abertos TSE' : ($candidatura->origem ?: 'Origem não informada'),
                'fonte_url' => $candidatura->isRegistroOficialTse() ? 'https://dadosabertos.tse.jus.br/' : null,
                'oficial' => $candidatura->isRegistroOficialTse(),
            ]);
        }

        foreach ($politico->mandatos as $mandato) {
            $eventos->push([
                'tipo' => $mandato->tipo ?: 'mandato',
                'ano' => (int) ($mandato->ano_inicio ?: $mandato->data_inicio?->year ?: 0),
                'ordem' => 10,
                'titulo' => trim(($mandato->cargo?->nome ?: 'Mandato').($mandato->partido?->sigla ? ' · '.$mandato->partido->sigla : '')),
                'periodo' => $mandato->periodoExibicao(),
                'votos' => null,
                'resultado' => $mandato->situacao,
                'eleito' => null,
                'detalhes' => $mandato->detalhes,
                'fonte' => $mandato->fonteLabel(),
                'fonte_url' => $mandato->fonte_url,
                'oficial' => (bool) $mandato->fonte_oficial,
            ]);
        }

        return $eventos
            ->sortByDesc(fn (array $evento) => sprintf('%04d-%02d', $evento['ano'], $evento['ordem']))
            ->values();
    }
}
