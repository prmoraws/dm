<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\EspelhoInteligenteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaEspelhoInteligenteServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resumo_da_cidade_ordena_resultados_e_calcula_diferenca_sem_consulta_externa(): void
    {
        $cidade = Cidade::query()->create([
            'nome' => 'Salvador',
            'ibge_code' => 2927408,
            'populacao' => 2_400_000,
        ]);

        $eleicao = Eleicao::query()->create([
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2026',
            'status' => 'planejada',
        ]);

        $cargo = Cargo::query()->create([
            'tse_codigo' => '0001',
            'nome' => 'Presidente',
            'esfera' => 'federal',
            'abrangencia' => 'nacional',
        ]);

        $a = Politico::query()->create(['nome_completo' => 'Candidato A', 'nome_publico' => 'Candidato A', 'slug' => 'candidato-a']);
        $b = Politico::query()->create(['nome_completo' => 'Candidato B', 'nome_publico' => 'Candidato B', 'slug' => 'candidato-b']);

        $ca = Candidatura::query()->create(['politico_id' => $a->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $cargo->id, 'votos_total' => 1200]);
        $cb = Candidatura::query()->create(['politico_id' => $b->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $cargo->id, 'votos_total' => 800]);

        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $ca->id, 'cidade_id' => $cidade->id, 'votos' => 1200, 'percentual' => 60]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $cb->id, 'cidade_id' => $cidade->id, 'votos' => 800, 'percentual' => 40]);

        $resumo = app(EspelhoInteligenteService::class)->resumoCidade($cidade, $eleicao->id);

        $this->assertSame('SALVADOR', $resumo['cidade']);
        $this->assertSame(2000, $resumo['total_votos_candidatos']);
        $this->assertSame('Candidato A', $resumo['lider']['politico']);
        $this->assertSame(400, $resumo['diferenca_votos']);
        $this->assertCount(2, $resumo['ranking']);
    }
}
