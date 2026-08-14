<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\EspelhoInteligenteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaEspelhoProfissionalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function panorama_separa_operacional_de_eleitoral_e_filtra_por_cargo(): void
    {
        $cidade = Cidade::query()->create(['nome' => 'Salvador', 'ibge_code' => 2927408, 'populacao' => 2400000]);
        $eleicao = Eleicao::query()->create([
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2026',
            'status' => 'planejada',
        ]);
        $presidente = Cargo::query()->create(['tse_codigo' => '0001', 'nome' => 'Presidente']);
        $governador = Cargo::query()->create(['tse_codigo' => '0003', 'nome' => 'Governador']);

        $p1 = Politico::query()->create(['nome_completo' => 'Presidente A', 'nome_publico' => 'Presidente A', 'slug' => 'presidente-a']);
        $p2 = Politico::query()->create(['nome_completo' => 'Presidente B', 'nome_publico' => 'Presidente B', 'slug' => 'presidente-b']);
        $g1 = Politico::query()->create(['nome_completo' => 'Governador A', 'nome_publico' => 'Governador A', 'slug' => 'governador-a']);

        $cp1 = Candidatura::query()->create(['politico_id' => $p1->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $presidente->id, 'votos_total' => 100, 'origem' => 'tse']);
        $cp2 = Candidatura::query()->create(['politico_id' => $p2->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $presidente->id, 'votos_total' => 80, 'origem' => 'tse']);
        $cg1 = Candidatura::query()->create(['politico_id' => $g1->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $governador->id, 'votos_total' => 1000, 'origem' => 'tse']);

        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $cp1->id, 'cidade_id' => $cidade->id, 'votos' => 100, 'percentual' => 55.5]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $cp2->id, 'cidade_id' => $cidade->id, 'votos' => 80, 'percentual' => 44.5]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $cg1->id, 'cidade_id' => $cidade->id, 'votos' => 1000, 'percentual' => 60]);

        EspelhoOperacional::query()->create([
            'cidade_id' => $cidade->id,
            'presidente_local' => 'Responsável Cidade',
            'observacoes' => 'Contato revisado',
        ]);

        $panorama = app(EspelhoInteligenteService::class)->panoramaCidade($cidade, $eleicao->id, $presidente->id);

        $this->assertSame('Responsável Cidade', $panorama['operacional']['presidente_local']);
        $this->assertSame(180, $panorama['eleitoral']['total_votos_candidatos']);
        $this->assertSame('Presidente A', $panorama['eleitoral']['lider']['politico']);
        $this->assertCount(2, $panorama['eleitoral']['ranking']);
        $this->assertTrue($panorama['eleitoral']['qualidade']['percentuais_oficiais_completos']);
        $this->assertEmpty($panorama['alertas']);
    }
}
