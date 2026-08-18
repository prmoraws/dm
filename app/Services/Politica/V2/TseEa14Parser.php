<?php

namespace App\Services\Politica\V2;

class TseEa14Parser
{
    public function parse(array $payload, string $uf = 'BA'): array
    {
        $abrangencias = collect($payload['abr'] ?? []);
        $uf = strtolower($uf);

        return [
            'eleicao' => (string) ($payload['ele'] ?? ''),
            'turno' => isset($payload['t']) ? (int) $payload['t'] : null,
            'fase' => $payload['f'] ?? null,
            'idg' => isset($payload['idg']) ? (string) $payload['idg'] : null,
            'gerado_em' => $this->dataHora($payload['dg'] ?? null, $payload['hg'] ?? null),
            'br' => $this->abrangencia($abrangencias->first(fn ($item) => ($item['tpabr'] ?? null) === 'br')),
            'uf' => $this->abrangencia($abrangencias->first(fn ($item) => strtolower((string) ($item['cdabr'] ?? '')) === $uf)),
        ];
    }

    private function abrangencia(?array $item): ?array
    {
        if (! $item) {
            return null;
        }

        $secoes = is_array($item['s'] ?? null) ? $item['s'] : [];
        $eleitores = is_array($item['e'] ?? null) ? $item['e'] : [];

        return [
            'andamento' => $item['and'] ?? null,
            'tipo' => $item['tpabr'] ?? null,
            'chave' => $item['cdabr'] ?? null,
            'ultima_totalizacao' => $this->dataHora($item['dt'] ?? null, $item['ht'] ?? null),
            'secoes_total' => $this->inteiro($secoes['ts'] ?? null),
            'secoes_totalizadas' => $this->inteiro($secoes['st'] ?? null),
            'secoes_nao_totalizadas' => $this->inteiro($secoes['snt'] ?? null),
            'percentual_secoes' => $this->decimal($secoes['pstn'] ?? $secoes['pst'] ?? null),
            'eleitores_total' => $this->inteiro($eleitores['te'] ?? null),
            'comparecimento' => $this->inteiro($eleitores['c'] ?? null),
            'abstencoes' => $this->inteiro($eleitores['a'] ?? null),
        ];
    }

    private function dataHora(mixed $data, mixed $hora): ?string
    {
        if (! is_string($data) || trim($data) === '' || ! is_string($hora) || trim($hora) === '') {
            return null;
        }

        return trim($data).' '.trim($hora);
    }

    private function inteiro(mixed $valor): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
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
}
