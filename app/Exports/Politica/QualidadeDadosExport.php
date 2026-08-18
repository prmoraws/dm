<?php

namespace App\Exports\Politica;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QualidadeDadosExport implements WithMultipleSheets
{
    /** @param array<string,mixed> $dados */
    public function __construct(private readonly array $dados)
    {
    }

    public function sheets(): array
    {
        return [
            new PainelExecutivoSheet('Resumo', ['Indicador', 'Valor'], (array) $this->dados['resumo_export_linhas'], ['A' => 42, 'B' => 32]),
            new PainelExecutivoSheet(
                'Achados',
                ['Nível', 'Grupo', 'Código', 'Achado', 'Contexto', 'Descrição'],
                (array) $this->dados['achados_linhas'],
                ['A' => 12, 'B' => 22, 'C' => 36, 'D' => 44, 'E' => 60, 'F' => 100],
            ),
            new PainelExecutivoSheet('Metodologia', ['Critério', 'Descrição'], (array) $this->dados['metodologia_linhas'], ['A' => 28, 'B' => 110]),
        ];
    }
}
