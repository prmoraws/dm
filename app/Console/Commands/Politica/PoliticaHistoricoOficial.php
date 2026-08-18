<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\PoliticaHistoricoOficialService;
use App\Services\Politica\V2\TseOfficialSyncService;
use Illuminate\Console\Command;
use Throwable;

class PoliticaHistoricoOficial extends Command
{
    protected $signature = 'politica:historico-oficial
        {--anos= : Lista de anos separada por vírgula; vazio usa todos os ciclos configurados}
        {--uf=BA : UF principal}
        {--somente= : mandatos, filiacoes, candidaturas, resultados ou tse}
        {--forcar-download : Baixa novamente os arquivos TSE}
        {--manter-arquivos : Mantém ZIP/CSV do TSE após o processamento}
        {--ignorar-limite : Ignora conscientemente a trava de armazenamento}
        {--dry-run : Analisa sem gravar no banco}';

    protected $description = 'Sincroniza o histórico oficial completo somente de Márcio Marinho, Rogéria Santos, José de Arimateia e Jurailton Santos.';

    public function handle(
        PoliticaHistoricoOficialService $historico,
        TseOfficialSyncService $tse,
    ): int {
        $uf = strtoupper(trim((string) $this->option('uf')) ?: 'BA');
        $somente = strtolower(trim((string) $this->option('somente')));
        $dryRun = (bool) $this->option('dry-run');
        $manter = (bool) $this->option('manter-arquivos');
        $ignorarLimite = (bool) $this->option('ignorar-limite');

        if ($somente !== '' && ! in_array($somente, ['mandatos', 'filiacoes', 'candidaturas', 'resultados', 'tse'], true)) {
            $this->error('Use --somente=mandatos, filiacoes, candidaturas, resultados ou tse.');
            return self::FAILURE;
        }

        $anos = $this->resolverAnos($historico);
        if ($anos === []) {
            $this->error('Nenhum ano válido informado.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Histórico oficial especial — somente 4 políticos');
        $this->table(['Parâmetro', 'Valor'], [
            ['Políticos', implode(', ', $historico->slugs())],
            ['Anos', implode(', ', $anos)],
            ['UF', $uf],
            ['Escopo TSE', 'historico-especial'],
            ['Modo', $dryRun ? 'DRY-RUN' : 'GRAVAÇÃO'],
        ]);

        $erros = [];

        if ($somente === '' || $somente === 'mandatos') {
            if ($dryRun) {
                $total = 0;
                foreach ((array) config('politica.tse.historico_especial.mandatos', []) as $itens) {
                    $total += count((array) $itens);
                }
                $this->line("Mandatos/cargos oficiais configurados: {$total} (dry-run, sem gravação).");
            } else {
                $stats = $historico->sincronizarMandatosInstitucionais();
                $this->table(['Mandatos/cargos', 'Quantidade'], [
                    ['Políticos localizados', $stats['politicos']],
                    ['Registros configurados', $stats['registros_configurados']],
                    ['Inseridos', $stats['inseridos']],
                    ['Atualizados', $stats['atualizados']],
                    ['Removidos por correção histórica', $stats['removidos']],
                    ['Ignorados', $stats['ignorados']],
                ]);
            }
        }

        if ($somente === '' || $somente === 'filiacoes') {
            if ($dryRun) {
                $total = 0;
                foreach ((array) config('politica.tse.historico_especial.filiacoes', []) as $itens) {
                    $total += count((array) $itens);
                }
                $this->line("Filiações partidárias oficiais configuradas: {$total} (dry-run, sem gravação).");
            } else {
                $stats = $historico->sincronizarFiliacoesInstitucionais();
                $this->table(['Filiações partidárias', 'Quantidade'], [
                    ['Políticos localizados', $stats['politicos']],
                    ['Registros configurados', $stats['registros_configurados']],
                    ['Inseridos', $stats['inseridos']],
                    ['Atualizados', $stats['atualizados']],
                    ['Ignorados', $stats['ignorados']],
                ]);
            }
        }

        $processarCandidaturas = in_array($somente, ['', 'tse', 'candidaturas'], true);
        $processarResultados = in_array($somente, ['', 'tse', 'resultados'], true);

        if (! $processarCandidaturas && ! $processarResultados) {
            return self::SUCCESS;
        }

        foreach ($anos as $ano) {
            $this->newLine();
            $this->info("TSE {$ano}");
            $candInfo = null;
            $candStats = null;

            try {
                // Mesmo em --somente=resultados, lemos candidaturas para saber se algum dos
                // quatro participou daquele pleito. Isso evita baixar o arquivo de votação à toa.
                $candInfo = $tse->arquivo('candidaturas', $ano, null, (bool) $this->option('forcar-download'));
                $candStats = $dryRun || ! $processarCandidaturas
                    ? $tse->diagnosticarCandidaturas($ano, $uf, 'historico-especial', $candInfo['path'])
                    : $tse->sincronizarCandidaturas($ano, $uf, 'historico-especial', $candInfo['path'], $ignorarLimite);

                $this->line(sprintf(
                    'Candidaturas: %d selecionadas de %d linhas (%s).',
                    $candStats['linhas_selecionadas'],
                    $candStats['linhas_lidas'],
                    $processarCandidaturas ? ($dryRun ? 'dry-run' : 'sincronizadas') : 'somente diagnóstico'
                ));
                if (($candStats['identificadores_historicos'] ?? 0) > 0) {
                    $this->line(sprintf(
                        'Identificadores históricos compostos: %d (fallback seguro quando SQ_CANDIDATO não está disponível).',
                        $candStats['identificadores_historicos']
                    ));
                }
                if (($candStats['sem_identificador_oficial'] ?? 0) > 0) {
                    $this->warn(sprintf(
                        'Linhas selecionadas sem identificador suficiente: %d.',
                        $candStats['sem_identificador_oficial']
                    ));
                }

                if (! $manter) {
                    $tse->limparArquivoBaixado($candInfo);
                }
            } catch (Throwable $e) {
                $erros[] = "{$ano} candidaturas: {$e->getMessage()}";
                $this->warn(end($erros));
                if ($candInfo && ! $manter) {
                    $tse->limparArquivoBaixado($candInfo);
                }
                continue;
            }

            if (! $processarResultados || $ano >= (int) date('Y') || (int) ($candStats['linhas_selecionadas'] ?? 0) === 0) {
                $this->line($ano >= (int) date('Y') ? 'Resultados: não processados para eleição corrente.' : 'Resultados: dispensados neste ano.');
                continue;
            }

            if ($dryRun && $processarCandidaturas) {
                $this->line('Resultados: dry-run adiado até as candidaturas deste ano existirem no banco; nenhuma gravação foi feita.');
                continue;
            }

            $resultInfo = null;
            try {
                $resultInfo = $tse->arquivo('resultados', $ano, null, (bool) $this->option('forcar-download'));
                $resultStats = $dryRun
                    ? $tse->diagnosticarResultados($ano, $uf, 'historico-especial', $resultInfo['path'])
                    : $tse->sincronizarResultados($ano, $uf, 'historico-especial', $resultInfo['path'], $ignorarLimite);

                $this->line(sprintf(
                    'Resultados: %d linhas selecionadas; %d municípios; estimativa %s.',
                    $resultStats['linhas_selecionadas'],
                    $resultStats['municipios_com_resultado'] ?? $resultStats['resultados_municipais'] ?? 0,
                    $resultStats['estimativa_banco'] ?? '—'
                ));
                if (($resultStats['linhas_duplicadas_descartadas'] ?? 0) > 0) {
                    $this->line(sprintf(
                        'Linhas duplicadas do arquivo consolidado/UF descartadas: %d.',
                        $resultStats['linhas_duplicadas_descartadas']
                    ));
                }

                if (! $manter) {
                    $tse->limparArquivoBaixado($resultInfo);
                }
            } catch (Throwable $e) {
                $erros[] = "{$ano} resultados: {$e->getMessage()}";
                $this->warn(end($erros));
                if ($resultInfo && ! $manter) {
                    $tse->limparArquivoBaixado($resultInfo);
                }
            }
        }

        $this->newLine();
        if ($erros !== []) {
            $this->warn('Histórico concluído com pendências. Nada foi inventado para os anos que falharam:');
            foreach ($erros as $erro) {
                $this->line(' - '.$erro);
            }
            return self::FAILURE;
        }

        $this->info('Histórico oficial especial concluído sem pendências.');
        return self::SUCCESS;
    }

    /** @return array<int,int> */
    private function resolverAnos(PoliticaHistoricoOficialService $historico): array
    {
        $raw = trim((string) $this->option('anos'));
        if ($raw === '') {
            return $historico->anos();
        }

        $anos = array_values(array_unique(array_filter(
            array_map(fn (string $ano) => (int) trim($ano), explode(',', $raw)),
            fn (int $ano) => $ano >= 1990 && $ano <= ((int) date('Y') + 1)
        )));
        sort($anos);

        return $anos;
    }
}
