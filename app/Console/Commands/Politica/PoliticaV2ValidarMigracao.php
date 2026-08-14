<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\LegacyV1MigrationService;
use Illuminate\Console\Command;
use Throwable;

class PoliticaV2ValidarMigracao extends Command
{
    protected $signature = 'politica:v2-validar-migracao
                            {--escopo=prioritarios : prioritarios ou todos}';

    protected $description = 'Confere totais V1 x V2 por candidatura, seção e município.';

    public function handle(LegacyV1MigrationService $service): int
    {
        $escopo = mb_strtolower(trim((string) $this->option('escopo')), 'UTF-8');

        try {
            $validacao = $service->validarMigracao($escopo);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->table(
            ['ID V1', 'Nome', 'Ano', 'Cargo', 'V1', 'V2 candidatura', 'V2 seções', 'V2 municípios', 'Status'],
            collect($validacao['candidaturas'])->map(fn (array $linha) => [
                $linha['legacy_candidato_id'],
                $linha['nome'],
                $linha['ano'],
                $linha['cargo'],
                $linha['v1'],
                $linha['v2_candidatura'] ?? '-',
                $linha['v2_secoes'] ?? '-',
                $linha['v2_municipios'] ?? '-',
                $linha['ok'] ? 'OK' : 'ERRO',
            ])->all()
        );

        $espelhos = $validacao['espelhos'];
        $this->line(sprintf(
            'Espelhos operacionais: V1=%d | V2=%d | %s',
            $espelhos['v1'],
            $espelhos['v2'],
            $espelhos['ok'] ? 'OK' : 'ERRO'
        ));

        if (! $validacao['ok']) {
            $this->error('Validação encontrou divergências. Não avance para remover a V1.');
            return self::FAILURE;
        }

        $this->info('Validação aprovada: os totais migrados conferem com a V1.');
        return self::SUCCESS;
    }
}
