<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\InteligenciaTerritorial;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\InteligenciaTerritorialService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaInteligenciaTerritorialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function recuperacao_exige_tres_pleitos_e_recomposicao_de_pelo_menos_metade_da_perda(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $anterior = $this->candidatura($politico, $cargo, $partido, 2014, 100);
        $cidade = $this->cidade('Cidade Recuperacao', 200001);

        $this->resultado($anterior, $cidade, 100);
        $this->resultado($base, $cidade, 40);
        $this->resultado($comparada, $cidade, 80);
        $base->update(['votos_total' => 40]);
        $comparada->update(['votos_total' => 80]);

        EspelhoOperacional::query()->create([
            'cidade_id' => $cidade->id,
            'presidente_local' => 'Responsável local',
            'indicacao_bispo' => 'Acompanhamento prioritário',
            'filiados_republicanos' => 25,
            'revisado_em' => now(),
        ]);

        $painel = app(InteligenciaTerritorialService::class)->painel(
            $politico->slug,
            $cargo->nome,
            '2018',
            '2022',
        );

        $item = collect($painel['itens'])->firstWhere('nome', 'CIDADE RECUPERACAO');

        $this->assertTrue($painel['disponivel']);
        $this->assertContains('recuperacao', $item['sinais']);
        $this->assertSame('recuperacao', $item['sinal_principal']);
        $this->assertSame(2014, $item['recuperacao']['ano_anterior']);
        $this->assertSame(100, $item['recuperacao']['votos_anterior']);
        $this->assertSame(40, $item['recuperacao']['votos_recuperados']);
        $this->assertEqualsWithDelta(66.7, $item['recuperacao']['taxa_percentual'], 0.01);
        $this->assertTrue($item['operacional']['revisado']);
        $this->assertSame(25, $item['operacional']['filiados_republicanos']);
        $this->assertSame(1, $painel['resumo']['espelho_revisado']);
    }

    #[Test]
    public function perda_de_base_relevante_recebe_sinais_de_perda_e_oportunidade_sem_inventar_zero(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $a = $this->cidade('Cidade Queda', 200011);
        $b = $this->cidade('Cidade B', 200012);
        $c = $this->cidade('Cidade C', 200013);
        $d = $this->cidade('Cidade D', 200014);
        $e = $this->cidade('Cidade Sem Atual', 200015);

        foreach ([[$a, 200], [$b, 100], [$c, 50], [$d, 10], [$e, 30]] as [$cidade, $votos]) {
            $this->resultado($base, $cidade, $votos);
        }
        foreach ([[$a, 80], [$b, 120], [$c, 55], [$d, 12]] as [$cidade, $votos]) {
            $this->resultado($comparada, $cidade, $votos);
        }
        $base->update(['votos_total' => 390]);
        $comparada->update(['votos_total' => 267]);

        $painel = app(InteligenciaTerritorialService::class)->painel(
            $politico->slug,
            $cargo->nome,
            '2018',
            '2022',
        );
        $queda = collect($painel['itens'])->firstWhere('nome', 'CIDADE QUEDA');

        $this->assertContains('perda_relevante', $queda['sinais']);
        $this->assertContains('oportunidade_recuperacao', $queda['sinais']);
        $this->assertSame('perda_relevante', $queda['sinal_principal']);
        $this->assertSame(-120, $queda['delta']);
        $this->assertSame(4, $painel['resumo']['total']);
        $this->assertSame(1, $painel['metricasComparativo']['somente_base']);
        $this->assertNotContains('CIDADE SEM ATUAL', collect($painel['itens'])->pluck('nome')->all());
    }

    #[Test]
    public function fortalecimento_e_concentracao_sao_calculados_por_percentis_do_proprio_recorte(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $a = $this->cidade('Cidade Expansao', 200021);
        $b = $this->cidade('Cidade B', 200022);
        $c = $this->cidade('Cidade C', 200023);
        $d = $this->cidade('Cidade D', 200024);

        foreach ([[$a, 10], [$b, 50], [$c, 50], [$d, 50]] as [$cidade, $votos]) {
            $this->resultado($base, $cidade, $votos);
        }
        foreach ([[$a, 100], [$b, 55], [$c, 45], [$d, 50]] as [$cidade, $votos]) {
            $this->resultado($comparada, $cidade, $votos);
        }
        $base->update(['votos_total' => 160]);
        $comparada->update(['votos_total' => 250]);

        $painel = app(InteligenciaTerritorialService::class)->painel(
            $politico->slug,
            $cargo->nome,
            '2018',
            '2022',
        );
        $expansao = collect($painel['itens'])->firstWhere('nome', 'CIDADE EXPANSAO');

        $this->assertContains('fortalecimento', $expansao['sinais']);
        $this->assertContains('concentracao', $expansao['sinais']);
        $this->assertSame('concentracao', $expansao['sinal_principal']);
        $this->assertGreaterThan(0, $painel['limiares']['impacto_p75_votos']);
        $this->assertGreaterThan(0, $painel['limiares']['concentracao_p90_participacao']);
    }

    #[Test]
    public function componente_exibe_metodo_transparente_e_contexto_do_espelho(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $cidade = $this->cidade('Cidade Painel', 200031);
        $this->resultado($base, $cidade, 100);
        $this->resultado($comparada, $cidade, 130);
        $base->update(['votos_total' => 100]);
        $comparada->update(['votos_total' => 130]);

        Livewire::test(InteligenciaTerritorial::class)
            ->set('politico', $politico->slug)
            ->set('cargo', $cargo->nome)
            ->set('anoBase', '2018')
            ->set('anoComparada', '2022')
            ->assertSee('Eleição base')
            ->assertSee('Método transparente')
            ->assertSee('CIDADE PAINEL')
            ->assertSee('Espelho operacional')
            ->assertDontSee('@endif', false);
    }

    private function cenario(): array
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );

        $base = $this->candidatura($politico, $cargo, $partido, 2018, 150);
        $comparada = $this->candidatura($politico, $cargo, $partido, 2022, 150);

        return [$politico, $cargo, $partido, $base, $comparada];
    }

    private function candidatura(Politico $politico, Cargo $cargo, Partido $partido, int $ano, int $votosTotal): Candidatura
    {
        $eleicao = Eleicao::query()->create([
            'ano' => $ano,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => (string) $ano,
            'status' => 'concluida',
        ]);

        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10123',
            'nome_urna' => 'JURAILTON SANTOS',
            'uf' => 'BA',
            'votos_total' => $votosTotal,
            'situacao_eleicao' => 'ELEITO',
        ]);
    }

    private function cidade(string $nome, int $ibge): Cidade
    {
        return Cidade::query()->create([
            'nome' => $nome,
            'ibge_code' => $ibge,
            'latitude' => -12.0 - (($ibge % 100) / 100),
            'longitude' => -38.0 - (($ibge % 100) / 100),
        ]);
    }

    private function resultado(Candidatura $candidatura, Cidade $cidade, int $votos): void
    {
        ResultadoMunicipal::query()->create([
            'eleicao_id' => $candidatura->eleicao_id,
            'candidatura_id' => $candidatura->id,
            'cidade_id' => $cidade->id,
            'votos' => $votos,
        ]);
    }
}
