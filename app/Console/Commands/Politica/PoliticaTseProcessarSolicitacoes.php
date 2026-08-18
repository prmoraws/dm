<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\V2\TseSolicitacao;
use App\Services\Politica\V2\TseSyncRequestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class PoliticaTseProcessarSolicitacoes extends Command
{
    protected $signature = 'politica:tse-processar-solicitacoes {--limite=1 : Máximo de solicitações por execução}';

    protected $description = 'Processa a fila persistente de atualizações TSE solicitadas pelo painel ou pelo agendamento automático.';

    public function handle(TseSyncRequestService $service): int
    {
        $service->registrarHeartbeat();

        if (! (bool) config('politica.tse.automation.queue_enabled', true)) {
            $this->line('Fila automática do TSE desabilitada por configuração.');
            return self::SUCCESS;
        }

        $lockSeconds = max(60, (int) config('politica.tse.automation.processor_lock_seconds', 1800));
        $lock = Cache::lock('politica:tse:solicitacoes:processor', $lockSeconds);

        if (! $lock->get()) {
            $this->line('Outro processador de solicitações TSE já está em execução.');
            return self::SUCCESS;
        }

        try {
            $this->recuperarTravadas();
            $limite = max(1, min(5, (int) $this->option('limite')));
            $processadas = 0;

            while ($processadas < $limite) {
                $solicitacao = $this->reservarProxima();
                if (! $solicitacao) {
                    if ($processadas === 0) {
                        $this->line('Nenhuma atualização TSE pendente.');
                    }
                    break;
                }

                $this->info("Processando solicitação #{$solicitacao->id}: {$solicitacao->ano}/{$solicitacao->uf} {$solicitacao->somente}.");

                try {
                    $exitCode = Artisan::call('politica:tse-sincronizar', [
                        'ano' => $solicitacao->ano,
                        '--uf' => $solicitacao->uf,
                        '--escopo' => $solicitacao->escopo,
                        '--somente' => $solicitacao->somente,
                        '--forcar-download' => true,
                    ]);

                    $output = trim(Artisan::output());

                    if ($exitCode !== self::SUCCESS) {
                        throw new \RuntimeException($output !== '' ? $output : "Sincronização retornou código {$exitCode}.");
                    }

                    $fotoAviso = null;
                    if ((int) $solicitacao->ano === 2026 && $solicitacao->somente === 'candidaturas') {
                        try {
                            $fotoExit = Artisan::call('politica:fotos-oficiais', ['ano' => 2026]);
                            if ($fotoExit !== self::SUCCESS) {
                                $fotoAviso = trim(Artisan::output()) ?: 'A sincronização de fotos oficiais retornou erro.';
                            }
                        } catch (Throwable $fotoErro) {
                            // Foto é enriquecimento visual: nunca invalida a sincronização principal de candidaturas.
                            $fotoAviso = $fotoErro->getMessage();
                        }
                    }

                    $solicitacao->update([
                        'status' => 'concluida',
                        'concluida_em' => now(),
                        'ultimo_erro' => null,
                        'meta' => array_merge($solicitacao->meta ?? [], [
                            'saida' => mb_substr($output, -12000),
                            'fotos_aviso' => $fotoAviso ? mb_substr($fotoAviso, 0, 2000) : null,
                            'processada_em' => now()->toIso8601String(),
                        ]),
                    ]);

                    $this->info("Solicitação #{$solicitacao->id} concluída.");
                } catch (Throwable $e) {
                    $solicitacao->update([
                        'status' => 'erro',
                        'concluida_em' => now(),
                        'ultimo_erro' => mb_substr($e->getMessage(), 0, 65000),
                    ]);
                    $this->error("Solicitação #{$solicitacao->id} falhou: {$e->getMessage()}");
                }

                $processadas++;
            }

            return self::SUCCESS;
        } finally {
            optional($lock)->release();
        }
    }

    private function recuperarTravadas(): void
    {
        $staleMinutes = max(5, (int) config('politica.tse.automation.stale_minutes', 30));

        TseSolicitacao::query()
            ->where('status', 'executando')
            ->whereNotNull('iniciada_em')
            ->where('iniciada_em', '<', now()->subMinutes($staleMinutes))
            ->update([
                'status' => 'pendente',
                'iniciada_em' => null,
                'ultimo_erro' => 'Solicitação recuperada automaticamente após interrupção do processador.',
                'updated_at' => now(),
            ]);
    }

    private function reservarProxima(): ?TseSolicitacao
    {
        return DB::transaction(function () {
            $solicitacao = TseSolicitacao::query()
                ->where('status', 'pendente')
                ->orderBy('solicitada_em')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $solicitacao) {
                return null;
            }

            $solicitacao->update([
                'status' => 'executando',
                'iniciada_em' => now(),
                'concluida_em' => null,
                'ultimo_erro' => null,
            ]);

            return $solicitacao->fresh();
        });
    }
}
