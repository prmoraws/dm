<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\FonteEstado;
use App\Models\Politica\V2\MigracaoDados;
use Illuminate\Support\Facades\Cache;

class PoliticaDashboardService
{
    public function resumo(): array
    {
        return Cache::remember('politica:v2:dashboard:resumo', now()->addMinutes(5), function (): array {
            $acompanhamentos = Acompanhamento::query()
                ->with([
                    'politico.candidaturas' => fn ($query) => $query
                        ->with(['eleicao', 'cargo', 'partido'])
                        ->orderByDesc('eleicao_id'),
                ])
                ->where('ativo', true)
                ->orderBy('ordem')
                ->get();

            $prioritariosComHistorico = $acompanhamentos
                ->filter(fn ($acompanhamento) => $acompanhamento->politico?->candidaturas?->isNotEmpty())
                ->count();

            $ultimaMigracao = MigracaoDados::query()
                ->orderByDesc('concluida_em')
                ->orderByDesc('id')
                ->first();

            $totalFontes = FonteEstado::query()->count();
            $fontesSaudaveis = FonteEstado::query()
                ->whereIn('http_status', [200, 304])
                ->where('erros_consecutivos', 0)
                ->count();
            $fontesComErro = FonteEstado::query()
                ->where('erros_consecutivos', '>', 0)
                ->count();

            return [
                'metricas' => [
                    'cidades' => Cidade::query()->whereNotNull('ibge_code')->count(),
                    'acompanhamentos' => $acompanhamentos->count(),
                    'com_historico' => $prioritariosComHistorico,
                    'eleicoes' => Eleicao::query()->count(),
                    'candidaturas' => Candidatura::query()->count(),
                ],
                'grupos' => $acompanhamentos
                    ->groupBy('grupo')
                    ->map(fn ($itens) => $itens->values())
                    ->all(),
                'ultima_migracao' => $ultimaMigracao ? [
                    'chave' => $ultimaMigracao->chave,
                    'status' => $ultimaMigracao->status,
                    'concluida_em' => $ultimaMigracao->concluida_em?->toIso8601String(),
                    'estatisticas' => $ultimaMigracao->estatisticas,
                ] : null,
                'fontes' => [
                    'total' => $totalFontes,
                    'saudaveis' => $fontesSaudaveis,
                    'com_erro' => $fontesComErro,
                ],
            ];
        });
    }

    public function esquecerCache(): void
    {
        Cache::forget('politica:v2:dashboard:resumo');
    }
}
