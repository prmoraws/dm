<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\EspelhoOperacional;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InteligenciaTerritorialService
{
    public function __construct(private readonly ComparativoTerritorialService $comparativo)
    {
    }

    /** @return array<string,mixed> */
    public function painel(
        string $politico,
        string $cargo,
        string $anoBase,
        string $anoComparada,
        string $sinal = 'todos',
        string $busca = '',
        string $ordem = 'relevancia_desc',
        int $pagina = 1,
        int $porPagina = 40,
    ): array {
        $comparativo = $this->comparativo->painel(
            $politico,
            $cargo,
            $anoBase,
            $anoComparada,
            '',
            'delta_desc',
            1,
            100,
        );

        if (! ($comparativo['disponivel'] ?? false)) {
            return $this->vazio($comparativo, $sinal, $busca, $ordem);
        }

        $municipios = collect($comparativo['municipios'] ?? [])
            ->where('cobertura', 'ambas')
            ->values();

        if ($municipios->isEmpty()) {
            return $this->vazio($comparativo, $sinal, $busca, $ordem);
        }

        $limiares = $this->limiares($municipios);
        $historicoAnterior = $this->historicoAnterior(
            $comparativo['selecao']['politico'],
            $comparativo['selecao']['cargo'],
            (int) $comparativo['selecao']['ano_base'],
        );

        $operacionais = EspelhoOperacional::query()
            ->whereIn('cidade_id', $municipios->pluck('cidade_id')->all())
            ->get()
            ->keyBy('cidade_id');

        $classificados = $municipios->map(function (array $item) use ($limiares, $historicoAnterior, $operacionais) {
            $anterior = $historicoAnterior['resultados']->get($item['cidade_id']);
            $operacional = $operacionais->get($item['cidade_id']);

            return $this->classificar(
                $item,
                $limiares,
                $historicoAnterior['ano'],
                $anterior !== null ? (int) $anterior : null,
                $operacional,
            );
        })->values();

        $resumo = $this->resumo($classificados, $operacionais);
        $sinal = $this->normalizarSinal($sinal);

        $filtrados = $classificados;
        if ($sinal !== 'todos') {
            $filtrados = $filtrados
                ->filter(fn (array $item) => in_array($sinal, $item['sinais'], true))
                ->values();
        }

        $buscaNormalizada = Str::upper(trim($busca));
        if ($buscaNormalizada !== '') {
            $filtrados = $filtrados
                ->filter(fn (array $item) => Str::contains(Str::upper($item['nome']), $buscaNormalizada))
                ->values();
        }

        $filtrados = $this->ordenar($filtrados, $ordem);
        $porPagina = max(20, min($porPagina, 500));
        $total = $filtrados->count();
        $paginas = max(1, (int) ceil($total / $porPagina));
        $pagina = max(1, min($pagina, $paginas));

        return [
            'disponivel' => true,
            'selecao' => $comparativo['selecao'],
            'opcoes' => $comparativo['opcoes'],
            'politicoResumo' => $comparativo['politicoResumo'] ?? null,
            'base' => $comparativo['base'],
            'comparada' => $comparativo['comparada'],
            'metricasComparativo' => $comparativo['metricas'],
            'limiares' => $limiares + ['ano_anterior' => $historicoAnterior['ano']],
            'resumo' => $resumo,
            'sinal' => $sinal,
            'sinaisDisponiveis' => $this->sinaisDisponiveis(),
            'busca' => trim($busca),
            'ordem' => $ordem,
            'itens' => $filtrados->slice(($pagina - 1) * $porPagina, $porPagina)->values()->all(),
            'paginacao' => [
                'pagina' => $pagina,
                'paginas' => $paginas,
                'por_pagina' => $porPagina,
                'total' => $total,
            ],
            'cobertura_incompleta' => (bool) ($comparativo['cobertura_incompleta'] ?? false),
        ];
    }

    /** @param Collection<int,array<string,mixed>> $municipios */
    private function limiares(Collection $municipios): array
    {
        $absDeltas = $municipios->map(fn (array $i) => abs((int) ($i['delta'] ?? 0)))->sort()->values();
        $baseVotos = $municipios->map(fn (array $i) => (int) ($i['votos_base'] ?? 0))->sort()->values();
        $participacaoAtual = $municipios
            ->map(fn (array $i) => (float) ($i['participacao_comparada'] ?? 0))
            ->sort()
            ->values();
        $absParticipacao = $municipios
            ->map(fn (array $i) => abs((float) ($i['delta_participacao_pp'] ?? 0)))
            ->sort()
            ->values();

        return [
            'impacto_p75_votos' => max(1, (int) round($this->percentil($absDeltas, 0.75))),
            'base_relevante_p75_votos' => max(1, (int) round($this->percentil($baseVotos, 0.75))),
            'concentracao_p90_participacao' => round($this->percentil($participacaoAtual, 0.90), 3),
            'max_abs_delta' => max(1, (int) $absDeltas->max()),
            'max_abs_participacao_pp' => max(0.001, (float) $absParticipacao->max()),
            'municipios_comparaveis' => $municipios->count(),
        ];
    }

    /** @return array{ano:?int,resultados:Collection<int,int>} */
    private function historicoAnterior(string $slug, string $cargo, int $anoBase): array
    {
        $candidatura = Candidatura::query()
            ->with('eleicao:id,ano')
            ->whereHas('politico', fn ($q) => $q->where('slug', $slug))
            ->whereHas('cargo', fn ($q) => $q->where('nome', $cargo))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', '<', $anoBase))
            ->whereHas('resultadosMunicipais')
            ->get()
            ->sortByDesc(fn (Candidatura $c) => (int) ($c->eleicao?->ano ?? 0))
            ->first();

        if (! $candidatura) {
            return ['ano' => null, 'resultados' => collect()];
        }

        return [
            'ano' => (int) $candidatura->eleicao?->ano,
            'resultados' => $candidatura->resultadosMunicipais()
                ->pluck('votos', 'cidade_id')
                ->map(fn ($votos) => (int) $votos),
        ];
    }

    private function classificar(
        array $item,
        array $limiares,
        ?int $anoAnterior,
        ?int $votosAnterior,
        ?EspelhoOperacional $operacional,
    ): array {
        $delta = (int) ($item['delta'] ?? 0);
        $deltaPp = (float) ($item['delta_participacao_pp'] ?? 0);
        $votosBase = (int) ($item['votos_base'] ?? 0);
        $votosAtual = (int) ($item['votos_comparada'] ?? 0);
        $participacaoAtual = (float) ($item['participacao_comparada'] ?? 0);
        $sinais = [];
        $recuperacao = null;

        if ($anoAnterior !== null && $votosAnterior !== null && $votosAnterior > $votosBase && $votosAtual > $votosBase) {
            $perdaAnterior = $votosAnterior - $votosBase;
            $recuperado = $votosAtual - $votosBase;
            $taxa = $perdaAnterior > 0 ? ($recuperado / $perdaAnterior) * 100 : 0;

            if ($taxa >= 50) {
                $sinais[] = 'recuperacao';
                $recuperacao = [
                    'ano_anterior' => $anoAnterior,
                    'votos_anterior' => $votosAnterior,
                    'perda_ate_base' => $perdaAnterior,
                    'votos_recuperados' => $recuperado,
                    'taxa_percentual' => round($taxa, 1),
                ];
            }
        }

        if ($delta < 0
            && $deltaPp < 0
            && abs($delta) >= $limiares['impacto_p75_votos']) {
            $sinais[] = 'perda_relevante';
        }

        if ($participacaoAtual >= $limiares['concentracao_p90_participacao']
            && $deltaPp > 0
            && $votosAtual > 0) {
            $sinais[] = 'concentracao';
        }

        if ($delta > 0
            && $deltaPp > 0
            && abs($delta) >= $limiares['impacto_p75_votos']) {
            $sinais[] = 'fortalecimento';
        }

        if ($delta < 0 && $votosBase >= $limiares['base_relevante_p75_votos']) {
            $sinais[] = 'oportunidade_recuperacao';
        }

        if ($sinais === []) {
            $sinais[] = 'estavel';
        }

        $principal = $this->principal($sinais);
        $scoreImpacto = min(1, abs($delta) / $limiares['max_abs_delta']);
        $scoreParticipacao = min(1, abs($deltaPp) / $limiares['max_abs_participacao_pp']);
        $score = (int) round(($scoreImpacto * 70) + ($scoreParticipacao * 30));
        if ($recuperacao !== null) {
            $score = min(100, $score + 10);
        }

        $operacionalArray = $operacional ? [
            'presente' => true,
            'revisado' => $operacional->revisado_em !== null,
            'revisado_em' => $operacional->revisado_em?->format('d/m/Y H:i'),
            'presidente_local' => $operacional->presidente_local,
            'indicacao_bispo' => $operacional->indicacao_bispo,
            'filiados_republicanos' => $operacional->filiados_republicanos,
        ] : [
            'presente' => false,
            'revisado' => false,
            'revisado_em' => null,
            'presidente_local' => null,
            'indicacao_bispo' => null,
            'filiados_republicanos' => null,
        ];

        return $item + [
            'sinais' => array_values(array_unique($sinais)),
            'sinal_principal' => $principal,
            'sinal_label' => $this->sinaisDisponiveis()[$principal]['label'],
            'sinal_explicacao' => $this->explicacao($principal, $item, $recuperacao),
            'relevancia_score' => $score,
            'recuperacao' => $recuperacao,
            'historico_anterior' => $votosAnterior !== null ? [
                'ano' => $anoAnterior,
                'votos' => $votosAnterior,
            ] : null,
            'operacional' => $operacionalArray,
            'espelho_url' => route('politica.espelho.inteligente', $item['cidade_id']),
            'espelho_editar_url' => route('politica.espelho.edit', $item['cidade_id']),
        ];
    }

    /** @param Collection<int,array<string,mixed>> $itens */
    private function resumo(Collection $itens, Collection $operacionais): array
    {
        $contar = fn (string $sinal) => $itens->filter(fn (array $i) => in_array($sinal, $i['sinais'], true))->count();
        $revisados = $operacionais->filter(fn ($op) => $op->revisado_em !== null)->count();

        return [
            'total' => $itens->count(),
            'recuperacao' => $contar('recuperacao'),
            'perda_relevante' => $contar('perda_relevante'),
            'concentracao' => $contar('concentracao'),
            'fortalecimento' => $contar('fortalecimento'),
            'oportunidade_recuperacao' => $contar('oportunidade_recuperacao'),
            'estavel' => $contar('estavel'),
            'espelho_presente' => $operacionais->count(),
            'espelho_revisado' => $revisados,
            'espelho_sem_registro' => max(0, $itens->count() - $operacionais->count()),
            'filiados_republicanos_total' => (int) $operacionais->sum(fn ($op) => (int) ($op->filiados_republicanos ?? 0)),
        ];
    }

    /** @param Collection<int,array<string,mixed>> $itens */
    private function ordenar(Collection $itens, string $ordem): Collection
    {
        return match ($ordem) {
            'delta_desc' => $itens->sortByDesc(fn (array $i) => $i['delta'] ?? PHP_INT_MIN)->values(),
            'delta_asc' => $itens->sortBy(fn (array $i) => $i['delta'] ?? PHP_INT_MAX)->values(),
            'atual_desc' => $itens->sortByDesc(fn (array $i) => $i['votos_comparada'] ?? -1)->values(),
            'nome' => $itens->sortBy('nome', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            default => $itens->sortByDesc('relevancia_score')->values(),
        };
    }

    private function principal(array $sinais): string
    {
        foreach ([
            'recuperacao',
            'perda_relevante',
            'concentracao',
            'fortalecimento',
            'oportunidade_recuperacao',
            'estavel',
        ] as $sinal) {
            if (in_array($sinal, $sinais, true)) {
                return $sinal;
            }
        }

        return 'estavel';
    }

    private function explicacao(string $principal, array $item, ?array $recuperacao): string
    {
        return match ($principal) {
            'recuperacao' => sprintf(
                'Após cair de %s votos em %d para %s na eleição-base, recompôs %s votos (%s%% da perda).',
                number_format((int) $recuperacao['votos_anterior'], 0, ',', '.'),
                (int) $recuperacao['ano_anterior'],
                number_format((int) $item['votos_base'], 0, ',', '.'),
                number_format((int) $recuperacao['votos_recuperados'], 0, ',', '.'),
                number_format((float) $recuperacao['taxa_percentual'], 1, ',', '.'),
            ),
            'perda_relevante' => sprintf(
                'Queda de %s votos e %s p.p. de participação nos votos do candidato; está no quartil de maior impacto absoluto.',
                number_format(abs((int) $item['delta']), 0, ',', '.'),
                number_format(abs((float) $item['delta_participacao_pp']), 3, ',', '.'),
            ),
            'concentracao' => sprintf(
                'O município representa %s%% da votação atual do candidato e ganhou %s p.p. de participação.',
                number_format((float) $item['participacao_comparada'], 3, ',', '.'),
                number_format((float) $item['delta_participacao_pp'], 3, ',', '.'),
            ),
            'fortalecimento' => sprintf(
                'Crescimento de %s votos e +%s p.p. de participação; está no quartil de maior impacto absoluto.',
                number_format((int) $item['delta'], 0, ',', '.'),
                number_format((float) $item['delta_participacao_pp'], 3, ',', '.'),
            ),
            'oportunidade_recuperacao' => sprintf(
                'Tinha base de %s votos e recuou %s; a base anterior está no quartil superior do recorte.',
                number_format((int) $item['votos_base'], 0, ',', '.'),
                number_format(abs((int) $item['delta']), 0, ',', '.'),
            ),
            default => 'Sem sinal automático forte pelos critérios estatísticos deste comparativo.',
        };
    }

    /** @return array<string,array{label:string,descricao:string}> */
    private function sinaisDisponiveis(): array
    {
        return [
            'todos' => ['label' => 'Todos os sinais', 'descricao' => 'Sem filtro de classificação.'],
            'recuperacao' => ['label' => 'Recuperação', 'descricao' => 'Há três pleitos comparáveis; pelo menos metade da perda anterior foi recomposta.'],
            'perda_relevante' => ['label' => 'Perda relevante', 'descricao' => 'Queda de votos e participação no quartil de maior impacto absoluto.'],
            'concentracao' => ['label' => 'Concentração', 'descricao' => 'Município no decil superior de participação atual e com ganho de participação.'],
            'fortalecimento' => ['label' => 'Fortalecimento', 'descricao' => 'Alta de votos e participação no quartil de maior impacto absoluto.'],
            'oportunidade_recuperacao' => ['label' => 'Oportunidade de recuperação', 'descricao' => 'Havia base eleitoral no quartil superior e ocorreu queda no pleito comparado.'],
            'estavel' => ['label' => 'Sem sinal forte', 'descricao' => 'Não atingiu os critérios automáticos acima.'],
        ];
    }

    private function normalizarSinal(string $sinal): string
    {
        return array_key_exists($sinal, $this->sinaisDisponiveis()) ? $sinal : 'todos';
    }

    private function percentil(Collection $valores, float $percentil): float
    {
        $valores = $valores->filter(fn ($v) => is_numeric($v))->map(fn ($v) => (float) $v)->sort()->values();
        $n = $valores->count();
        if ($n === 0) {
            return 0;
        }
        if ($n === 1) {
            return (float) $valores->first();
        }

        $pos = ($n - 1) * max(0, min(1, $percentil));
        $inferior = (int) floor($pos);
        $superior = (int) ceil($pos);
        if ($inferior === $superior) {
            return (float) $valores[$inferior];
        }

        $peso = $pos - $inferior;
        return ((float) $valores[$inferior] * (1 - $peso)) + ((float) $valores[$superior] * $peso);
    }

    private function vazio(array $comparativo, string $sinal, string $busca, string $ordem): array
    {
        return [
            'disponivel' => false,
            'selecao' => $comparativo['selecao'] ?? ['politico' => '', 'cargo' => '', 'ano_base' => '', 'ano_comparada' => ''],
            'opcoes' => $comparativo['opcoes'] ?? ['politicos' => [], 'cargos' => [], 'anos' => []],
            'politicoResumo' => null,
            'base' => null,
            'comparada' => null,
            'metricasComparativo' => [],
            'limiares' => [],
            'resumo' => [
                'total' => 0,
                'recuperacao' => 0,
                'perda_relevante' => 0,
                'concentracao' => 0,
                'fortalecimento' => 0,
                'oportunidade_recuperacao' => 0,
                'estavel' => 0,
                'espelho_presente' => 0,
                'espelho_revisado' => 0,
                'espelho_sem_registro' => 0,
                'filiados_republicanos_total' => 0,
            ],
            'sinal' => $this->normalizarSinal($sinal),
            'sinaisDisponiveis' => $this->sinaisDisponiveis(),
            'busca' => trim($busca),
            'ordem' => $ordem,
            'itens' => [],
            'paginacao' => ['pagina' => 1, 'paginas' => 1, 'por_pagina' => 40, 'total' => 0],
            'cobertura_incompleta' => false,
        ];
    }
}
