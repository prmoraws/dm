<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Services\Politica\V2\TseApuracaoCollectorService;
use App\Services\Politica\V2\TseApuracaoOrchestrator;
use Illuminate\Console\Command;
use RuntimeException;

class PoliticaApuracaoColetar extends Command
{
    protected $signature = 'politica:apuracao-coletar
        {--ano=2026}
        {--turno=1}
        {--uf=BA}
        {--ignorar-cooldown : Ignora somente o intervalo local; não ignora a trava LIVE_ENABLED}
        {--arquivo= : Analisa um EA20 JSON local sem acessar a internet}
        {--cargo= : Cargo correspondente ao --arquivo}
        {--abrangencia= : BR ou UF correspondente ao --arquivo}
        {--gravar-fixture : Persiste explicitamente o arquivo local nas tabelas de apuração}';

    protected $description = 'Coleta apuração TSE com trava de segurança, HTTP condicional e snapshots locais';

    public function handle(TseApuracaoOrchestrator $orchestrator, TseApuracaoCollectorService $collector): int
    {
        if ($arquivo = $this->option('arquivo')) {
            return $this->arquivoLocal($collector, (string) $arquivo);
        }

        try {
            $resultado = $orchestrator->coletar(
                (int) $this->option('ano'),
                (int) $this->option('turno'),
                strtoupper((string) $this->option('uf')),
                (bool) $this->option('ignorar-cooldown'),
            );
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->table(['Indicador', 'Valor'], [
            ['Executado', $resultado['executado'] ? 'sim' : 'não'],
            ['Motivo', $resultado['motivo'] ?: '—'],
            ['Requisições lógicas', $resultado['requests']],
            ['Atualizações EA20', count($resultado['atualizacoes'])],
        ]);

        foreach ($resultado['atualizacoes'] as $item) {
            $this->line(sprintf(
                '%s · %s · %s%% seções · %d candidato(s) vinculados · snapshot %s',
                $item['cargo'],
                $item['abrangencia'],
                $item['percentual_secoes'] ?? 0,
                $item['candidatos_vinculados'],
                $item['snapshot'],
            ));
        }

        return self::SUCCESS;
    }

    private function arquivoLocal(TseApuracaoCollectorService $collector, string $arquivo): int
    {
        if (! is_file($arquivo)) {
            $this->error("Arquivo não encontrado: {$arquivo}");
            return self::FAILURE;
        }

        $payload = json_decode((string) file_get_contents($arquivo), true);
        if (! is_array($payload)) {
            $this->error('O arquivo informado não contém JSON válido.');
            return self::FAILURE;
        }

        $analise = $collector->analisar($payload);
        $this->table(['Indicador', 'Valor'], [
            ['IDG', $analise['idg'] ?: '—'],
            ['Andamento', $analise['andamento'] ?: '—'],
            ['Seções', $analise['percentual_secoes'] !== null ? $analise['percentual_secoes'].'%' : '—'],
            ['Candidatos detectados', $analise['candidatos']],
        ]);

        if (! $this->option('gravar-fixture')) {
            $this->comment('Modo seguro: arquivo apenas analisado. Use --gravar-fixture explicitamente para persistir.');
            return self::SUCCESS;
        }

        $cargoNome = trim((string) $this->option('cargo'));
        $abrangencia = strtoupper(trim((string) $this->option('abrangencia')));
        if ($cargoNome === '' || ! in_array($abrangencia, ['BR', 'BA'], true)) {
            $this->error('Para --gravar-fixture informe --cargo="..." e --abrangencia=BR|BA.');
            return self::FAILURE;
        }

        $eleicao = Eleicao::query()
            ->where('ano', (int) $this->option('ano'))
            ->where('turno', (int) $this->option('turno'))
            ->orderByDesc('id')
            ->first();
        $cargo = Cargo::query()->where('nome', $cargoNome)->first();

        if (! $eleicao || ! $cargo) {
            $this->error('Eleição ou cargo não localizado no banco.');
            return self::FAILURE;
        }

        $resultado = $collector->persistir($eleicao, $cargo, $payload, $abrangencia);
        $this->info("Fixture persistida na apuração #{$resultado['apuracao_id']}.");

        return self::SUCCESS;
    }
}
