<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\TseSyncRequestService;
use Illuminate\Console\Command;

class PoliticaTseSolicitarAtualizacao extends Command
{
    protected $signature = 'politica:tse-solicitar-atualizacao
        {ano=2026 : Ano eleitoral}
        {--uf=BA : UF principal}
        {--escopo=espelho : Escopo de importação}
        {--somente=candidaturas : candidaturas ou resultados}
        {--origem=cli : cli, manual ou automatica}';

    protected $description = 'Enfileira uma atualização oficial do TSE para execução segura pelo scheduler.';

    public function handle(TseSyncRequestService $service): int
    {
        $resultado = $service->solicitar(
            (int) $this->argument('ano'),
            (string) $this->option('uf'),
            (string) $this->option('escopo'),
            (string) $this->option('somente'),
            (string) $this->option('origem'),
            null,
        );

        $solicitacao = $resultado['request'];

        if ($resultado['created']) {
            $this->info("Atualização TSE enfileirada. Solicitação #{$solicitacao->id}.");
            return self::SUCCESS;
        }

        $this->line("Nenhuma nova solicitação criada. Já existe/foi solicitada recentemente (#{$solicitacao->id}, status {$solicitacao->status}).");
        return self::SUCCESS;
    }
}
