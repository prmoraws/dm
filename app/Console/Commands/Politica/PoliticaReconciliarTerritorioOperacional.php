<?php

namespace App\Console\Commands\Politica;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Services\Politica\V2\PoliticaQualidadeDadosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PoliticaReconciliarTerritorioOperacional extends Command
{
    protected $signature = 'politica:reconciliar-territorio-operacional
        {--aplicar : Efetiva somente correções sem conflito}';

    protected $description = 'Reconcilia vínculos operacionais presos em municípios auxiliares do legado com o município oficial correspondente.';

    /**
     * Pares confirmados pela auditoria da base real.
     *
     * O registro auxiliar não é apagado: apenas vínculos V2 seguros são
     * transferidos para o município oficial.
     */
    private const PARES = [
        [
            'auxiliar' => 'MUQUÉM DE SÃO FRANCISCO',
            'oficial' => 'MUQUÉM DO SÃO FRANCISCO',
        ],
    ];

    public function handle(PoliticaQualidadeDadosService $qualidade): int
    {
        $aplicar = (bool) $this->option('aplicar');

        $stats = [
            'pares_analisados' => 0,
            'operacionais_movidos' => 0,
            'inteligencias_movidas' => 0,
            'conflitos' => 0,
            'nao_localizados' => 0,
        ];
        $linhas = [];

        foreach (self::PARES as $par) {
            $stats['pares_analisados']++;

            $auxiliar = Cidade::query()
                ->whereNull('ibge_code')
                ->where('nome', $par['auxiliar'])
                ->first();

            $oficial = Cidade::query()
                ->whereNotNull('ibge_code')
                ->where('nome', $par['oficial'])
                ->first();

            if (! $auxiliar || ! $oficial) {
                $stats['nao_localizados']++;
                $linhas[] = [
                    'território',
                    $par['auxiliar'],
                    $par['oficial'],
                    'não localizado',
                ];
                continue;
            }

            $operacionalOrigem = EspelhoOperacional::query()
                ->where('cidade_id', $auxiliar->id)
                ->first();

            $operacionalDestino = EspelhoOperacional::query()
                ->where('cidade_id', $oficial->id)
                ->first();

            if ($operacionalOrigem && ! $operacionalDestino) {
                if ($aplicar) {
                    $operacionalOrigem->update(['cidade_id' => $oficial->id]);
                }

                $stats['operacionais_movidos']++;
                $linhas[] = [
                    'operacional',
                    "{$auxiliar->nome} (#{$auxiliar->id})",
                    "{$oficial->nome} (#{$oficial->id})",
                    $aplicar ? 'movido' : 'mover',
                ];
            } elseif ($operacionalOrigem && $operacionalDestino) {
                $stats['conflitos']++;
                $linhas[] = [
                    'operacional',
                    "{$auxiliar->nome} (#{$auxiliar->id})",
                    "{$oficial->nome} (#{$oficial->id})",
                    'conflito: ambos possuem espelho',
                ];
            } else {
                $linhas[] = [
                    'operacional',
                    "{$auxiliar->nome} (#{$auxiliar->id})",
                    "{$oficial->nome} (#{$oficial->id})",
                    'nenhum vínculo a mover',
                ];
            }

            $inteligencias = EspelhoInteligencia::query()
                ->where('cidade_id', $auxiliar->id)
                ->orderBy('id')
                ->get();

            foreach ($inteligencias as $inteligencia) {
                $duplicadaNoOficial = EspelhoInteligencia::query()
                    ->where('cidade_id', $oficial->id)
                    ->where('contexto_chave', $inteligencia->contexto_chave)
                    ->exists();

                if ($duplicadaNoOficial) {
                    $stats['conflitos']++;
                    $linhas[] = [
                        'inteligência',
                        "{$auxiliar->nome} · {$inteligencia->contexto_chave}",
                        $oficial->nome,
                        'conflito: contexto já existe no oficial',
                    ];
                    continue;
                }

                if ($aplicar) {
                    $inteligencia->update(['cidade_id' => $oficial->id]);
                }

                $stats['inteligencias_movidas']++;
                $linhas[] = [
                    'inteligência',
                    "{$auxiliar->nome} · {$inteligencia->contexto_chave}",
                    $oficial->nome,
                    $aplicar ? 'movida' : 'mover',
                ];
            }
        }

        $this->newLine();
        $this->info('Política V2 — Reconciliação territorial operacional');
        $this->line($aplicar
            ? 'Modo: APLICAR (alterações seguras efetivadas)'
            : 'Modo: DRY-RUN (nenhuma alteração gravada)');

        $this->table(
            ['Tipo', 'Origem', 'Destino', 'Ação'],
            $linhas
        );

        $this->table(
            ['Indicador', 'Valor'],
            collect($stats)->map(fn ($valor, $chave) => [$chave, $valor])->values()->all()
        );

        if ($aplicar) {
            $qualidade->limparCache();
        }

        if ($stats['conflitos'] > 0 || $stats['nao_localizados'] > 0) {
            $this->warn('Há itens que exigem conferência manual; nada conflitante foi sobrescrito.');

            return self::FAILURE;
        }

        if (! $aplicar && ($stats['operacionais_movidos'] > 0 || $stats['inteligencias_movidas'] > 0)) {
            $this->comment('Dry-run aprovado. Execute novamente com --aplicar para efetivar.');
        }

        return self::SUCCESS;
    }
}
