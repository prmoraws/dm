<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use InvalidArgumentException;

class TseApuracaoUrlBuilder
{
    public function ea14(Eleicao $eleicao): string
    {
        $codigo = $this->codigoEleicao($eleicao);

        return sprintf(
            '%s/%s/%s/%d/dados/br/br-e%s-ab.json',
            rtrim((string) config('politica.apuracao.base_url'), '/'),
            trim((string) config('politica.apuracao.ambiente', 'oficial'), '/'),
            trim($this->ciclo($eleicao), '/'),
            (int) $eleicao->tse_eleicao_codigo,
            $codigo,
        );
    }

    public function ea20(Eleicao $eleicao, Cargo $cargo, string $uf = 'BA'): string
    {
        $codigoEleicao = $this->codigoEleicao($eleicao);
        $codigoCargo = $this->codigoCargo($cargo);
        $abrangencia = $cargo->nome === 'Presidente' ? 'br' : strtolower($uf);

        return sprintf(
            '%s/%s/%s/%d/dados/%s/%s-c%s-e%s-u.json',
            rtrim((string) config('politica.apuracao.base_url'), '/'),
            trim((string) config('politica.apuracao.ambiente', 'oficial'), '/'),
            trim($this->ciclo($eleicao), '/'),
            (int) $eleicao->tse_eleicao_codigo,
            $abrangencia,
            $abrangencia,
            $codigoCargo,
            $codigoEleicao,
        );
    }

    private function codigoEleicao(Eleicao $eleicao): string
    {
        if (! $eleicao->tse_eleicao_codigo) {
            throw new InvalidArgumentException('A eleição não possui tse_eleicao_codigo. Sincronize primeiro as candidaturas oficiais.');
        }

        return str_pad((string) ((int) $eleicao->tse_eleicao_codigo), 6, '0', STR_PAD_LEFT);
    }

    private function codigoCargo(Cargo $cargo): string
    {
        $codigo = trim((string) $cargo->tse_codigo);

        if ($codigo === '') {
            $codigo = match ($cargo->nome) {
                'Presidente' => '1',
                'Governador' => '3',
                'Senador' => '5',
                'Deputado Federal' => '6',
                'Deputado Estadual' => '7',
                default => throw new InvalidArgumentException("Cargo {$cargo->nome} sem código TSE para apuração."),
            };
        }

        return str_pad((string) ((int) $codigo), 4, '0', STR_PAD_LEFT);
    }

    private function ciclo(Eleicao $eleicao): string
    {
        $configurado = trim((string) config('politica.apuracao.ciclo', ''));

        return $configurado !== '' ? $configurado : 'ele'.$eleicao->ano;
    }
}
