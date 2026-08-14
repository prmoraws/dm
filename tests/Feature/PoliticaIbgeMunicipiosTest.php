<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Services\Politica\DataIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PoliticaIbgeMunicipiosTest extends TestCase
{
    use RefreshDatabase;

    public function test_reconcilia_codigos_oficiais_sem_apagar_registros_legados(): void
    {
        config()->set('politica.data_sources.ibge.expected_municipalities', 4);
        config()->set('politica.data_sources.ibge.uf_code', 29);
        config()->set('politica.data_sources.ibge.url', 'https://servicodados.ibge.gov.br/api/v1/');

        $valente = Cidade::create(['nome' => 'VALENTE', 'ibge_code' => 2933075, 'latitude' => -1, 'longitude' => -1]);
        $xique = Cidade::create(['nome' => 'XIQUE-XIQUE', 'ibge_code' => 2933703]);
        $santa = Cidade::create(['nome' => 'SANTA TERESINHA', 'ibge_code' => 2928604]);
        Cidade::create(['nome' => 'SANTA TEREZINHA']);
        $muquem = Cidade::create(['nome' => 'MUQUÉM DE SÃO FRANCISCO']);
        $extra = Cidade::create(['nome' => 'GOVERNADOR RUI BARBOSA', 'ibge_code' => 2911659]);

        Http::fake([
            'https://servicodados.ibge.gov.br/api/v1/localidades/estados/29/municipios' => Http::response([
                ['id' => 2933000, 'nome' => 'Valente'],
                ['id' => 2933604, 'nome' => 'Xique-Xique'],
                ['id' => 2928505, 'nome' => 'Santa Terezinha'],
                ['id' => 2922250, 'nome' => 'Muquém de São Francisco'],
            ]),
        ]);

        $resumo = app(DataIntegrationService::class)->reconcileOfficialMunicipalities();

        $this->assertSame(4, $resumo['oficiais']);
        $this->assertSame(4, Cidade::query()->whereNotNull('ibge_code')->count());
        $this->assertSame(2933000, (int) $valente->fresh()->ibge_code);
        $this->assertSame(2933604, (int) $xique->fresh()->ibge_code);
        $this->assertSame(2928505, (int) $santa->fresh()->ibge_code);
        $this->assertSame('SANTA TEREZINHA', $santa->fresh()->nome);
        $this->assertSame(2922250, (int) $muquem->fresh()->ibge_code);
        $this->assertNull($extra->fresh()->ibge_code);
        $this->assertContains($valente->id, $resumo['changed_ids']);
        $this->assertContains('GOVERNADOR RUI BARBOSA', $resumo['extras']);
    }
}
