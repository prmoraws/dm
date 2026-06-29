<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\{Cidade, Espelho};
use Illuminate\Support\LazyCollection;

class ImportCityData extends Command
{
    protected $signature = 'politica:import-city-data 
                            {--populacao= : O caminho para o CSV de população a partir da raiz do projeto} 
                            {--prefeitos= : O caminho para o CSV de prefeitos eleitos a partir da raiz do projeto}';

    protected $description = 'Importa dados de população (IBGE) e prefeitos eleitos (TSE) a partir de arquivos CSV.';

    public function handle()
    {
        $populacaoFile = $this->option('populacao');
        $prefeitosFile = $this->option('prefeitos');

        if ($populacaoFile) {
            $this->importarPopulacao($populacaoFile);
        }

        if ($prefeitosFile) {
            $this->importarPrefeitos($prefeitosFile);
        }

        $this->info('Processo de importação de dados da cidade concluído.');
        return 0;
    }

    private function importarPopulacao(string $filename)
    {
        $filepath = base_path($filename);
        if (!file_exists($filepath)) {
            $this->error("Arquivo de população não encontrado: {$filepath}");
            return;
        }

        $this->info("Importando dados de população de {$filename}...");
        LazyCollection::make(fn() => $this->readCsv($filepath))
            ->each(function ($row) {
                $nomeMunicipio = mb_strtoupper($row[0] ?? '', 'UTF-8');
                $populacao = (int)($row[1] ?? 0);

                if (!empty($nomeMunicipio) && $populacao > 0) {
                    Cidade::where('nome', $nomeMunicipio)->update(['populacao' => $populacao]);
                }
            });
        $this->info('Dados de população importados com sucesso.');
    }

    private function importarPrefeitos(string $filename)
    {
        $filepath = base_path($filename);
        if (!file_exists($filepath)) {
            $this->error("Arquivo de prefeitos não encontrado: {$filepath}");
            return;
        }

        $this->info("Importando dados de prefeitos eleitos de {$filename}...");
        LazyCollection::make(fn() => $this->readCsv($filepath))
            ->each(function ($row) {
                $nomeMunicipio = mb_strtoupper($row[1] ?? '', 'UTF-8');
                $cidade = Cidade::firstWhere('nome', $nomeMunicipio);

                if ($cidade) {
                    Espelho::updateOrCreate(
                        ['cidade_id' => $cidade->id],
                        [
                            'prefeito_atual_nome' => $row[0] ?? null,
                            'prefeito_atual_partido' => $row[2] ?? null,
                            'prefeito_atual_votos' => (int)($row[3] ?? 0),
                        ]
                    );
                }
            });
        $this->info('Dados de prefeitos importados com sucesso.');
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