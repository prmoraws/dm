<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\PoliticaQualidadeDadosService;
use Illuminate\Console\Command;

class PoliticaAuditarDados extends Command
{
    protected $signature = 'politica:auditar-dados {--sem-cache : Recalcula todas as verificações antes de exibir} {--falhar-em-critico : Retorna código de erro quando houver achado crítico}';

    protected $description = 'Audita integridade eleitoral, territorial, operacional e integrações da Política V2 sem alterar dados.';

    public function handle(PoliticaQualidadeDadosService $service): int
    {
        $dados = $service->snapshot((bool) $this->option('sem-cache'));

        $this->info('Política V2 — Centro de Qualidade e Auditoria');
        $this->table(['Indicador', 'Valor'], [
            ['Status', strtoupper((string) $dados['status'])],
            ['Críticos', $dados['contagens']['critico']],
            ['Alertas', $dados['contagens']['alerta']],
            ['Informativos', $dados['contagens']['info']],
            ['Candidaturas', $dados['metricas']['candidaturas']],
            ['Resultados municipais', $dados['metricas']['resultados_municipais']],
            ['Resultados por zona', $dados['metricas']['resultados_zonas']],
            ['Municípios oficiais', $dados['metricas']['municipios_oficiais']],
            ['Espelhos operacionais', $dados['metricas']['espelhos_operacionais']],
        ]);

        $achados = collect($dados['achados'])->take(40)->map(fn (array $item) => [
            strtoupper($item['nivel']),
            $item['grupo'],
            $item['codigo'],
            $item['titulo'],
            $item['contexto'],
        ])->all();

        if ($achados !== []) {
            $this->newLine();
            $this->table(['Nível', 'Grupo', 'Código', 'Achado', 'Contexto'], $achados);
        }

        if ((bool) $this->option('falhar-em-critico') && (int) $dados['contagens']['critico'] > 0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
