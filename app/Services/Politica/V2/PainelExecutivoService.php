<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Politico;
use Illuminate\Support\Collection;

class PainelExecutivoService
{
    public function __construct(
        private readonly PoliticaHistoricoOficialService $historico,
        private readonly InteligenciaTerritorialService $inteligencia,
    ) {
    }

    public function normalizarPolitico(string $politico): string
    {
        if ($politico === 'todos') {
            return 'todos';
        }

        return in_array($politico, $this->historico->slugs(), true) ? $politico : 'todos';
    }

    /** @return array<string,mixed> */
    public function painel(string $politicoSelecionado = 'todos'): array
    {
        $politicoSelecionado = $this->normalizarPolitico($politicoSelecionado);
        $slugs = $this->historico->slugs();

        $politicos = Politico::query()
            ->whereIn('slug', $slugs)
            ->with(['candidaturas' => fn ($q) => $q
                ->with(['eleicao:id,ano,turno', 'cargo:id,nome', 'partido:id,sigla'])
                ->withCount('resultadosMunicipais')])
            ->get()
            ->sortBy(fn (Politico $p) => array_search($p->slug, $slugs, true))
            ->values();

        $opcoes = $politicos->map(fn (Politico $p) => [
            'slug' => $p->slug,
            'nome' => $p->nome_publico,
            'foto_url' => $p->foto_url,
        ])->all();

        if ($politicoSelecionado !== 'todos') {
            $politicos = $politicos->where('slug', $politicoSelecionado)->values();
        }

        $cards = collect();
        $contextos = collect();

        foreach ($politicos as $politico) {
            $selecao = $this->selecaoMaisRecente($politico);
            if ($selecao === null) {
                $cards->push($this->cardAguardando($politico));
                continue;
            }

            $painel = $this->inteligencia->painel(
                $politico->slug,
                $selecao['cargo'],
                (string) $selecao['ano_base'],
                (string) $selecao['ano_comparada'],
                'todos',
                '',
                'relevancia_desc',
                1,
                500,
            );

            if (! ($painel['disponivel'] ?? false)) {
                $cards->push($this->cardAguardando($politico));
                continue;
            }

            $card = $this->cardDisponivel($politico, $painel);
            $cards->push($card);

            foreach ((array) ($painel['itens'] ?? []) as $item) {
                $contextos->push($this->contexto($politico, $painel, $item));
            }
        }

        $atencao = $contextos
            ->filter(fn (array $i) => $this->temAlgumSinal($i, ['perda_relevante', 'oportunidade_recuperacao']))
            ->sortByDesc(fn (array $i) => sprintf('%03d-%012d', (int) $i['relevancia_score'], abs((int) $i['delta'])))
            ->take(12)
            ->values();

        $positivos = $contextos
            ->filter(fn (array $i) => $this->temAlgumSinal($i, ['recuperacao', 'fortalecimento', 'concentracao']))
            ->sortByDesc(fn (array $i) => sprintf('%03d-%012d', (int) $i['relevancia_score'], abs((int) $i['delta'])))
            ->take(12)
            ->values();

        $municipiosUnicos = $contextos->groupBy('cidade_id');
        $pendencias = $this->pendenciasEspelho($contextos);
        $revisados = $municipiosUnicos
            ->filter(fn (Collection $itens) => $itens->contains(fn (array $i) => (bool) ($i['operacional']['revisado'] ?? false)))
            ->count();

        return [
            'politicoSelecionado' => $politicoSelecionado,
            'opcoesPoliticos' => $opcoes,
            'cards' => $cards->all(),
            'resumo' => [
                'acompanhados' => $cards->count(),
                'com_comparacao' => $cards->where('status', 'disponivel')->count(),
                'aguardando_comparacao' => $cards->where('status', 'aguardando')->count(),
                'contextos_analisados' => $contextos->count(),
                'municipios_unicos' => $municipiosUnicos->count(),
                'variacoes_negativas_relevantes' => $contextos
                    ->filter(fn (array $i) => $this->temAlgumSinal($i, ['perda_relevante', 'oportunidade_recuperacao']))
                    ->count(),
                'sinais_positivos' => $contextos
                    ->filter(fn (array $i) => $this->temAlgumSinal($i, ['recuperacao', 'fortalecimento', 'concentracao']))
                    ->count(),
                'espelhos_revisados' => $revisados,
                'pendencias_espelho' => $pendencias->count(),
                'alertas_cobertura' => $cards->where('cobertura_incompleta', true)->count(),
            ],
            'atencao' => $atencao->all(),
            'positivos' => $positivos->all(),
            'pendenciasEspelho' => $pendencias->take(12)->values()->all(),
            'gerado_em' => now(),
        ];
    }

    /** @return array{cargo:string,ano_base:int,ano_comparada:int}|null */
    private function selecaoMaisRecente(Politico $politico): ?array
    {
        $opcoes = $politico->candidaturas
            ->filter(fn (Candidatura $c) => (int) ($c->resultados_municipais_count ?? 0) > 0 && $c->eleicao?->ano && $c->cargo?->nome)
            ->groupBy(fn (Candidatura $c) => $c->cargo->nome)
            ->map(function (Collection $itens, string $cargo) {
                $anos = $itens
                    ->pluck('eleicao.ano')
                    ->map(fn ($ano) => (int) $ano)
                    ->unique()
                    ->sortDesc()
                    ->values();

                if ($anos->count() < 2) {
                    return null;
                }

                return [
                    'cargo' => $cargo,
                    'ano_base' => (int) $anos[1],
                    'ano_comparada' => (int) $anos[0],
                    'pleitos' => $anos->count(),
                ];
            })
            ->filter()
            ->sortByDesc(fn (array $item) => sprintf('%04d-%03d', $item['ano_comparada'], $item['pleitos']))
            ->values();

        return $opcoes->first();
    }

    /** @return array<string,mixed> */
    private function cardDisponivel(Politico $politico, array $painel): array
    {
        $delta = (int) ($painel['metricasComparativo']['delta_total'] ?? 0);

        return [
            'status' => 'disponivel',
            'slug' => $politico->slug,
            'nome' => $politico->nome_publico,
            'foto_url' => $politico->foto_url,
            'cargo' => $painel['selecao']['cargo'],
            'ano_base' => (int) $painel['selecao']['ano_base'],
            'ano_comparada' => (int) $painel['selecao']['ano_comparada'],
            'votos_base' => (int) ($painel['base']['votos'] ?? 0),
            'votos_comparada' => (int) ($painel['comparada']['votos'] ?? 0),
            'delta_total' => $delta,
            'delta_percentual' => $painel['metricasComparativo']['delta_total_percentual'] ?? null,
            'municipios_comparaveis' => (int) ($painel['resumo']['total'] ?? 0),
            'perda_relevante' => (int) ($painel['resumo']['perda_relevante'] ?? 0),
            'fortalecimento' => (int) ($painel['resumo']['fortalecimento'] ?? 0),
            'recuperacao' => (int) ($painel['resumo']['recuperacao'] ?? 0),
            'oportunidade_recuperacao' => (int) ($painel['resumo']['oportunidade_recuperacao'] ?? 0),
            'espelho_revisado' => (int) ($painel['resumo']['espelho_revisado'] ?? 0),
            'cobertura_incompleta' => (bool) ($painel['cobertura_incompleta'] ?? false),
            'inteligencia_url' => route('politica.inteligencia-territorial', [
                'politico' => $politico->slug,
                'cargo' => $painel['selecao']['cargo'],
                'base' => $painel['selecao']['ano_base'],
                'comparada' => $painel['selecao']['ano_comparada'],
            ]),
            'comparativo_url' => route('politica.comparativo-territorial', [
                'politico' => $politico->slug,
                'cargo' => $painel['selecao']['cargo'],
                'base' => $painel['selecao']['ano_base'],
                'comparada' => $painel['selecao']['ano_comparada'],
            ]),
            'perfil_url' => route('politica.politicos.show', $politico),
        ];
    }

    /** @return array<string,mixed> */
    private function cardAguardando(Politico $politico): array
    {
        $ultima = $politico->candidaturas
            ->filter(fn (Candidatura $c) => (int) ($c->resultados_municipais_count ?? 0) > 0 && $c->eleicao?->ano)
            ->sortByDesc(fn (Candidatura $c) => (int) $c->eleicao?->ano)
            ->first();

        return [
            'status' => 'aguardando',
            'slug' => $politico->slug,
            'nome' => $politico->nome_publico,
            'foto_url' => $politico->foto_url,
            'cargo' => $ultima?->cargo?->nome,
            'ano_base' => null,
            'ano_comparada' => $ultima?->eleicao?->ano ? (int) $ultima->eleicao->ano : null,
            'votos_base' => null,
            'votos_comparada' => $ultima ? (int) $ultima->votos_total : null,
            'delta_total' => null,
            'delta_percentual' => null,
            'municipios_comparaveis' => 0,
            'perda_relevante' => 0,
            'fortalecimento' => 0,
            'recuperacao' => 0,
            'oportunidade_recuperacao' => 0,
            'espelho_revisado' => 0,
            'cobertura_incompleta' => false,
            'inteligencia_url' => null,
            'comparativo_url' => null,
            'perfil_url' => route('politica.politicos.show', $politico),
        ];
    }

    /** @return array<string,mixed> */
    private function contexto(Politico $politico, array $painel, array $item): array
    {
        return $item + [
            'politico_slug' => $politico->slug,
            'politico_nome' => $politico->nome_publico,
            'politico_foto_url' => $politico->foto_url,
            'cargo' => $painel['selecao']['cargo'],
            'ano_base' => (int) $painel['selecao']['ano_base'],
            'ano_comparada' => (int) $painel['selecao']['ano_comparada'],
            'inteligencia_url' => route('politica.inteligencia-territorial', [
                'politico' => $politico->slug,
                'cargo' => $painel['selecao']['cargo'],
                'base' => $painel['selecao']['ano_base'],
                'comparada' => $painel['selecao']['ano_comparada'],
                'busca' => $item['nome'],
            ]),
        ];
    }

    /** @param array<string,mixed> $item */
    private function temAlgumSinal(array $item, array $sinais): bool
    {
        return count(array_intersect((array) ($item['sinais'] ?? []), $sinais)) > 0;
    }

    /** @param Collection<int,array<string,mixed>> $contextos */
    private function pendenciasEspelho(Collection $contextos): Collection
    {
        return $contextos
            ->filter(function (array $item) {
                if ((bool) ($item['operacional']['revisado'] ?? false)) {
                    return false;
                }

                return $this->temAlgumSinal($item, [
                    'recuperacao',
                    'perda_relevante',
                    'concentracao',
                    'fortalecimento',
                    'oportunidade_recuperacao',
                ]);
            })
            ->groupBy('cidade_id')
            ->map(function (Collection $itens) {
                $maisRelevante = $itens->sortByDesc('relevancia_score')->first();
                $politicos = $itens->pluck('politico_nome')->unique()->values()->all();
                $temRegistro = $itens->contains(fn (array $i) => (bool) ($i['operacional']['presente'] ?? false));

                return [
                    'cidade_id' => $maisRelevante['cidade_id'],
                    'nome' => $maisRelevante['nome'],
                    'ibge_code' => $maisRelevante['ibge_code'] ?? null,
                    'relevancia_score' => (int) $itens->max('relevancia_score'),
                    'status' => $temRegistro ? 'revisao_pendente' : 'sem_registro',
                    'contextos' => $itens->count(),
                    'politicos' => $politicos,
                    'espelho_url' => $maisRelevante['espelho_url'],
                    'espelho_editar_url' => $maisRelevante['espelho_editar_url'],
                ];
            })
            ->sortByDesc(fn (array $item) => sprintf('%03d-%05d', (int) $item['relevancia_score'], (int) $item['contextos']))
            ->values();
    }
}
