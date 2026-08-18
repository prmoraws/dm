<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\TseApuracaoOrchestrator;
use Illuminate\Console\Command;

class PoliticaApuracaoStatus extends Command
{
    protected $signature = 'politica:apuracao-status {--ano=2026} {--turno=1} {--uf=BA}';
    protected $description = 'Exibe a prontidão do coletor de apuração sem fazer requisições ao TSE';

    public function handle(TseApuracaoOrchestrator $service): int
    {
        $status = $service->status((int) $this->option('ano'), (int) $this->option('turno'), strtoupper((string) $this->option('uf')));

        $this->table(['Indicador', 'Valor'], [
            ['Coleta ao vivo', $status['live_enabled'] ? 'HABILITADA' : 'desabilitada (seguro)'],
            ['Agendamento', $status['schedule_enabled'] ? 'HABILITADO' : 'desabilitado'],
            ['Intervalo mínimo', $status['poll_seconds'].' s'],
            ['Máx. requisições/ciclo', $status['max_requests_per_cycle']],
            ['Eleição local', $status['eleicao_id'] ?: 'não localizada'],
            ['Código TSE', $status['tse_eleicao_codigo'] ?: 'não localizado'],
        ]);

        if ($status['ea14_url']) {
            $this->newLine();
            $this->line('<info>EA14:</info> '.$status['ea14_url']);
            foreach ($status['ea20'] as $cargo => $url) {
                $this->line("<info>EA20 {$cargo}:</info> {$url}");
            }
        }

        $this->newLine();
        $this->comment('Nenhuma requisição externa foi feita por este comando.');

        return self::SUCCESS;
    }
}
