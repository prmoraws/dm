<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\{Cidade, Bairro, LocalVotacao, Candidato, VotacaoDetalhada};
use Illuminate\Support\Facades\DB;

class ImportVereadoresCsv extends Command
{
    protected $signature = 'politica:import-vereadores-csv {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}';
    protected $description = 'Importa os votos detalhados dos vereadores de interesse de um arquivo CSV pré-filtrado.';

    public function handle()
    {
        $filepath = base_path($this->argument('filepath'));
        if (!file_exists($filepath)) {
            $this->error("Arquivo não encontrado em: {$filepath}");
            return 1;
        }

        // Cache em memória
        $cidadesCache = Cidade::pluck('id', 'nome')->toArray();
        $bairrosCache = [];
        $locaisVotacaoCache = [];
        $candidatosCache = Candidato::pluck('id', 'nome')->toArray();
        
        $votacaoData = [];
        $chunkSize = 1000;

        $this->info("Iniciando importação do arquivo de vereadores...");
        $fileHandle = fopen($filepath, 'r');
        fgetcsv($fileHandle); // Pula cabeçalho

        $totalLines = count(file($filepath)) - 1;
        $progressBar = $this->output->createProgressBar($totalLines);
        
        DB::beginTransaction();
        
        while (($row = fgetcsv($fileHandle)) !== false) {
            $progressBar->advance();

            // Mapeamento das colunas do seu CSV
            $nomeCandidato = mb_strtoupper($row[0] ?? '', 'UTF-8');
            $partido = mb_strtoupper($row[1] ?? '', 'UTF-8');
            $cargo = mb_strtoupper($row[2] ?? '', 'UTF-8');
            $nomeMunicipio = mb_strtoupper($row[3] ?? '', 'UTF-8');
            $zona = $row[4] ?? '';
            $secao = $row[5] ?? '';
            $votos = (int)($row[6] ?? 0);

            if ($cargo !== 'VEREADOR' || $votos === 0 || empty($nomeCandidato) || empty($nomeMunicipio)) {
                continue;
            }

            // Lógica de Cache e Inserção
            if (!isset($cidadesCache[$nomeMunicipio])) {
                $cidade = Cidade::create(['nome' => $nomeMunicipio]);
                $cidadesCache[$nomeMunicipio] = $cidade->id;
            }
            $cidadeId = $cidadesCache[$nomeMunicipio];

            $bairroKey = $cidadeId . '_BAIRRO NÃO INFORMADO';
            if (!isset($bairrosCache[$bairroKey])) {
                $bairro = Bairro::create(['cidade_id' => $cidadeId, 'nome' => 'BAIRRO NÃO INFORMADO']);
                $bairrosCache[$bairroKey] = $bairro->id;
            }
            $bairroId = $bairrosCache[$bairroKey];

            // Criamos um nome único para o local de votação para o cache
            $localKey = "{$cidadeId}_{$zona}_{$secao}";
            if (!isset($locaisVotacaoCache[$localKey])) {
                $local = LocalVotacao::firstOrCreate(
                    ['cidade_id' => $cidadeId, 'endereco' => "Zona: {$zona} / Seção: {$secao}"],
                    ['bairro_id' => $bairroId, 'nome' => "Local Zona {$zona} / Seção {$secao}"]
                );
                $locaisVotacaoCache[$localKey] = $local->id;
            }
            $localVotacaoId = $locaisVotacaoCache[$localKey];

            if (!isset($candidatosCache[$nomeCandidato])) {
                $candidato = Candidato::create(['nome' => $nomeCandidato, 'partido' => $partido]);
                $candidatosCache[$nomeCandidato] = $candidato->id;
            }
            $candidatoId = $candidatosCache[$nomeCandidato];

            $votacaoData[] = [
                'local_votacao_id' => $localVotacaoId,
                'candidato_id' => $candidatoId,
                'ano_eleicao' => 2024, // Assumindo que os dados são de 2024
                'cargo' => 'VEREADOR',
                'votos_recebidos' => $votos,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($votacaoData) >= $chunkSize) {
                DB::table('politica_votacao_detalhada')->insert($votacaoData);
                $votacaoData = [];
            }
        }

        if (!empty($votacaoData)) {
            DB::table('politica_votacao_detalhada')->insert($votacaoData);
        }

        DB::commit();
        fclose($fileHandle);
        $progressBar->finish();
        $this->info("\n\nImportação de vereadores concluída com sucesso!");
        return 0;
    }
}