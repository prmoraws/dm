<?php
namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Services\Politica\DataIntegrationService;

class SyncData extends Command
{
    protected $signature = 'politica:sync-data';
    protected $description = 'Sincroniza dados leves de fontes externas (IBGE, etc)';

    public function handle(DataIntegrationService $dataService)
    {
        $this->info('Iniciando sincronização de dados...');
        $dataService->syncCityData();
        $this->info('Sincronização finalizada com sucesso!');
        return 0;
    }
}