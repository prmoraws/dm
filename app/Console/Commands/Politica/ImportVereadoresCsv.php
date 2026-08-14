<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\{Bairro, Candidato, Cidade, LocalVotacao};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVereadoresCsv extends Command
{
    protected $signature = 'politica:import-vereadores-csv
                            {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}
                            {--ano=2024 : Ano da eleição}
                            {--chunk=1000 : Quantidade de registros gravados por lote}';

    protected $description = 'Importa votos detalhados de vereadores em lotes, sem carregar o CSV inteiro na memória.';

    public function handle(): int
    {
        $filepath = base_path($this->argument('filepath'));
        $ano = (int) $this->option('ano');
        $chunkSize = max(100, min((int) $this->option('chunk'), 10_000));

        if (! is_file($filepath)) {
            $this->error("Arquivo não encontrado em: {$filepath}");
            return self::FAILURE;
        }

        $cidadesCache = Cidade::query()->pluck('id', 'nome')->toArray();
        $bairrosCache = [];
        $locaisVotacaoCache = [];
        $candidatosCache = Candidato::query()->pluck('id', 'nome')->toArray();
        $votacaoData = [];
        $importados = 0;

        $fileHandle = fopen($filepath, 'rb');
        if ($fileHandle === false) {
            $this->error('Não foi possível abrir o CSV.');
            return self::FAILURE;
        }

        $this->info("Importando vereadores / {$ano} em lotes de {$chunkSize} registros.");
        fgetcsv($fileHandle);
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        try {
            while (($row = fgetcsv($fileHandle)) !== false) {
                $progressBar->advance();

                $nomeCandidato = mb_strtoupper($row[0] ?? '', 'UTF-8');
                $partido = mb_strtoupper($row[1] ?? '', 'UTF-8');
                $cargo = mb_strtoupper($row[2] ?? '', 'UTF-8');
                $nomeMunicipio = mb_strtoupper($row[3] ?? '', 'UTF-8');
                $zona = trim((string) ($row[4] ?? ''));
                $secao = trim((string) ($row[5] ?? ''));
                $votos = (int) ($row[6] ?? 0);

                if ($cargo !== 'VEREADOR' || $votos === 0 || $nomeCandidato === '' || $nomeMunicipio === '') {
                    continue;
                }

                if (! isset($cidadesCache[$nomeMunicipio])) {
                    $cidade = Cidade::query()->create(['nome' => $nomeMunicipio]);
                    $cidadesCache[$nomeMunicipio] = $cidade->id;
                }
                $cidadeId = $cidadesCache[$nomeMunicipio];

                $bairroKey = $cidadeId.'_BAIRRO NÃO INFORMADO';
                if (! isset($bairrosCache[$bairroKey])) {
                    $bairro = Bairro::query()->firstOrCreate([
                        'cidade_id' => $cidadeId,
                        'nome' => 'BAIRRO NÃO INFORMADO',
                    ]);
                    $bairrosCache[$bairroKey] = $bairro->id;
                }
                $bairroId = $bairrosCache[$bairroKey];

                $localKey = "{$cidadeId}_{$zona}_{$secao}";
                if (! isset($locaisVotacaoCache[$localKey])) {
                    $local = LocalVotacao::query()->firstOrCreate(
                        ['cidade_id' => $cidadeId, 'endereco' => "Zona: {$zona} / Seção: {$secao}"],
                        ['bairro_id' => $bairroId, 'nome' => "Local Zona {$zona} / Seção {$secao}"]
                    );
                    $locaisVotacaoCache[$localKey] = $local->id;
                }

                if (! isset($candidatosCache[$nomeCandidato])) {
                    $candidato = Candidato::query()->create(['nome' => $nomeCandidato, 'partido' => $partido]);
                    $candidatosCache[$nomeCandidato] = $candidato->id;
                }

                $votacaoData[] = [
                    'local_votacao_id' => $locaisVotacaoCache[$localKey],
                    'candidato_id' => $candidatosCache[$nomeCandidato],
                    'ano_eleicao' => $ano,
                    'cargo' => 'VEREADOR',
                    'votos_recebidos' => $votos,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($votacaoData) >= $chunkSize) {
                    $importados += $this->insertChunk($votacaoData);
                    $votacaoData = [];
                }
            }

            if ($votacaoData !== []) {
                $importados += $this->insertChunk($votacaoData);
            }
        } finally {
            fclose($fileHandle);
            $progressBar->finish();
        }

        $this->newLine(2);
        $this->info("Importação concluída: {$importados} registros gravados.");

        return self::SUCCESS;
    }

    private function insertChunk(array $rows): int
    {
        DB::transaction(static fn () => DB::table('politica_votacao_detalhada')->insert($rows), 3);

        return count($rows);
    }
}
