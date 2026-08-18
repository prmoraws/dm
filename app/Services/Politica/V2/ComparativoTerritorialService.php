<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Politico;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ComparativoTerritorialService
{
    public function __construct(private readonly PoliticaHistoricoOficialService $historico)
    {
    }

    /** @return array{politico:string,cargo:string,ano_base:string,ano_comparada:string} */
    public function selecaoPadrao(): array
    {
        $dados = $this->estruturaOpcoes();
        $politico = array_key_first($dados);
        if ($politico === null) {
            return ['politico' => '', 'cargo' => '', 'ano_base' => '', 'ano_comparada' => ''];
        }

        $cargo = array_key_first($dados[$politico]['cargos']);
        $anos = $cargo ? $dados[$politico]['cargos'][$cargo] : [];

        return [
            'politico' => $politico,
            'cargo' => $cargo ?: '',
            'ano_base' => isset($anos[1]) ? (string) $anos[1] : '',
            'ano_comparada' => isset($anos[0]) ? (string) $anos[0] : '',
        ];
    }

    /** @return array{politico:string,cargo:string,ano_base:string,ano_comparada:string} */
    public function normalizarSelecao(string $politico, string $cargo, string $anoBase, string $anoComparada): array
    {
        $dados = $this->estruturaOpcoes();
        if ($dados === []) {
            return ['politico' => '', 'cargo' => '', 'ano_base' => '', 'ano_comparada' => ''];
        }

        if (! isset($dados[$politico])) {
            $politico = (string) array_key_first($dados);
        }

        $cargos = $dados[$politico]['cargos'];
        if (! isset($cargos[$cargo])) {
            $cargo = (string) array_key_first($cargos);
        }

        $anos = $cargos[$cargo] ?? [];
        if (count($anos) < 2) {
            return ['politico' => $politico, 'cargo' => $cargo, 'ano_base' => '', 'ano_comparada' => ''];
        }

        $base = ctype_digit($anoBase) ? (int) $anoBase : null;
        $comparada = ctype_digit($anoComparada) ? (int) $anoComparada : null;

        if (! in_array($comparada, $anos, true)) {
            $comparada = $anos[0];
        }
        if (! in_array($base, $anos, true) || $base === $comparada) {
            $base = collect($anos)->first(fn (int $ano) => $ano < $comparada)
                ?? collect($anos)->first(fn (int $ano) => $ano !== $comparada)
                ?? $anos[1];
        }

        if ($base > $comparada) {
            [$base, $comparada] = [$comparada, $base];
        }

        return [
            'politico' => $politico,
            'cargo' => $cargo,
            'ano_base' => (string) $base,
            'ano_comparada' => (string) $comparada,
        ];
    }

    /** @return array<string,mixed> */
    public function painel(
        string $politico,
        string $cargo,
        string $anoBase,
        string $anoComparada,
        string $busca = '',
        string $ordem = 'delta_desc',
        int $pagina = 1,
        int $porPagina = 50,
    ): array {
        $selecao = $this->normalizarSelecao($politico, $cargo, $anoBase, $anoComparada);
        $opcoes = $this->opcoes($selecao['politico'], $selecao['cargo']);

        if ($selecao['politico'] === '' || $selecao['cargo'] === '' || $selecao['ano_base'] === '' || $selecao['ano_comparada'] === '') {
            return $this->vazio($selecao, $opcoes);
        }

        $base = $this->candidatura($selecao['politico'], $selecao['cargo'], (int) $selecao['ano_base']);
        $comparada = $this->candidatura($selecao['politico'], $selecao['cargo'], (int) $selecao['ano_comparada']);

        if (! $base || ! $comparada) {
            return $this->vazio($selecao, $opcoes);
        }

        $politicoModel = $base->politico;
        $baseRows = $base->resultadosMunicipais()
            ->with('cidade:id,nome,ibge_code,latitude,longitude,populacao')
            ->get()
            ->keyBy('cidade_id');
        $comparadaRows = $comparada->resultadosMunicipais()
            ->with('cidade:id,nome,ibge_code,latitude,longitude,populacao')
            ->get()
            ->keyBy('cidade_id');

        $ids = $baseRows->keys()->merge($comparadaRows->keys())->unique()->values();
        $municipios = $ids->map(function ($cidadeId) use ($baseRows, $comparadaRows, $base, $comparada) {
            $a = $baseRows->get($cidadeId);
            $b = $comparadaRows->get($cidadeId);
            $cidade = $b?->cidade ?: $a?->cidade;
            $temAmbos = $a !== null && $b !== null;
            $votosBase = $a ? (int) $a->votos : null;
            $votosComparada = $b ? (int) $b->votos : null;
            $delta = $temAmbos ? $votosComparada - $votosBase : null;
            $shareBase = ($votosBase !== null && (int) $base->votos_total > 0)
                ? ($votosBase / (int) $base->votos_total) * 100
                : null;
            $shareComparada = ($votosComparada !== null && (int) $comparada->votos_total > 0)
                ? ($votosComparada / (int) $comparada->votos_total) * 100
                : null;

            $status = 'sem_comparacao';
            if ($temAmbos) {
                $status = $delta > 0 ? 'crescimento' : ($delta < 0 ? 'queda' : 'estavel');
            }

            return [
                'cidade_id' => (int) $cidadeId,
                'nome' => $cidade?->nome ?: 'Município #'.$cidadeId,
                'ibge_code' => $cidade?->ibge_code,
                'lat' => $cidade?->latitude !== null ? (float) $cidade->latitude : null,
                'lng' => $cidade?->longitude !== null ? (float) $cidade->longitude : null,
                'populacao' => $cidade?->populacao !== null ? (int) $cidade->populacao : null,
                'votos_base' => $votosBase,
                'votos_comparada' => $votosComparada,
                'delta' => $delta,
                'delta_percentual' => ($temAmbos && $votosBase > 0)
                    ? round(($delta / $votosBase) * 100, 1)
                    : null,
                'participacao_base' => $shareBase !== null ? round($shareBase, 3) : null,
                'participacao_comparada' => $shareComparada !== null ? round($shareComparada, 3) : null,
                'delta_participacao_pp' => ($shareBase !== null && $shareComparada !== null)
                    ? round($shareComparada - $shareBase, 3)
                    : null,
                'status' => $status,
                'cobertura' => $temAmbos ? 'ambas' : ($a ? 'somente_base' : 'somente_comparada'),
                'url' => $cidade ? route('politica.espelho.inteligente', $cidade) : null,
            ];
        })->values();

        $comparaveis = $municipios->where('cobertura', 'ambas')->values();
        $maxAbsDelta = max(1, (int) $comparaveis->max(fn (array $item) => abs((int) $item['delta'])));
        $mapaData = $municipios
            ->filter(fn (array $item) => $item['lat'] !== null && $item['lng'] !== null)
            ->map(function (array $item) use ($maxAbsDelta) {
                $item['intensidade'] = $item['delta'] !== null ? round(abs($item['delta']) / $maxAbsDelta, 5) : 0;
                return $item;
            })
            ->values()
            ->all();

        $filtrados = $municipios;
        $busca = Str::upper(trim($busca));
        if ($busca !== '') {
            $filtrados = $filtrados->filter(fn (array $item) => Str::contains(Str::upper($item['nome']), $busca))->values();
        }

        $filtrados = $this->ordenar($filtrados, $ordem);
        $porPagina = max(25, min($porPagina, 100));
        $total = $filtrados->count();
        $paginas = max(1, (int) ceil($total / $porPagina));
        $pagina = max(1, min($pagina, $paginas));
        $tabela = $filtrados->slice(($pagina - 1) * $porPagina, $porPagina)->values()->all();

        $deltaTotal = (int) $comparada->votos_total - (int) $base->votos_total;

        return [
            'disponivel' => true,
            'selecao' => $selecao,
            'opcoes' => $opcoes,
            'politicoResumo' => [
                'nome' => $politicoModel?->nome_publico ?: $base->nome_urna,
                'slug' => $politicoModel?->slug,
                'foto_url' => $politicoModel?->foto_url ?: $comparada->foto_url ?: $base->foto_url,
            ],
            'base' => $this->resumoCandidatura($base, $baseRows->count()),
            'comparada' => $this->resumoCandidatura($comparada, $comparadaRows->count()),
            'metricas' => [
                'delta_total' => $deltaTotal,
                'delta_total_percentual' => (int) $base->votos_total > 0 ? round(($deltaTotal / (int) $base->votos_total) * 100, 1) : null,
                'comparaveis' => $comparaveis->count(),
                'crescimento' => $comparaveis->where('status', 'crescimento')->count(),
                'queda' => $comparaveis->where('status', 'queda')->count(),
                'estaveis' => $comparaveis->where('status', 'estavel')->count(),
                'somente_base' => $municipios->where('cobertura', 'somente_base')->count(),
                'somente_comparada' => $municipios->where('cobertura', 'somente_comparada')->count(),
                'saldo_comparavel' => (int) $comparaveis->sum('delta'),
                'mapa' => count($mapaData),
            ],
            'ganhos' => $comparaveis->where('status', 'crescimento')->sortByDesc('delta')->take(10)->values()->all(),
            'quedas' => $comparaveis->where('status', 'queda')->sortBy('delta')->take(10)->values()->all(),
            'mapaData' => $mapaData,
            // Coleção completa (máx. ~417 municípios na Bahia) para camadas derivadas
            // de inteligência. Não é exibida diretamente pela view do comparativo.
            'municipios' => $municipios->all(),
            'tabela' => $tabela,
            'paginacao' => [
                'pagina' => $pagina,
                'paginas' => $paginas,
                'por_pagina' => $porPagina,
                'total' => $total,
            ],
            'busca' => trim($busca),
            'ordem' => $ordem,
            'cobertura_incompleta' => $municipios->where('cobertura', '!=', 'ambas')->isNotEmpty(),
        ];
    }

    /** @return array<string,mixed> */
    private function opcoes(string $politicoSelecionado = '', string $cargoSelecionado = ''): array
    {
        $dados = $this->estruturaOpcoes();
        $politicos = collect($dados)->map(fn (array $item, string $slug) => [
            'slug' => $slug,
            'nome' => $item['nome'],
            'foto_url' => $item['foto_url'],
        ])->values()->all();

        $cargos = isset($dados[$politicoSelecionado])
            ? collect($dados[$politicoSelecionado]['cargos'])->keys()->values()->all()
            : [];
        $anos = isset($dados[$politicoSelecionado]['cargos'][$cargoSelecionado])
            ? $dados[$politicoSelecionado]['cargos'][$cargoSelecionado]
            : [];

        return ['politicos' => $politicos, 'cargos' => $cargos, 'anos' => $anos];
    }

    /** @return array<string,array{nome:string,foto_url:?string,cargos:array<string,array<int,int>>}> */
    private function estruturaOpcoes(): array
    {
        $politicos = Politico::query()
            ->whereIn('slug', $this->historico->slugs())
            ->with(['candidaturas' => fn ($q) => $q
                ->with(['eleicao:id,ano,turno', 'cargo:id,nome'])
                ->withCount('resultadosMunicipais')])
            ->get()
            ->sortBy(fn (Politico $p) => array_search($p->slug, $this->historico->slugs(), true));

        $saida = [];
        foreach ($politicos as $politico) {
            $cargos = $politico->candidaturas
                ->filter(fn (Candidatura $c) => (int) ($c->resultados_municipais_count ?? 0) > 0 && $c->eleicao?->ano && $c->cargo?->nome)
                ->groupBy(fn (Candidatura $c) => $c->cargo->nome)
                ->map(fn (Collection $itens) => $itens->pluck('eleicao.ano')->map(fn ($ano) => (int) $ano)->unique()->sortDesc()->values()->all())
                ->filter(fn (array $anos) => count($anos) >= 2)
                ->all();

            if ($cargos === []) {
                continue;
            }

            $saida[$politico->slug] = [
                'nome' => $politico->nome_publico,
                'foto_url' => $politico->foto_url,
                'cargos' => $cargos,
            ];
        }

        return $saida;
    }

    private function candidatura(string $slug, string $cargo, int $ano): ?Candidatura
    {
        return Candidatura::query()
            ->with(['politico:id,nome_publico,slug,foto_url', 'partido:id,sigla', 'cargo:id,nome', 'eleicao:id,ano,turno,tipo'])
            ->whereHas('politico', fn ($q) => $q->where('slug', $slug))
            ->whereHas('cargo', fn ($q) => $q->where('nome', $cargo))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', $ano))
            ->whereHas('resultadosMunicipais')
            ->orderByDesc('id')
            ->first();
    }

    /** @return array<string,mixed> */
    private function resumoCandidatura(Candidatura $candidatura, int $municipios): array
    {
        return [
            'id' => $candidatura->id,
            'ano' => (int) $candidatura->eleicao?->ano,
            'turno' => (int) $candidatura->eleicao?->turno,
            'cargo' => $candidatura->cargo?->nome,
            'partido' => $candidatura->partido?->sigla,
            'numero' => $candidatura->numero_urna,
            'votos' => (int) $candidatura->votos_total,
            'situacao' => $candidatura->situacao_eleicao ?: $candidatura->situacaoRegistroExibicao(),
            'municipios' => $municipios,
            'mapa_url' => route('politica.mapa', [
                'eleicao' => $candidatura->eleicao_id,
                'cargo' => $candidatura->cargo_id,
                'candidatura' => $candidatura->id,
            ]),
        ];
    }

    /** @param Collection<int,array<string,mixed>> $itens */
    private function ordenar(Collection $itens, string $ordem): Collection
    {
        return match ($ordem) {
            'delta_asc' => $itens->sortBy(fn (array $i) => $i['delta'] ?? PHP_INT_MAX)->values(),
            'atual_desc' => $itens->sortByDesc(fn (array $i) => $i['votos_comparada'] ?? -1)->values(),
            'base_desc' => $itens->sortByDesc(fn (array $i) => $i['votos_base'] ?? -1)->values(),
            'nome' => $itens->sortBy('nome', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            default => $itens->sortByDesc(fn (array $i) => $i['delta'] ?? PHP_INT_MIN)->values(),
        };
    }

    private function vazio(array $selecao, array $opcoes): array
    {
        return [
            'disponivel' => false,
            'selecao' => $selecao,
            'opcoes' => $opcoes,
            'politicoResumo' => null,
            'base' => null,
            'comparada' => null,
            'metricas' => [],
            'ganhos' => [],
            'quedas' => [],
            'mapaData' => [],
            'municipios' => [],
            'tabela' => [],
            'paginacao' => ['pagina' => 1, 'paginas' => 1, 'por_pagina' => 50, 'total' => 0],
            'busca' => '',
            'ordem' => 'delta_desc',
            'cobertura_incompleta' => false,
        ];
    }
}
