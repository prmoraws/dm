<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\PoliticaDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaDashboardV2Test extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resumo_prioriza_acompanhamentos_e_nao_exige_fonte_externa(): void
    {
        Cidade::query()->create(['nome' => 'Salvador', 'ibge_code' => 2927408]);

        $politico = Politico::query()->create([
            'nome_completo' => 'Rogéria de Almeida Pereira dos Santos',
            'nome_publico' => 'Rogéria Santos',
            'slug' => 'rogeria-santos',
        ]);

        Acompanhamento::query()->create([
            'politico_id' => $politico->id,
            'grupo' => 'Deputados Federais BA',
            'prioridade' => 1,
            'ordem' => 10,
            'ativo' => true,
        ]);

        $eleicao = Eleicao::query()->create([
            'ano' => 2022,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2022',
        ]);
        $cargo = Cargo::query()->create(['tse_codigo' => '0006', 'nome' => 'Deputado Federal']);

        Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'votos_total' => 82012,
            'origem' => 'legacy_v1',
        ]);

        $service = app(PoliticaDashboardService::class);
        $service->esquecerCache();
        $resumo = $service->resumo();

        $this->assertSame(1, $resumo['metricas']['cidades']);
        $this->assertSame(1, $resumo['metricas']['acompanhamentos']);
        $this->assertSame(1, $resumo['metricas']['com_historico']);
        $this->assertSame(0, $resumo['fontes']['total']);
        $this->assertArrayHasKey('Deputados Federais BA', $resumo['grupos']);
    }
}
