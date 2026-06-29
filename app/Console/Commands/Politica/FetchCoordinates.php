<?php
namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Services\Politica\DataIntegrationService;

class FetchCoordinates extends Command
{
    protected $signature = 'politica:fetch-coordinates';
    protected $description = 'Busca e salva as coordenadas geográficas das cidades via API do IBGE.';

    public function handle(DataIntegrationService $dataService)
    {
        // Passamos o próprio objeto de comando para o serviço
        // para que ele possa interagir com o terminal (ex: barra de progresso)
        $dataService->syncCityCoordinates($this);
        return 0;
    }
}