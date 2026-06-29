<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\Cidade;
use Illuminate\Support\Facades\DB;

class CleanDuplicateCities extends Command
{
    protected $signature = 'politica:clean-duplicates';
    protected $description = 'Padroniza nomes de cidades para uppercase e mescla registros duplicados.';

    public function handle()
    {
        $this->info('Iniciando limpeza e padronização das cidades...');

        DB::transaction(function () {
            // Passo 1: Padronizar todos os nomes para UPPERCASE
            $this->line('Padronizando todos os nomes de cidade para maiúsculas...');
            Cidade::all()->each(function ($cidade) {
                $cidade->nome = mb_strtoupper($cidade->nome, 'UTF-8');
                $cidade->saveQuietly(); // Salva sem disparar eventos
            });

            // Passo 2: Encontrar nomes duplicados
            $duplicateNames = DB::table('politica_cidades')
                ->select('nome')
                ->groupBy('nome')
                ->havingRaw('COUNT(nome) > 1')
                ->pluck('nome');

            if ($duplicateNames->isEmpty()) {
                $this->info('Nenhuma cidade duplicada encontrada. A base já está limpa.');
                return;
            }

            $this->warn('Encontradas cidades duplicadas: ' . $duplicateNames->implode(', '));

            // Passo 3: Mesclar duplicatas
            foreach ($duplicateNames as $name) {
                $this->line("Processando duplicatas para: {$name}");

                // Pega todas as cidades com o mesmo nome, ordenadas pelo ID (a primeira é a original)
                $cities = Cidade::where('nome', $name)->orderBy('id', 'asc')->get();
                $originalCity = $cities->shift(); // Pega a primeira e a remove da coleção

                foreach ($cities as $duplicateCity) {
                    $this->line("Mesclando ID {$duplicateCity->id} no ID {$originalCity->id}...");

                    // Reatribui os registros relacionados para a cidade original
                    $duplicateCity->bairros()->update(['cidade_id' => $originalCity->id]);
                    $duplicateCity->espelho()->update(['cidade_id' => $originalCity->id]);
                    $duplicateCity->projecoes()->update(['cidade_id' => $originalCity->id]);
                    
                    // Apaga a cidade duplicada que agora está vazia
                    $duplicateCity->delete();
                }
            }
        });

        $this->info('Limpeza concluída com sucesso!');
        return 0;
    }
}