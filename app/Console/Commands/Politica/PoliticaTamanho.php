<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\PoliticaStorageService;
use Illuminate\Console\Command;

class PoliticaTamanho extends Command
{
    protected $signature = 'politica:tamanho {--top=15 : Quantidade de maiores tabelas politica_* a exibir}';

    protected $description = 'Mostra o tamanho real do módulo Política no MySQL, incluindo dados e índices.';

    public function handle(PoliticaStorageService $storage): int
    {
        $snapshot = $storage->snapshot();

        $this->info('Armazenamento do módulo Política');
        $this->table(['Indicador', 'Valor'], [
            ['Driver', $snapshot['driver']],
            ['Medição exata', $snapshot['exact'] ? 'sim' : 'não'],
            ['Banco completo', $storage->human($snapshot['database_bytes'])],
            ['Tabelas politica_*', $storage->human($snapshot['politica_bytes'])],
            ['Aviso configurado', $storage->human($snapshot['warning_bytes'])],
            ['Limite de segurança', $storage->human($snapshot['hard_limit_bytes'])],
        ]);

        if (! $snapshot['exact']) {
            $this->warn('O driver atual não expõe DATA_LENGTH/INDEX_LENGTH. Em MySQL/MariaDB a medição será exata.');
            return self::SUCCESS;
        }

        $top = max(1, min(100, (int) $this->option('top')));
        $rows = array_slice($snapshot['tables'], 0, $top);
        $this->table(
            ['Tabela', 'Dados', 'Índices', 'Total'],
            array_map(fn (array $row) => [
                $row['table'],
                $storage->human($row['data_bytes']),
                $storage->human($row['index_bytes']),
                $storage->human($row['total_bytes']),
            ], $rows)
        );

        return self::SUCCESS;
    }
}
