<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\PoliticaStorageService;
use App\Services\Politica\V2\TseOfficialSyncService;
use Illuminate\Console\Command;
use Throwable;

class PoliticaTseSincronizar extends Command
{
    protected $signature = 'politica:tse-sincronizar
        {ano : Ano da eleição, ex.: 2022, 2024 ou 2026}
        {--uf=BA : UF territorial principal}
        {--escopo=espelho : espelho, todos, prioritarios ou historico-especial}
        {--somente= : candidaturas ou resultados; vazio usa automático}
        {--arquivo-candidaturas= : ZIP/CSV local em vez de download}
        {--arquivo-resultados= : ZIP/CSV local em vez de download}
        {--forcar-download : Ignora validação condicional e baixa novamente}
        {--manter-arquivos : Mantém ZIPs baixados automaticamente após importação bem-sucedida}
        {--ignorar-limite : Permite ultrapassar conscientemente o limite de armazenamento configurado}
        {--dry-run : Lê e filtra os dados, mas não grava no banco}';

    protected $description = 'Sincroniza o recorte econômico de candidaturas e resultados oficiais do TSE com a Política V2.';

    public function handle(TseOfficialSyncService $service, PoliticaStorageService $storage): int
    {
        $ano = (int) $this->argument('ano');
        $uf = strtoupper((string) $this->option('uf'));
        $escopo = strtolower((string) $this->option('escopo'));
        $somente = strtolower(trim((string) $this->option('somente')));
        $dryRun = (bool) $this->option('dry-run');
        $ignorarLimite = (bool) $this->option('ignorar-limite');
        $manterArquivos = (bool) $this->option('manter-arquivos');

        if ($ano < 1990 || $ano > ((int) date('Y') + 1)) {
            $this->error('Ano inválido.');
            return self::FAILURE;
        }

        if (! in_array($escopo, ['espelho', 'todos', 'prioritarios', 'historico-especial', 'historico_especial'], true)) {
            $this->error('Escopo inválido. Use espelho, todos, prioritarios ou historico-especial.');
            return self::FAILURE;
        }

        if ($somente !== '' && ! in_array($somente, ['candidaturas', 'resultados'], true)) {
            $this->error('A opção --somente aceita candidaturas ou resultados.');
            return self::FAILURE;
        }

        $etapas = $somente !== ''
            ? [$somente]
            : ($ano >= (int) date('Y') ? ['candidaturas'] : ['candidaturas', 'resultados']);

        $this->newLine();
        $this->info('TSE Dados Abertos → Política V2 (escopo econômico)');
        $this->table(
            ['Parâmetro', 'Valor'],
            [
                ['ano', $ano],
                ['uf', $uf],
                ['escopo', $escopo],
                ['etapas', implode(', ', $etapas)],
                ['modo', $dryRun ? 'DRY-RUN' : 'GRAVAÇÃO'],
                ['arquivos após sucesso', $manterArquivos ? 'manter' : (config('politica.tse.cleanup_after_success', true) ? 'apagar automaticamente' : 'manter')],
            ]
        );

        try {
            $assessment = $storage->assertCurrentBelowLimit($ignorarLimite);
            $this->renderStorage('ANTES', $assessment, $storage);

            if (($assessment['warning'] ?? false) && ! ($assessment['blocked'] ?? false)) {
                $this->warn('O módulo Política já atingiu a faixa de aviso de armazenamento.');
            }

            foreach ($etapas as $etapa) {
                $option = $etapa === 'candidaturas' ? 'arquivo-candidaturas' : 'arquivo-resultados';
                $arquivoInfo = $service->arquivo(
                    $etapa,
                    $ano,
                    $this->option($option) ? (string) $this->option($option) : null,
                    (bool) $this->option('forcar-download')
                );

                $this->line(sprintf(
                    '%s: %s%s',
                    ucfirst($etapa),
                    $arquivoInfo['path'],
                    $arquivoInfo['changed'] === false ? ' (sem alteração no download)' : ''
                ));

                if ($dryRun) {
                    $stats = $etapa === 'candidaturas'
                        ? $service->diagnosticarCandidaturas($ano, $uf, $escopo, $arquivoInfo['path'])
                        : $service->diagnosticarResultados($ano, $uf, $escopo, $arquivoInfo['path']);
                } else {
                    $stats = $etapa === 'candidaturas'
                        ? $service->sincronizarCandidaturas($ano, $uf, $escopo, $arquivoInfo['path'], $ignorarLimite)
                        : $service->sincronizarResultados($ano, $uf, $escopo, $arquivoInfo['path'], $ignorarLimite);
                }

                $this->renderStats($etapa, $stats);

                if (! $dryRun) {
                    $after = $storage->snapshot();
                    $this->renderStorage('DEPOIS DE '.strtoupper($etapa), $after, $storage);

                    if (! $manterArquivos
                        && (bool) config('politica.tse.cleanup_after_success', true)
                        && ! ($arquivoInfo['manual'] ?? false)) {
                        $removed = $service->limparArquivoBaixado($arquivoInfo);
                        $this->line($removed
                            ? 'Arquivo bruto do TSE removido após processamento bem-sucedido.'
                            : 'Arquivo bruto não precisou/não pôde ser removido automaticamente.');
                    }
                }
            }

            if ($dryRun) {
                $this->warn('Dry-run concluído. Nenhuma linha eleitoral foi gravada. O arquivo é mantido para a execução real.');
            } else {
                $this->info('Sincronização oficial concluída.');
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }

    private function renderStats(string $etapa, array $stats): void
    {
        $rows = [];
        foreach ($stats as $key => $value) {
            if (str_ends_with((string) $key, '_bytes')) {
                continue;
            }
            if (is_array($value)) {
                $value = collect($value)
                    ->map(fn ($v, $k) => is_int($k) ? (string) $v : "{$k}: {$v}")
                    ->implode(' | ');
            }
            $rows[] = [$key, (string) $value];
        }

        $this->newLine();
        $this->comment(strtoupper($etapa));
        $this->table(['Indicador', 'Valor'], $rows);
    }

    private function renderStorage(string $label, array $snapshot, PoliticaStorageService $storage): void
    {
        $this->newLine();
        $this->comment('ARMAZENAMENTO '.$label);
        $this->table(['Indicador', 'Valor'], [
            ['Política', $storage->human($snapshot['politica_bytes'] ?? null)],
            ['Banco completo', $storage->human($snapshot['database_bytes'] ?? null)],
            ['Aviso', $storage->human($snapshot['warning_bytes'] ?? $storage->warningBytes())],
            ['Bloqueio', $storage->human($snapshot['hard_limit_bytes'] ?? $storage->hardLimitBytes())],
        ]);
    }
}
