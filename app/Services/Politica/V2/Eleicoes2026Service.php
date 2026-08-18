<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\FonteEstado;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Eleicoes2026Service
{
    private const CACHE_KEY = 'politica:v2:eleicoes-2026:resumo';

    public function resumo(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(5), fn (): array => $this->montarResumo());
    }

    public function esquecerCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function montarResumo(): array
    {
        $partidoPrioritario = strtoupper((string) config('politica.tse.scope.partido_prioritario', 'REPUBLICANOS'));
        $candidaturas = Candidatura::query()
            ->where('origem', 'tse_dados_abertos')
            ->whereHas('eleicao', fn ($query) => $query->where('ano', 2026))
            ->with([
                'eleicao:id,ano,turno,descricao,status',
                'cargo:id,nome,ordem',
                'partido:id,sigla,nome,numero',
                'politico:id,nome_publico,slug,foto_url',
                'politico.acompanhamento:id,politico_id,grupo,prioridade,ordem,ativo',
            ])
            ->orderBy('cargo_id')
            ->orderBy('numero_urna')
            ->orderBy('nome_urna')
            ->get();

        $presidencia = $this->blocoCargo($candidaturas, 'Presidente', 'Todos os registros oficiais importados para Presidente.');
        $governo = $this->blocoCargo($candidaturas, 'Governador', 'Todos os registros oficiais importados para Governador da Bahia.');
        $senado = $this->blocoCargo(
            $candidaturas,
            'Senador',
            "Recorte econômico do banco: {$partidoPrioritario}."
        );

        $republicanos = $candidaturas
            ->filter(fn (Candidatura $candidatura) => strtoupper((string) $candidatura->partido?->sigla) === $partidoPrioritario)
            ->filter(fn (Candidatura $candidatura) => in_array($candidatura->cargo?->nome, ['Senador', 'Deputado Federal', 'Deputado Estadual'], true))
            ->groupBy(fn (Candidatura $candidatura) => $candidatura->cargo?->nome ?: 'Outros')
            ->map(fn (Collection $itens, string $cargo) => [
                'cargo' => $cargo,
                'total' => $itens->count(),
                'acompanhados' => $itens->filter(fn (Candidatura $c) => (bool) $c->politico?->acompanhamento?->ativo)->count(),
                'candidatos' => $itens->map(fn (Candidatura $c) => $this->candidato($c))->values()->all(),
            ])
            ->sortBy(fn (array $grupo) => match ($grupo['cargo']) {
                'Senador' => 10,
                'Deputado Federal' => 20,
                'Deputado Estadual' => 30,
                default => 99,
            })
            ->values()
            ->all();

        $ultimaSincronizacao = $candidaturas
            ->pluck('sincronizado_em')
            ->filter()
            ->sortDesc()
            ->first();

        $fonte = FonteEstado::query()
            ->where('chave', 'tse_dados_abertos:candidaturas:2026')
            ->first();

        $timezone = (string) config('politica.tse.registro_2026.timezone', 'America/Bahia');
        $prazo = CarbonImmutable::parse(
            (string) config('politica.tse.registro_2026.prazo', '2026-08-15 19:00:00'),
            $timezone
        );

        $apuracao = Apuracao::query()
            ->whereHas('eleicao', fn ($query) => $query->where('ano', 2026))
            ->with('cargo:id,nome')
            ->orderByDesc('sincronizado_em')
            ->orderByDesc('id')
            ->get()
            ->unique('cargo_id')
            ->mapWithKeys(fn (Apuracao $item) => [
                ($item->cargo?->nome ?: 'Sem cargo') => [
                    'status' => $item->status,
                    'percentual_secoes' => $item->percentual_secoes !== null ? (float) $item->percentual_secoes : null,
                    'secoes_totalizadas' => (int) $item->secoes_totalizadas,
                    'secoes_total' => (int) $item->secoes_total,
                    'totalizacao_final' => (bool) $item->totalizacao_final,
                    'sincronizado_em' => $item->sincronizado_em,
                ],
            ])
            ->all();

        return [
            'metricas' => [
                'candidaturas' => $candidaturas->count(),
                'acompanhadas' => $candidaturas->filter(fn (Candidatura $c) => (bool) $c->politico?->acompanhamento?->ativo)->count(),
                'partidos' => $candidaturas->pluck('partido.sigla')->filter()->unique()->count(),
                'cargos' => $candidaturas->pluck('cargo.nome')->filter()->unique()->count(),
            ],
            'presidencia' => $presidencia,
            'governo' => $governo,
            'senado' => $senado,
            'republicanos' => [
                'partido' => $partidoPrioritario,
                'total' => collect($republicanos)->sum('total'),
                'grupos' => $republicanos,
            ],
            'registro' => [
                'timezone' => $timezone,
                'prazo' => $prazo,
                'antes_prazo' => CarbonImmutable::now($timezone)->lt($prazo),
                'ultima_sincronizacao' => $ultimaSincronizacao
                    ? CarbonImmutable::parse($ultimaSincronizacao)->setTimezone($timezone)
                    : null,
            ],
            'fonte' => $fonte ? [
                'http_status' => $fonte->http_status,
                'ultimo_sucesso_em' => $fonte->ultimo_sucesso_em,
                'erros_consecutivos' => (int) $fonte->erros_consecutivos,
                'ultimo_erro' => $fonte->ultimo_erro,
                'url' => $fonte->url,
                'sha256' => data_get($fonte->meta, 'sha256'),
            ] : null,
            'apuracao' => $apuracao,
            'coletor' => [
                'live_enabled' => (bool) config('politica.apuracao.live_enabled', false),
                'schedule_enabled' => (bool) config('politica.apuracao.schedule_enabled', false),
                'poll_seconds' => (int) config('politica.apuracao.poll_seconds', 60),
                'max_requests_per_cycle' => (int) config('politica.apuracao.max_requests_per_cycle', 6),
                'modo' => (bool) config('politica.apuracao.live_enabled', false) ? 'pronto_para_coleta' : 'protegido',
            ],
        ];
    }

    private function blocoCargo(Collection $candidaturas, string $cargo, string $descricao): array
    {
        $itens = $candidaturas
            ->filter(fn (Candidatura $candidatura) => $candidatura->cargo?->nome === $cargo)
            ->sortBy(fn (Candidatura $candidatura) => sprintf('%06d|%s', (int) ($candidatura->numero_urna ?: 999999), $candidatura->nome_urna ?: $candidatura->politico?->nome_publico))
            ->values();

        return [
            'cargo' => $cargo,
            'descricao' => $descricao,
            'total' => $itens->count(),
            'partidos' => $itens->pluck('partido.sigla')->filter()->unique()->count(),
            'acompanhados' => $itens->filter(fn (Candidatura $c) => (bool) $c->politico?->acompanhamento?->ativo)->count(),
            'candidatos' => $itens->map(fn (Candidatura $c) => $this->candidato($c))->all(),
        ];
    }

    private function candidato(Candidatura $candidatura): array
    {
        $acompanhamento = $candidatura->politico?->acompanhamento;

        return [
            'id' => $candidatura->id,
            'politico' => $candidatura->politico?->nome_publico ?: $candidatura->nome_urna ?: 'Sem nome',
            'slug' => $candidatura->politico?->slug,
            'foto_url' => $candidatura->politico?->foto_url ?: $candidatura->foto_url,
            'nome_urna' => $candidatura->nome_urna,
            'numero' => $candidatura->numero_urna,
            'partido' => $candidatura->partido?->sigla,
            'partido_nome' => $candidatura->partido?->nome,
            'situacao' => $candidatura->situacaoRegistroExibicao(),
            'situacao_tom' => $candidatura->situacaoRegistroTom(),
            'situacao_codigo' => $candidatura->situacaoRegistroEhCodigoTecnico() ? $candidatura->situacao_registro : null,
            'tse_sq_candidato' => $candidatura->tse_sq_candidato,
            'sincronizado_em' => $candidatura->sincronizado_em,
            'acompanhado' => (bool) $acompanhamento?->ativo,
            'grupo_acompanhamento' => $acompanhamento?->grupo,
        ];
    }
}
