<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\Cidade;
use Illuminate\Support\LazyCollection;

class ImportCadeiras extends Command
{
    protected $signature = 'politica:import-cadeiras {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}';
    protected $description = 'Importa o número de cadeiras de vereadores por cidade a partir de um arquivo CSV.';

    public function handle()
    {
        $filepath = base_path($this->argument('filepath'));
        if (!file_exists($filepath)) {
            $this->error("Arquivo não encontrado em: {$filepath}");
            return 1;
        }

        $this->info("Iniciando importação do número de cadeiras...");

        LazyCollection::make(fn() => $this->readCsv($filepath))
            ->each(function ($row) {
                // Mapeamento das colunas do seu CSV
                $nomeMunicipio = mb_strtoupper($row[0] ?? '', 'UTF-8');
                $cadeiras = (int)($row[1] ?? 0);

                if (!empty($nomeMunicipio) && $cadeiras > 0) {
                    Cidade::where('nome', $nomeMunicipio)->update(['cadeiras_camara' => $cadeiras]);
                }
            });

        $this->info('Importação do número de cadeiras concluída com sucesso.');
        return 0;
    }

    private function readCsv(string $path)
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle); // Pula cabeçalho
        while (($row = fgetcsv($handle)) !== false) {
            yield $row;
        }
        fclose($handle);
    }
}