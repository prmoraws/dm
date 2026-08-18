<?php

namespace App\Services\Politica\V2;

use Illuminate\Support\Str;

class PainelExecutivoRelatorioService
{
    public function __construct(private readonly PainelExecutivoService $painel)
    {
    }

    /** @return array<string,mixed> */
    public function gerar(string $politico = 'todos'): array
    {
        $politico = $this->painel->normalizarPolitico($politico);
        $dados = $this->painel->painel($politico);
        $opcao = collect($dados['opcoesPoliticos'] ?? [])->firstWhere('slug', $politico);
        $recorte = $politico === 'todos'
            ? 'Todos os 4 históricos'
            : ($opcao['nome'] ?? Str::headline($politico));

        $resumoLinhas = $this->resumoLinhas($dados);
        array_unshift($resumoLinhas, ['Recorte', $recorte]);

        return $dados + [
            'recorte_label' => $recorte,
            'resumo_linhas' => $resumoLinhas,
            'acompanhados_linhas' => $this->acompanhadosLinhas($dados),
            'atencao_linhas' => $this->sinaisLinhas((array) ($dados['atencao'] ?? [])),
            'positivos_linhas' => $this->sinaisLinhas((array) ($dados['positivos'] ?? [])),
            'pendencias_linhas' => $this->pendenciasLinhas((array) ($dados['pendenciasEspelho'] ?? [])),
            'metodologia_linhas' => $this->metodologiaLinhas(),
        ];
    }

    /** @return array<int,array{0:string,1:int|string}> */
    private function resumoLinhas(array $dados): array
    {
        $r = (array) ($dados['resumo'] ?? []);

        return [
            ['Gerado em', ($dados['gerado_em'] ?? now())->format('d/m/Y H:i')],
            ['Acompanhados no recorte', (int) ($r['acompanhados'] ?? 0)],
            ['Com comparação do mesmo cargo', (int) ($r['com_comparacao'] ?? 0)],
            ['Aguardando segundo pleito comparável', (int) ($r['aguardando_comparacao'] ?? 0)],
            ['Contextos político x município analisados', (int) ($r['contextos_analisados'] ?? 0)],
            ['Municípios únicos comparáveis', (int) ($r['municipios_unicos'] ?? 0)],
            ['Variações negativas relevantes', (int) ($r['variacoes_negativas_relevantes'] ?? 0)],
            ['Sinais positivos', (int) ($r['sinais_positivos'] ?? 0)],
            ['Espelhos revisados', (int) ($r['espelhos_revisados'] ?? 0)],
            ['Pendências de espelho', (int) ($r['pendencias_espelho'] ?? 0)],
            ['Alertas de cobertura histórica', (int) ($r['alertas_cobertura'] ?? 0)],
        ];
    }

    /** @return array<int,array<int,mixed>> */
    private function acompanhadosLinhas(array $dados): array
    {
        return collect($dados['cards'] ?? [])->map(function (array $card) {
            return [
                $card['nome'] ?? '—',
                ($card['status'] ?? 'aguardando') === 'disponivel' ? 'Comparável' : 'Aguardando',
                $card['cargo'] ?? null,
                $card['ano_base'] ?? null,
                $card['ano_comparada'] ?? null,
                $card['votos_base'] ?? null,
                $card['votos_comparada'] ?? null,
                $card['delta_total'] ?? null,
                $card['delta_percentual'] ?? null,
                (int) ($card['municipios_comparaveis'] ?? 0),
                (int) ($card['perda_relevante'] ?? 0),
                (int) ($card['oportunidade_recuperacao'] ?? 0),
                (int) ($card['fortalecimento'] ?? 0),
                (int) ($card['recuperacao'] ?? 0),
                ! empty($card['cobertura_incompleta']) ? 'Cobertura desigual' : 'Sem alerta',
            ];
        })->values()->all();
    }

    /** @param array<int,array<string,mixed>> $itens
     *  @return array<int,array<int,mixed>>
     */
    private function sinaisLinhas(array $itens): array
    {
        return collect($itens)->map(function (array $item) {
            return [
                $item['nome'] ?? '—',
                $item['politico_nome'] ?? '—',
                $item['cargo'] ?? null,
                $item['ano_base'] ?? null,
                $item['ano_comparada'] ?? null,
                $item['votos_base'] ?? null,
                $item['votos_comparada'] ?? null,
                $item['delta'] ?? null,
                $item['delta_percentual'] ?? null,
                $item['participacao_base'] ?? null,
                $item['participacao_comparada'] ?? null,
                $item['delta_participacao_pp'] ?? null,
                $item['sinal_label'] ?? ($item['sinal_principal'] ?? null),
                (int) ($item['relevancia_score'] ?? 0),
                ! empty($item['operacional']['revisado']) ? 'Sim' : 'Não',
            ];
        })->values()->all();
    }

    /** @param array<int,array<string,mixed>> $itens
     *  @return array<int,array<int,mixed>>
     */
    private function pendenciasLinhas(array $itens): array
    {
        return collect($itens)->map(function (array $item) {
            return [
                $item['nome'] ?? '—',
                $item['ibge_code'] ?? null,
                ($item['status'] ?? '') === 'sem_registro' ? 'Sem registro operacional' : 'Revisão pendente',
                (int) ($item['relevancia_score'] ?? 0),
                (int) ($item['contextos'] ?? 0),
                implode(', ', (array) ($item['politicos'] ?? [])),
            ];
        })->values()->all();
    }

    /** @return array<int,array{0:string,1:string}> */
    private function metodologiaLinhas(): array
    {
        return [
            ['Comparação', 'Somente eleições do mesmo político e do mesmo cargo; o painel usa os dois pleitos mais recentes com resultado municipal disponível.'],
            ['Cobertura histórica', 'Município sem linha oficial em um dos pleitos fica fora do delta; ausência nunca é convertida em zero.'],
            ['Relevância', 'Score descritivo de intensidade estatística calculado dentro do próprio recorte; não é previsão de voto, probabilidade eleitoral ou pesquisa.'],
            ['Espelho operacional', 'A fila de pendências serve para revisão e qualidade dos dados internos e não altera resultados oficiais.'],
            ['Fonte eleitoral', 'Resultados oficiais armazenados localmente a partir dos Dados Abertos do TSE.'],
        ];
    }
}
