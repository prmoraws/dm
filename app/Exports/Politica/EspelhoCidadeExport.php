<?php

namespace App\Exports\Politica;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EspelhoCidadeExport implements WithMultipleSheets
{
    /** @param array<string,mixed> $dados */
    public function __construct(private readonly array $dados)
    {
    }

    public function sheets(): array
    {
        return [
            new PainelExecutivoSheet('Resumo', ['Indicador', 'Valor'], (array) $this->dados['resumo_linhas'], ['A' => 48, 'B' => 48]),
            new PainelExecutivoSheet('Espelho operacional', ['Campo', 'Valor'], (array) $this->dados['operacional_linhas'], ['A' => 34, 'B' => 90]),
            new PainelExecutivoSheet('Favorito e metas', ['Campo', 'Valor'], (array) $this->dados['favorito_linhas'], ['A' => 38, 'B' => 90]),
            new PainelExecutivoSheet(
                'Histórico na cidade',
                ['Ano', 'Turno', 'Partido', 'Número', 'Votos', '% oficial', 'Posição', 'Delta votos', 'Delta %', 'Situação', 'Linha municipal', 'Origem'],
                (array) $this->dados['historico_linhas'],
                ['A' => 10, 'B' => 10, 'C' => 16, 'D' => 12, 'E' => 14, 'F' => 14, 'G' => 12, 'H' => 14, 'I' => 12, 'J' => 24, 'K' => 20, 'L' => 22],
            ),
            new PainelExecutivoSheet(
                'Zonas',
                ['Zona', 'Código TSE', 'Votos', '% oficial', 'Seções', 'Seções totalizadas'],
                (array) $this->dados['zonas_linhas'],
                ['A' => 12, 'B' => 16, 'C' => 14, 'D' => 14, 'E' => 12, 'F' => 20],
            ),
            new PainelExecutivoSheet(
                'Ranking do recorte',
                ['#', 'Selecionado', 'Candidato', 'Partido', 'Número', 'Votos no município', '% oficial'],
                (array) $this->dados['ranking_linhas'],
                ['A' => 8, 'B' => 14, 'C' => 32, 'D' => 16, 'E' => 12, 'F' => 20, 'G' => 14],
            ),
            new PainelExecutivoSheet('Auditoria', ['Verificação', 'Resultado'], (array) $this->dados['auditoria_linhas'], ['A' => 46, 'B' => 32]),
            new PainelExecutivoSheet('Metodologia', ['Critério', 'Descrição'], (array) $this->dados['metodologia_linhas'], ['A' => 26, 'B' => 100]),
        ];
    }
}
