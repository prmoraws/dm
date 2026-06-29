<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\{Cidade, Candidato, Espelho};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ImportMunicipalResults extends Command
{
    protected $signature = 'politica:import-municipal 
                            {filepath : O caminho para o arquivo CSV}
                            {--ano= : O ano da eleição}
                            {--cargo= : O cargo a ser importado ("PREFEITO" ou "VEREADOR")}
                            {--candidato=* : (Opcional) Nomes de candidatos para filtrar}
                            {--lista= : (Opcional) Arquivo .txt com a lista de candidatos de interesse}';

    protected $description = 'Importa resultados de eleições municipais, com opção de filtrar por candidato ou lista.';

    public function handle()
    {
        $filepath = base_path($this->argument('filepath'));
        $ano = $this->option('ano');
        $cargoFilter = mb_strtoupper($this->option('cargo'), 'UTF-8');
        
        if (empty($cargoFilter) || empty($ano)) {
            $this->error('Os filtros --cargo e --ano são obrigatórios.');
            return 1;
        }
        if (!file_exists($filepath)) {
            $this->error("Arquivo de votos não encontrado em: {$filepath}");
            return 1;
        }

        $candidatosInteresse = $this->loadInterestList();

        $this->info("Iniciando importação para o cargo de {$cargoFilter} do ano de {$ano}...");
        
        DB::beginTransaction();
        $cidadesCache = Cidade::pluck('id', 'nome')->toArray();
        $candidatosCache = Candidato::pluck('id', 'nome')->toArray();

        LazyCollection::make(fn() => $this->readCsv($filepath))
            ->chunk(500)
            ->each(function (LazyCollection $chunk) use ($ano, $cargoFilter, $candidatosInteresse, &$cidadesCache, &$candidatosCache) {
                foreach ($chunk as $row) {
                    $cargo = mb_strtoupper($row[13] ?? '', 'UTF-8');
                    if ($cargo !== $cargoFilter) continue;

                    $nomeMunicipio = mb_strtoupper($row[8] ?? '', 'UTF-8');
                    $nomeVotavel = $row[15] ?? null;

                    if (empty($nomeMunicipio) || empty($nomeVotavel)) continue;

                    if ($cargo === 'PREFEITO') {
                        $this->processaPrefeito($row, $cidadesCache);
                    }
                    
                    if ($cargo === 'VEREADOR' && ($candidatosInteresse->isEmpty() || $candidatosInteresse->has($nomeVotavel))) {
                        $this->processaVereador($row, $ano, $cidadesCache, $candidatosCache);
                    }
                }
            });

        DB::commit();
        $this->info("\n\nImportação de resultados municipais concluída!");
        return 0;
    }

    // O restante do código (readCsv, processaPrefeito, etc.) permanece o mesmo...

    private function readCsv(string $path)
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, ';');
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            yield array_map(fn($item) => mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1'), $row);
        }
        fclose($handle);
    }

    private function processaPrefeito(array $row, array &$cidadesCache)
    {
        $situacao = $row[23] ?? '';
        if (in_array($situacao, ['ELEITO', 'ELEITO POR QP', 'ELEITO POR MÉDIA'])) {
            $nomeMunicipio = mb_strtoupper($row[8] ?? '', 'UTF-8');
            
            if (isset($cidadesCache[$nomeMunicipio])) {
                $cidadeId = $cidadesCache[$nomeMunicipio];
                Espelho::updateOrCreate(
                    ['cidade_id' => $cidadeId],
                    [
                        'prefeito_atual_nome' => $row[15] ?? null,
                        'prefeito_atual_partido' => $row[22] ?? '',
                        'prefeito_atual_votos' => (int)($row[16] ?? 0),
                    ]
                );
            }
        }
    }

    private function processaVereador(array $row, int $ano, array &$cidadesCache, array &$candidatosCache)
    {
        $nomeMunicipio = mb_strtoupper($row[8] ?? '', 'UTF-8');
        if (!isset($cidadesCache[$nomeMunicipio])) return;
        $cidadeId = $cidadesCache[$nomeMunicipio];
        
        $nomeVotavel = $row[15] ?? null;
        if (!isset($candidatosCache[$nomeVotavel])) {
            $candidato = Candidato::create(['nome' => $nomeVotavel, 'partido' => $row[22] ?? '']);
            $candidatosCache[$nomeVotavel] = $candidato->id;
        }
        $candidatoId = $candidatosCache[$nomeVotavel];

        DB::table('politica_resultados_municipio')->insert([
            'cidade_id' => $cidadeId,
            'candidato_id' => $candidatoId,
            'ano_eleicao' => $ano,
            'cargo' => 'VEREADOR',
            'votos_recebidos' => (int)($row[16] ?? 0),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function loadInterestList()
    {
        $fromCli = collect($this->option('candidato'));
        $fromFile = collect();
        $listaFilename = $this->option('lista');

        if ($listaFilename) {
            $listaPath = Storage::path($listaFilename);
            if (file_exists($listaPath)) {
                $fromFile = collect(file($listaPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                $this->info($fromFile->count() . ' candidatos carregados do arquivo ' . $listaFilename);
            } else {
                $this->warn("Arquivo de lista não encontrado: storage/app/{$listaFilename}");
            }
        }
        
        return $fromCli->merge($fromFile)
            ->map(fn($name) => mb_strtoupper($name, 'UTF-8'))->flip();
    }
}