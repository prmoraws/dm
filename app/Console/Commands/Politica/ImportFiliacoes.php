<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\Cidade;
use App\Models\Politica\Espelho;
use Illuminate\Support\LazyCollection;

class ImportFiliacoes extends Command
{
    protected $signature = 'politica:import-filiacoes {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}';
    protected $description = 'Importa o número de filiações do partido por cidade a partir de um arquivo CSV.';

    public function handle()
    {
        $filepath = base_path($this->argument('filepath'));
        if (!file_exists($filepath)) {
            $this->error("Arquivo não encontrado em: {$filepath}");
            return 1;
        }

        $this->info("Iniciando importação do número de filiações...");

        LazyCollection::make(fn() => $this->readCsv($filepath))
            ->each(function ($row) {
                // Mapeamento das colunas do seu CSV (municipio, filiacoes)
                $nomeMunicipio = mb_strtoupper($row[0] ?? '', 'UTF-8');
                $filiacoes = (int)($row[1] ?? 0);

                if (!empty($nomeMunicipio) && $filiacoes > 0) {
                    $cidade = Cidade::firstWhere('nome', $nomeMunicipio);
                    if ($cidade) {
                        Espelho::updateOrCreate(
                            ['cidade_id' => $cidade->id],
                            ['filiados_republicanos' => $filiacoes]
                        );
                    }
                }
            });

        $this->info('Importação do número de filiações concluída com sucesso.');
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