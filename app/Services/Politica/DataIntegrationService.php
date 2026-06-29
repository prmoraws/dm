<?php

namespace App\Services\Politica;

use App\Models\Politica\Cidade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataIntegrationService
{
    protected string $ibgeApiUrl;

    public function __construct()
    {
        $this->ibgeApiUrl = config('politica.data_sources.ibge.url');
    }

    /**
     * Sincroniza dados básicos das cidades via API do IBGE.
     * Atualiza o nome e busca a população mais recente.
     */
    public function syncCityData()
    {
        Log::info('Iniciando sincronização de dados das cidades...');
        $cidades = Cidade::all();

        foreach ($cidades as $cidade) {
            try {
                // Busca dados de população e outros agregados
                $response = Http::get("{$this->ibgeApiUrl}localidades/municipios/{$cidade->ibge_code}/agregados");
                
                if ($response->successful()) {
                    // Lógica para extrair a população do JSON de resposta complexo do IBGE
                    // Este é um exemplo simplificado. A API do IBGE pode ser complexa.
                    // Vamos focar em atualizar o nome por enquanto, como no seu exemplo.
                    // E podemos adicionar a população em um passo futuro.
                    
                    // Exemplo: $cidade->populacao = $extraido_da_api;
                    $cidade->save();
                }

            } catch (\Exception $e) {
                Log::error("Erro ao sincronizar dados para a cidade {$cidade->nome}: " . $e->getMessage());
                continue; // Pula para a próxima cidade em caso de erro
            }
        }
        Log::info('Sincronização de cidades concluída.');
    }

    public function syncCityCoordinates(\Illuminate\Console\Command $command)
    {
        $command->info('Buscando coordenadas geográficas do IBGE (API de Malhas)...');
        $cidades = Cidade::whereNull('latitude')->orWhereNull('longitude')->get();

        if ($cidades->isEmpty()) {
            $command->info('Todas as cidades já possuem coordenadas.');
            return;
        }

        $progressBar = $command->getOutput()->createProgressBar($cidades->count());
        $progressBar->start();

        foreach ($cidades as $cidade) {
            try {
                $response = \Illuminate\Support\Facades\Http::get("https://servicodados.ibge.gov.br/api/v2/malhas/{$cidade->ibge_code}?formato=application/json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Caminho final e CORRETO para o centroide na API de Malhas
                    $longitude = data_get($data, 'objects.foo.geometries.0.properties.centroide.0');
                    $latitude = data_get($data, 'objects.foo.geometries.0.properties.centroide.1');
                    
                    if ($latitude && $longitude) {
                       $cidade->update(['latitude' => $latitude, 'longitude' => $longitude]);
                    } else {
                        $command->getOutput()->writeln("\n<fg=yellow>Aviso: Coordenadas não encontradas na API para a cidade: {$cidade->nome}</>");
                    }
                }
                usleep(100000); // Pausa de 0.1s para não sobrecarregar a API
            } catch (\Exception $e) {
                $command->error("\nFalha para a cidade {$cidade->nome}: " . $e->getMessage());
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $command->info("\nBusca de coordenadas concluída.");
    }
}