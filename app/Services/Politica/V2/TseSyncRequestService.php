<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\TseImportacao;
use App\Models\Politica\V2\TseSolicitacao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TseSyncRequestService
{
    public const HEARTBEAT_KEY = 'politica:tse:scheduler:last_run';

    public function solicitar(
        int $ano = 2026,
        string $uf = 'BA',
        string $escopo = 'espelho',
        string $somente = 'candidaturas',
        string $origem = 'manual',
        ?int $solicitanteUserId = null,
    ): array {
        $uf = strtoupper($uf);
        $escopo = strtolower($escopo);
        $somente = strtolower($somente);
        $origem = strtolower($origem);

        return DB::transaction(function () use ($ano, $uf, $escopo, $somente, $origem, $solicitanteUserId) {
            $emAndamento = TseSolicitacao::query()
                ->where('ano', $ano)
                ->where('uf', $uf)
                ->where('escopo', $escopo)
                ->where('somente', $somente)
                ->whereIn('status', ['pendente', 'executando'])
                ->lockForUpdate()
                ->oldest('id')
                ->first();

            if ($emAndamento) {
                return [
                    'created' => false,
                    'reason' => 'already_pending',
                    'request' => $emAndamento,
                ];
            }

            if ($origem === 'manual') {
                $cooldown = max(0, (int) config('politica.tse.automation.manual_cooldown_minutes', 10));
                if ($cooldown > 0) {
                    $recente = TseSolicitacao::query()
                        ->where('ano', $ano)
                        ->where('uf', $uf)
                        ->where('somente', $somente)
                        ->where('origem', 'manual')
                        ->where('solicitada_em', '>=', now()->subMinutes($cooldown))
                        ->latest('id')
                        ->first();

                    if ($recente) {
                        return [
                            'created' => false,
                            'reason' => 'cooldown',
                            'request' => $recente,
                        ];
                    }
                }
            }

            $solicitacao = TseSolicitacao::query()->create([
                'ano' => $ano,
                'uf' => $uf,
                'escopo' => $escopo,
                'somente' => $somente,
                'origem' => $origem,
                'status' => 'pendente',
                'solicitante_user_id' => $solicitanteUserId,
                'solicitada_em' => now(),
                'meta' => [
                    'forcar_download' => true,
                    'economico' => true,
                ],
            ]);

            return [
                'created' => true,
                'reason' => 'created',
                'request' => $solicitacao,
            ];
        });
    }

    public function estado(int $ano = 2026, string $uf = 'BA', string $somente = 'candidaturas'): array
    {
        $uf = strtoupper($uf);
        $somente = strtolower($somente);

        $ultimaSolicitacao = TseSolicitacao::query()
            ->where('ano', $ano)
            ->where('uf', $uf)
            ->where('somente', $somente)
            ->latest('id')
            ->first();

        $ultimaImportacao = TseImportacao::query()
            ->where('ano', $ano)
            ->where('uf', $uf)
            ->where('tipo', $somente)
            ->where('status', 'concluida')
            ->orderByDesc('concluida_em')
            ->first();

        $heartbeatRaw = Cache::get(self::HEARTBEAT_KEY);
        $heartbeat = null;
        $schedulerAtivo = false;

        if (is_string($heartbeatRaw) && $heartbeatRaw !== '') {
            try {
                $heartbeat = Carbon::parse($heartbeatRaw);
                $schedulerAtivo = $heartbeat->gte(now()->subMinutes(3));
            } catch (\Throwable) {
                $heartbeat = null;
            }
        }

        return [
            'ultima_solicitacao' => $ultimaSolicitacao,
            'ultima_importacao' => $ultimaImportacao,
            'scheduler_heartbeat' => $heartbeat,
            'scheduler_ativo' => $schedulerAtivo,
            'automatico_ativo' => (bool) config('politica.tse.automation.automatic_enabled', true),
            'fila_ativa' => (bool) config('politica.tse.automation.queue_enabled', true),
            'cron' => (string) config('politica.tse.automation.cron', '17 3 * * *'),
            'timezone' => (string) config('politica.tse.automation.timezone', 'America/Bahia'),
        ];
    }

    public function registrarHeartbeat(): void
    {
        Cache::forever(self::HEARTBEAT_KEY, now()->toIso8601String());
    }
}
