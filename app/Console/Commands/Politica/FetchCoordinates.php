<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\Cidade;
use App\Services\Politica\DataIntegrationService;
use Illuminate\Console\Command;

class FetchCoordinates extends Command
{
    protected $signature = 'politica:fetch-coordinates
                            {--skip-reconcile : Não reconcilia os códigos municipais com a lista oficial do IBGE}
                            {--refresh : Recalcula coordenadas de todos os municípios oficiais}';

    protected $description = 'Reconcilia municípios oficiais e atualiza coordenadas geográficas via IBGE.';

    public function handle(DataIntegrationService $dataService): int
    {
        $forceCityIds = [];

        if (! $this->option('skip-reconcile')) {
            $this->info('Conferindo a base municipal com a lista oficial do IBGE...');

            try {
                $resumo = $dataService->reconcileOfficialMunicipalities();
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
                return self::FAILURE;
            }

            $forceCityIds = $resumo['changed_ids'];

            $this->table(['Indicador', 'Quantidade'], [
                ['Municípios oficiais', $resumo['oficiais']],
                ['Códigos IBGE corrigidos', $resumo['codigos_corrigidos']],
                ['Registros auxiliares/legados fora da base oficial', count($resumo['extras'])],
            ]);

            if ($resumo['extras'] !== []) {
                $this->warn('Registros preservados sem ibge_code: ' . implode(', ', $resumo['extras']));
            }
        }

        if ($this->option('refresh')) {
            $forceCityIds = Cidade::query()
                ->whereNotNull('ibge_code')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        $dataService->syncCityCoordinates($this, $forceCityIds);

        return self::SUCCESS;
    }
}
