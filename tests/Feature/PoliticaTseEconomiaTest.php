<?php

namespace Tests\Feature;

use App\Services\Politica\V2\PoliticaStorageService;
use App\Services\Politica\V2\TseOpenDataDownloader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaTseEconomiaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function configuracao_economica_limita_prefeito_ao_partido_prioritario(): void
    {
        $partyOnly = config('politica.tse.scope.somente_partido');
        $all = config('politica.tse.scope.todos');

        $this->assertContains('Prefeito', $partyOnly);
        $this->assertContains('Vereador', $partyOnly);
        $this->assertContains('Deputado Estadual', $partyOnly);
        $this->assertContains('Deputado Federal', $partyOnly);
        $this->assertContains('Senador', $partyOnly);
        $this->assertNotContains('Prefeito', $all);
        $this->assertSame(['Governador', 'Presidente'], array_values($all));
    }

    #[Test]
    public function estima_crescimento_de_resultados_com_margem_para_indices(): void
    {
        config()->set('politica.tse.storage.result_row_estimate_bytes', 900);
        config()->set('politica.tse.storage.candidate_row_estimate_bytes', 4096);

        $service = app(PoliticaStorageService::class);

        $this->assertSame(900000, $service->estimateResults(400, 600));
        $this->assertSame(40960, $service->estimateCandidates(10));
    }

    #[Test]
    public function remove_apenas_arquivo_bruto_dentro_do_cache_tse(): void
    {
        config()->set('politica.tse.disk', 'local');
        config()->set('politica.tse.path', 'framework/testing/politica-tse-cache');

        $relative = 'framework/testing/politica-tse-cache/2022/resultados_2022.zip';
        Storage::disk('local')->put($relative, 'conteudo-teste');
        $absolute = Storage::disk('local')->path($relative);

        $this->assertFileExists($absolute);
        $this->assertTrue(app(TseOpenDataDownloader::class)->cleanup($absolute));
        $this->assertFileDoesNotExist($absolute);
    }
}
