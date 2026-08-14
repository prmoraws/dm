<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\LegacyV1MigrationService;
use Illuminate\Console\Command;
use Throwable;

class PoliticaV2MigrarLegado extends Command
{
    protected $signature = 'politica:v2-migrar-legado
                            {--escopo=prioritarios : prioritarios ou todos}
                            {--dry-run : Apenas diagnostica; não grava dados}';

    protected $description = 'Migra dados úteis da Política V1 para a arquitetura V2 de forma idempotente e rastreável.';

    public function handle(LegacyV1MigrationService $service): int
    {
        $escopo = mb_strtolower(trim((string) $this->option('escopo')), 'UTF-8');

        try {
            $diagnostico = $service->diagnostico($escopo);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Política V1 → V2');
        $this->table(
            ['Indicador', 'Valor'],
            collect($diagnostico)->map(fn ($valor, $chave) => [
                $chave,
                is_array($valor) ? implode(', ', $valor) : ($valor ?? 'todos'),
            ])->values()->all()
        );

        if ($this->option('dry-run')) {
            $this->comment('Dry-run concluído. Nenhuma alteração foi gravada.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Executar a migração idempotente deste escopo?', true)) {
            $this->warn('Migração cancelada.');
            return self::SUCCESS;
        }

        try {
            $stats = $service->migrar($escopo);
        } catch (Throwable $e) {
            report($e);
            $this->error('Falha na migração: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Migração concluída.');
        $this->table(
            ['Indicador', 'Quantidade'],
            collect($stats)->map(fn ($valor, $chave) => [$chave, $valor])->values()->all()
        );

        return self::SUCCESS;
    }
}
