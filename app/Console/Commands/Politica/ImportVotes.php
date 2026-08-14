<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\{Bairro, Candidato, Cidade, LocalVotacao, VotacaoDetalhada};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVotes extends Command
{
    protected $signature = 'politica:import-votes
                            {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}
                            {--ano=2022 : Ano da eleição}
                            {--cargo= : O cargo a ser importado (ex: "DEPUTADO ESTADUAL")}
                            {--candidato=* : Nomes exatos dos candidatos para filtrar a importação}
                            {--chunk=1000 : Quantidade de registros gravados por lote}';

    protected $description = 'Importa votos detalhados de CSV oficial do TSE em lotes, com baixo uso de memória.';

    public function handle(): int
    {
        $filepath = $this->argument('filepath');
        $ano = (int) $this->option('ano');
        $cargoFilter = mb_strtoupper((string) $this->option('cargo'), 'UTF-8');
        $candidatosFilter = $this->option('candidato');
        $chunkSize = max(100, min((int) $this->option('chunk'), 10_000));

        if ($ano < 1900 || $ano > 2200) {
            $this->error('O parâmetro --ano é inválido.');
            return self::FAILURE;
        }

        if ($cargoFilter === '') {
            $this->error('O filtro --cargo é obrigatório. Ex: --cargo="DEPUTADO ESTADUAL"');
            return self::FAILURE;
        }

        $csvPath = base_path($filepath);
        if (! is_file($csvPath)) {
            $this->error("Arquivo não encontrado em: {$csvPath}");
            return self::FAILURE;
        }

        $cidadesCache = Cidade::query()->pluck('id', 'nome')->toArray();
        $bairrosCache = [];
        $locaisVotacaoCache = [];
        $candidatosCache = Candidato::query()->pluck('id', 'nome')->toArray();
        $votacaoData = [];
        $importados = 0;

        $this->info("Importando {$cargoFilter} / {$ano} em lotes de {$chunkSize} registros.");
        if ($candidatosFilter !== []) {
            $this->info('Filtro de candidatos: '.implode(', ', $candidatosFilter));
        }

        $fileHandle = fopen($csvPath, 'rb');
        if ($fileHandle === false) {
            $this->error('Não foi possível abrir o CSV.');
            return self::FAILURE;
        }

        // Evita contar todas as linhas antecipadamente, pois CSVs do TSE podem ser muito grandes.
        $progressBar = $this->output->createProgressBar();
        fgetcsv($fileHandle, 0, ';');
        $progressBar->start();

        try {
            while (($row = fgetcsv($fileHandle, 0, ';')) !== false) {
                $progressBar->advance();
                $row = array_map(
                    static fn ($item) => mb_convert_encoding((string) $item, 'UTF-8', 'ISO-8859-1'),
                    $row
                );

                $cargo = mb_strtoupper($row[18] ?? '', 'UTF-8');
                $nomeVotavel = $row[20] ?? null;

                if ($cargo !== $cargoFilter) {
                    continue;
                }
                if ($candidatosFilter !== [] && ! in_array($nomeVotavel, $candidatosFilter, true)) {
                    continue;
                }

                $votos = (int) ($row[21] ?? 0);
                if ($votos === 0 || empty($nomeVotavel) || in_array($nomeVotavel, ['#NULO', '#NE'], true)) {
                    continue;
                }

                $nomeMunicipio = mb_strtoupper($row[14] ?? '', 'UTF-8');
                if ($nomeMunicipio === '') {
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

                $zona = trim((string) ($row[15] ?? ''));
                $secao = trim((string) ($row[16] ?? ''));
                $nomeLocalVotacao = trim((string) ($row[24] ?? 'LOCAL NÃO INFORMADO'));
                $localKey = implode('_', [$cidadeId, $zona, $secao, $nomeLocalVotacao]);

                if (! isset($locaisVotacaoCache[$localKey])) {
                    $local = LocalVotacao::query()->firstOrCreate(
                        [
                            'cidade_id' => $cidadeId,
                            'endereco' => "Zona: {$zona} / Seção: {$secao}",
                        ],
                        [
                            'bairro_id' => $bairroId,
                            'nome' => $nomeLocalVotacao,
                        ]
                    );
                    $locaisVotacaoCache[$localKey] = $local->id;
                }
                $localVotacaoId = $locaisVotacaoCache[$localKey];

                if (! isset($candidatosCache[$nomeVotavel])) {
                    $candidato = Candidato::query()->create(['nome' => $nomeVotavel]);
                    $candidatosCache[$nomeVotavel] = $candidato->id;
                }

                $votacaoData[] = [
                    'local_votacao_id' => $localVotacaoId,
                    'candidato_id' => $candidatosCache[$nomeVotavel],
                    'ano_eleicao' => $ano,
                    'cargo' => $cargo,
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
        DB::transaction(static fn () => VotacaoDetalhada::query()->insert($rows), 3);

        return count($rows);
    }
}
