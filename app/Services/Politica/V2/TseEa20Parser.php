<?php

namespace App\Services\Politica\V2;

class TseEa20Parser
{
    public function parse(array $payload): array
    {
        $candidatos = collect($this->findCandidateRows($payload))
            ->map(function (array $item): array {
                return [
                    'sq_candidato' => $this->string($item, ['sqcand', 'sq', 'sq_candidato']),
                    'numero' => $this->string($item, ['n', 'nr', 'numero']),
                    'nome' => $this->string($item, ['nm', 'nome', 'nome_urna']),
                    'partido' => $this->string($item, ['cc', 'sgp', 'partido']),
                    'votos' => $this->inteiro($this->valor($item, ['vap', 'nv', 'votos', 'v'])),
                    'percentual' => $this->decimal($this->valor($item, ['pvapn', 'pvap', 'percentual', 'p'])),
                    'eleito' => strtolower((string) $this->valor($item, ['e', 'eleito'])) === 's',
                    'situacao' => $this->string($item, ['st', 'sit', 'situacao']),
                ];
            })
            ->filter(fn (array $item) => filled($item['sq_candidato']))
            ->sortByDesc('votos')
            ->values()
            ->map(function (array $item, int $indice): array {
                $item['posicao'] = $indice + 1;
                return $item;
            })
            ->all();

        $secoes = $this->findObjectWithKeys($payload, ['ts', 'st', 'snt']);
        $eleitores = $this->findObjectWithKeys($payload, ['te', 'c', 'a']);
        $andamento = $this->firstRecursive($payload, 'and');

        return [
            'eleicao' => (string) ($payload['ele'] ?? ''),
            'idg' => isset($payload['idg']) ? (string) $payload['idg'] : null,
            'gerado_em' => $this->dataHora($payload['dg'] ?? null, $payload['hg'] ?? null),
            'andamento' => is_scalar($andamento) ? (string) $andamento : null,
            'secoes_total' => $this->inteiro($secoes['ts'] ?? null),
            'secoes_totalizadas' => $this->inteiro($secoes['st'] ?? null),
            'secoes_nao_totalizadas' => $this->inteiro($secoes['snt'] ?? null),
            'percentual_secoes' => $this->decimal($secoes['pstn'] ?? $secoes['pst'] ?? null),
            'eleitores_total' => $this->inteiro($eleitores['te'] ?? null),
            'comparecimento' => $this->inteiro($eleitores['c'] ?? null),
            'abstencoes' => $this->inteiro($eleitores['a'] ?? null),
            'candidatos' => $candidatos,
        ];
    }

    private function findCandidateRows(array $node): array
    {
        if (isset($node['cand']) && is_array($node['cand'])) {
            return array_values(array_filter($node['cand'], 'is_array'));
        }

        foreach ($node as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (array_is_list($value)) {
                foreach ($value as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $encontrados = $this->findCandidateRows($item);
                    if ($encontrados !== []) {
                        return $encontrados;
                    }
                }
                continue;
            }

            $encontrados = $this->findCandidateRows($value);
            if ($encontrados !== []) {
                return $encontrados;
            }
        }

        return [];
    }

    private function findObjectWithKeys(array $node, array $keys): array
    {
        if (collect($keys)->every(fn (string $key) => array_key_exists($key, $node))) {
            return $node;
        }

        foreach ($node as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (array_is_list($value)) {
                foreach ($value as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $encontrado = $this->findObjectWithKeys($item, $keys);
                    if ($encontrado !== []) {
                        return $encontrado;
                    }
                }
                continue;
            }

            $encontrado = $this->findObjectWithKeys($value, $keys);
            if ($encontrado !== []) {
                return $encontrado;
            }
        }

        return [];
    }

    private function firstRecursive(array $node, string $key): mixed
    {
        if (array_key_exists($key, $node)) {
            return $node[$key];
        }

        foreach ($node as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (array_is_list($value)) {
                foreach ($value as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $found = $this->firstRecursive($item, $key);
                    if ($found !== null) {
                        return $found;
                    }
                }
                continue;
            }

            $found = $this->firstRecursive($value, $key);
            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }

    private function valor(array $item, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $item)) {
                return $item[$key];
            }
        }

        return null;
    }

    private function string(array $item, array $keys): ?string
    {
        $valor = $this->valor($item, $keys);

        if ($valor === null || ! is_scalar($valor)) {
            return null;
        }

        $valor = trim((string) $valor);
        return $valor !== '' ? $valor : null;
    }

    private function inteiro(mixed $valor): int
    {
        if ($valor === null || $valor === '') {
            return 0;
        }

        return (int) preg_replace('/[^0-9-]/', '', (string) $valor);
    }

    private function decimal(mixed $valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $normalizado = str_replace(['%', ','], ['', '.'], trim((string) $valor));
        return is_numeric($normalizado) ? (float) $normalizado : null;
    }

    private function dataHora(mixed $data, mixed $hora): ?string
    {
        if (! is_string($data) || trim($data) === '' || ! is_string($hora) || trim($hora) === '') {
            return null;
        }

        return trim($data).' '.trim($hora);
    }
}
