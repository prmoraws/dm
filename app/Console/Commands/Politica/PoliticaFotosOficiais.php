<?php

namespace App\Console\Commands\Politica;

use App\Services\Politica\V2\PoliticaFotosOficiaisService;
use Illuminate\Console\Command;

class PoliticaFotosOficiais extends Command
{
    protected $signature = 'politica:fotos-oficiais
        {ano=2026 : Ano eleitoral}
        {--slug= : Atualiza somente um acompanhado, ex.: lula}
        {--forcar : Ignora ETag/Last-Modified e baixa novamente}';

    protected $description = 'Sincroniza localmente as fotos oficiais TSE dos oito acompanhamentos prioritários.';

    public function handle(PoliticaFotosOficiaisService $service): int
    {
        $slug = trim((string) $this->option('slug')) ?: null;
        $resultado = $service->sincronizar(
            (int) $this->argument('ano'),
            (bool) $this->option('forcar'),
            $slug,
        );

        $this->info("Fotos oficiais atualizadas: {$resultado['atualizadas']}.");
        $this->line("Fotos inalteradas: {$resultado['inalteradas']}.");
        $this->line("Fotos não encontradas: {$resultado['ausentes']}.");

        foreach ($resultado['fontes'] as $uf => $status) {
            $this->line("TSE {$uf}: HTTP {$status}.");
        }

        if ($resultado['itens'] !== []) {
            $this->newLine();
            $this->table(
                ['Candidato', 'Slug', 'SQ candidato', 'UF', 'Status', 'Bytes'],
                array_map(fn (array $item) => [
                    $item['nome'],
                    $item['slug'],
                    $item['sq'],
                    $item['abrangencia'],
                    $item['status'],
                    $item['bytes'] !== null ? number_format($item['bytes'], 0, ',', '.') : '—',
                ], $resultado['itens'])
            );
        } elseif ($slug) {
            $this->warn("Nenhuma candidatura oficial {$this->argument('ano')} foi localizada para o slug '{$slug}'.");
        }

        return self::SUCCESS;
    }
}
