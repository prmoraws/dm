<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\TseOpenDataDownloader;
use Illuminate\Console\Command;

class PoliticaTseLimparCache extends Command
{
    protected $signature = 'politica:tse-limpar-cache {--ano= : Limita a limpeza a um ano específico}';

    protected $description = 'Remove ZIPs/arquivos temporários baixados do TSE sem tocar nos dados já importados.';

    public function handle(TseOpenDataDownloader $downloader): int
    {
        $ano = $this->option('ano');
        $ano = $ano !== null && $ano !== '' ? (int) $ano : null;

        $resultado = $downloader->cleanupCache($ano);

        $this->table(['Indicador', 'Valor'], [
            ['Arquivos removidos', $resultado['files']],
            ['Espaço liberado', $this->human((int) $resultado['bytes'])],
            ['Escopo', $ano ? (string) $ano : 'todos os anos'],
        ]);

        return self::SUCCESS;
    }

    private function human(int $bytes): string
    {
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1, ',', '.').' KB';
        }
        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 2, ',', '.').' MB';
        }

        return number_format($bytes / 1024 / 1024 / 1024, 2, ',', '.').' GB';
    }
}
