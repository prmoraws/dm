<?php
namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\Cidade;
use Illuminate\Support\Facades\File;

class GenerateCoordsSql extends Command
{
    protected $signature = 'politica:generate-coords-sql';
    protected $description = 'Gera um arquivo SQL com os comandos UPDATE para as coordenadas das cidades.';

    public function handle()
    {
        $this->info('Gerando script SQL para atualização de coordenadas...');
        $cidades = Cidade::whereNotNull('latitude')->whereNotNull('longitude')->get();
        $sqlContent = "";

        if ($cidades->isEmpty()) {
            $this->warn('Nenhuma cidade com coordenadas para exportar.');
            return 1;
        }

        foreach ($cidades as $cidade) {
            $sqlContent .= "UPDATE `politica_cidades` SET `latitude` = {$cidade->latitude}, `longitude` = {$cidade->longitude} WHERE `id` = {$cidade->id};\n";
        }

        $filePath = storage_path('app/coordenadas_update.sql');
        File::put($filePath, $sqlContent);

        $this->info("Script SQL gerado com sucesso em: {$filePath}");
        return 0;
    }
}