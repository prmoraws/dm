<?php

namespace App\Exports\Politica;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PainelExecutivoExport implements WithMultipleSheets
{
    /** @param array<string,mixed> $dados */
    public function __construct(private readonly array $dados)
    {
    }

    public function sheets(): array
    {
        return [
            new PainelExecutivoSheet(
                'Resumo',
                ['Indicador', 'Valor'],
                (array) ($this->dados['resumo_linhas'] ?? []),
                ['A' => 45, 'B' => 28],
            ),
            new PainelExecutivoSheet(
                'Acompanhados',
                [
                    'Acompanhado', 'Status', 'Cargo', 'Eleição base', 'Eleição comparada',
                    'Votos base', 'Votos comparada', 'Delta votos', 'Delta %', 'Municípios comparáveis',
                    'Perda relevante', 'Oportunidade recuperação', 'Fortalecimento', 'Recuperação', 'Cobertura',
                ],
                (array) ($this->dados['acompanhados_linhas'] ?? []),
                [
                    'A' => 26, 'B' => 14, 'C' => 22, 'D' => 13, 'E' => 15,
                    'F' => 14, 'G' => 16, 'H' => 14, 'I' => 12, 'J' => 18,
                    'K' => 15, 'L' => 22, 'M' => 16, 'N' => 14, 'O' => 18,
                ],
            ),
            new PainelExecutivoSheet('Variações negativas', $this->cabecalhoSinais(), (array) ($this->dados['atencao_linhas'] ?? []), $this->largurasSinais()),
            new PainelExecutivoSheet('Sinais positivos', $this->cabecalhoSinais(), (array) ($this->dados['positivos_linhas'] ?? []), $this->largurasSinais()),
            new PainelExecutivoSheet(
                'Pendências espelho',
                ['Município', 'Código IBGE', 'Situação', 'Relevância / 100', 'Contextos', 'Acompanhados'],
                (array) ($this->dados['pendencias_linhas'] ?? []),
                ['A' => 28, 'B' => 14, 'C' => 24, 'D' => 18, 'E' => 12, 'F' => 42],
            ),
            new PainelExecutivoSheet(
                'Metodologia',
                ['Critério', 'Descrição'],
                (array) ($this->dados['metodologia_linhas'] ?? []),
                ['A' => 26, 'B' => 90],
            ),
        ];
    }

    /** @return array<int,string> */
    private function cabecalhoSinais(): array
    {
        return [
            'Município', 'Acompanhado', 'Cargo', 'Eleição base', 'Eleição comparada',
            'Votos base', 'Votos comparada', 'Delta votos', 'Delta %',
            'Participação base %', 'Participação comparada %', 'Delta participação p.p.',
            'Sinal principal', 'Relevância / 100', 'Espelho revisado',
        ];
    }

    /** @return array<string,int> */
    private function largurasSinais(): array
    {
        return [
            'A' => 28, 'B' => 26, 'C' => 22, 'D' => 13, 'E' => 15,
            'F' => 14, 'G' => 16, 'H' => 14, 'I' => 12, 'J' => 18,
            'K' => 22, 'L' => 22, 'M' => 24, 'N' => 18, 'O' => 16,
        ];
    }
}
