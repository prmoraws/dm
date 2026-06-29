<?php

namespace App\Console\Commands\Politica;

use Illuminate\Console\Command;
use App\Models\Politica\{Cidade, Bairro, LocalVotacao, Candidato, VotacaoDetalhada};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportVotes extends Command
{
    /**
     * A assinatura do comando, que define seu nome e argumentos.
     */
    protected $signature = 'politica:import-votes 
                            {filepath : O caminho para o arquivo CSV a partir da raiz do projeto}
                            {--cargo= : O cargo a ser importado (ex: "DEPUTADO ESTADUAL")}
                            {--candidato=* : Nomes exatos dos candidatos para filtrar a importação}';

    /**
     * A descrição do comando.
     */
    protected $description = 'Importa os votos detalhados de um arquivo CSV oficial do TSE de forma otimizada e filtrada.';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        $filepath = $this->argument('filepath');
        $cargoFilter = mb_strtoupper($this->option('cargo'), 'UTF-8');
        $candidatosFilter = $this->option('candidato');

        if (empty($cargoFilter)) {
            $this->error('O filtro --cargo é obrigatório. Ex: --cargo="DEPUTADO ESTADUAL"');
            return 1;
        }

        $csvPath = base_path($filepath);

        if (!file_exists($csvPath)) {
            $this->error("Arquivo não encontrado em: {$csvPath}");
            return 1;
        }

        // Cache em memória
        $cidadesCache = Cidade::pluck('id', 'nome')->toArray();
        $bairrosCache = [];
        $locaisVotacaoCache = [];
        $candidatosCache = Candidato::pluck('id', 'nome')->toArray();
        
        $votacaoData = [];
        $chunkSize = 1000;

        $this->info("Iniciando importação para o cargo: {$cargoFilter}");
        if (!empty($candidatosFilter)) {
            $this->info('Filtrando pelos candidatos: ' . implode(', ', $candidatosFilter));
        }

        $fileHandle = fopen($csvPath, 'r');
        fgetcsv($fileHandle, 0, ';');

        $totalLines = count(file($csvPath)) - 1;
        $progressBar = $this->output->createProgressBar($totalLines);
        
        DB::beginTransaction();
        
        while (($row = fgetcsv($fileHandle, 0, ';')) !== false) {
            $progressBar->advance();
            $row = array_map(fn($item) => mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1'), $row);

            $cargo = mb_strtoupper($row[18] ?? '', 'UTF-8');
            $nomeVotavel = $row[20] ?? null;
            
            // LÓGICA DE FILTRO ATUALIZADA
            if ($cargo !== $cargoFilter) continue;
            // Se um filtro de candidato foi passado, e o nome não está na lista, pula a linha
            if (!empty($candidatosFilter) && !in_array($nomeVotavel, $candidatosFilter)) continue;
            
            $votos = (int)($row[21] ?? 0);
            if ($votos === 0 || empty($nomeVotavel) || $nomeVotavel === '#NULO' || $nomeVotavel === '#NE') continue;

            $nomeMunicipio = mb_strtoupper($row[14] ?? '', 'UTF-8');
            if(empty($nomeMunicipio)) continue;

            // O restante da lógica de cache e inserção
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

            $nomeLocalVotacao = $row[24] ?? 'LOCAL NÃO INFORMADO';
            $localKey = $cidadeId . '_' . $nomeLocalVotacao;
            if (!isset($locaisVotacaoCache[$localKey])) {
                $zona = $row[15] ?? ''; $secao = $row[16] ?? '';
                $local = LocalVotacao::create([
                    'bairro_id' => $bairroId,
                    'cidade_id' => $cidadeId,
                    'nome' => $nomeLocalVotacao,
                    'endereco' => "Zona: {$zona} / Seção: {$secao}"
                ]);
                $locaisVotacaoCache[$localKey] = $local->id;
            }
            $localVotacaoId = $locaisVotacaoCache[$localKey];

            if (!isset($candidatosCache[$nomeVotavel])) {
                $candidato = Candidato::create(['nome' => $nomeVotavel]);
                $candidatosCache[$nomeVotavel] = $candidato->id;
            }
            $candidatoId = $candidatosCache[$nomeVotavel];

            $votacaoData[] = [
                'local_Votacao_id' => $localVotacaoId,
                'candidato_id' => $candidatoId,
                'ano_eleicao' => 2022,
                'cargo' => $cargo,
                'votos_recebidos' => $votos,
            ];

            if (count($votacaoData) >= $chunkSize) {
                VotacaoDetalhada::insert($votacaoData);
                $votacaoData = [];
            }
        }

        if (!empty($votacaoData)) {
            VotacaoDetalhada::insert($votacaoData);
        }

        DB::commit();
        fclose($fileHandle);
        $progressBar->finish();
        $this->info("\n\nImportação otimizada concluída com sucesso!");
        return 0;
    }
}