<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\ComparativoTerritorial;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\ComparativoTerritorialService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaComparativoTerritorialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function compara_apenas_municipios_presentes_nos_dois_pleitos_sem_inventar_zero(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();

        $a = Cidade::query()->create(['nome' => 'Cidade Alfa', 'ibge_code' => 100001, 'latitude' => -12.1, 'longitude' => -38.1]);
        $b = Cidade::query()->create(['nome' => 'Cidade Beta', 'ibge_code' => 100002, 'latitude' => -12.2, 'longitude' => -38.2]);
        $c = Cidade::query()->create(['nome' => 'Cidade Gama', 'ibge_code' => 100003, 'latitude' => -12.3, 'longitude' => -38.3]);

        $this->resultado($base, $a, 100);
        $this->resultado($base, $b, 50);
        $this->resultado($comparada, $a, 130);
        $this->resultado($comparada, $c, 20);

        $painel = app(ComparativoTerritorialService::class)->painel(
            $politico->slug,
            $cargo->nome,
            '2018',
            '2022',
        );

        $this->assertTrue($painel['disponivel']);
        $this->assertSame(1, $painel['metricas']['comparaveis']);
        $this->assertSame(1, $painel['metricas']['somente_base']);
        $this->assertSame(1, $painel['metricas']['somente_comparada']);

        $alfa = collect($painel['mapaData'])->firstWhere('nome', 'CIDADE ALFA');
        $beta = collect($painel['mapaData'])->firstWhere('nome', 'CIDADE BETA');
        $gama = collect($painel['mapaData'])->firstWhere('nome', 'CIDADE GAMA');

        $this->assertSame(30, $alfa['delta']);
        $this->assertSame('crescimento', $alfa['status']);
        $this->assertNull($beta['delta']);
        $this->assertSame('somente_base', $beta['cobertura']);
        $this->assertNull($gama['delta']);
        $this->assertSame('somente_comparada', $gama['cobertura']);
    }

    #[Test]
    public function selecao_padrao_exige_duas_eleicoes_do_mesmo_cargo(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $cidade = Cidade::query()->create(['nome' => 'Cidade Apoio', 'ibge_code' => 100021, 'latitude' => -12.4, 'longitude' => -38.4]);
        $this->resultado($base, $cidade, 10);
        $this->resultado($comparada, $cidade, 20);

        $outroCargo = Cargo::query()->where('nome', 'Prefeito')->firstOrFail();
        $eleicao = Eleicao::query()->create(['ano' => 2020, 'turno' => 1, 'tipo' => 'municipal', 'descricao' => '2020', 'status' => 'concluida']);
        Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $outroCargo->id,
            'numero_urna' => '10',
            'nome_urna' => 'TESTE',
            'uf' => 'BA',
            'votos_total' => 100,
        ]);

        $padrao = app(ComparativoTerritorialService::class)->normalizarSelecao(
            $politico->slug,
            $cargo->nome,
            '',
            '',
        );

        $this->assertSame($politico->slug, $padrao['politico']);
        $this->assertSame($cargo->nome, $padrao['cargo']);
        $this->assertSame('2018', $padrao['ano_base']);
        $this->assertSame('2022', $padrao['ano_comparada']);
    }

    #[Test]
    public function componente_exibe_aviso_de_cobertura_sem_converter_ausencia_em_zero(): void
    {
        [$politico, $cargo, $partido, $base, $comparada] = $this->cenario();
        $a = Cidade::query()->create(['nome' => 'Cidade Alfa', 'ibge_code' => 100011, 'latitude' => -12.1, 'longitude' => -38.1]);
        $b = Cidade::query()->create(['nome' => 'Cidade Beta', 'ibge_code' => 100012, 'latitude' => -12.2, 'longitude' => -38.2]);
        $this->resultado($base, $a, 100);
        $this->resultado($base, $b, 50);
        $this->resultado($comparada, $a, 130);

        Livewire::test(ComparativoTerritorial::class)
            ->set('politico', $politico->slug)
            ->set('cargo', $cargo->nome)
            ->set('anoBase', '2018')
            ->set('anoComparada', '2022')
            ->assertSee('Eleição base')
            ->assertSee('não transforma ausência de registro em zero voto', false)
            ->assertSee('CIDADE ALFA')
            ->assertSee('CIDADE BETA');
    }

    private function cenario(): array
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );

        $e2018 = Eleicao::query()->create(['ano' => 2018, 'turno' => 1, 'tipo' => 'geral', 'descricao' => '2018', 'status' => 'concluida']);
        $e2022 = Eleicao::query()->create(['ano' => 2022, 'turno' => 1, 'tipo' => 'geral', 'descricao' => '2022', 'status' => 'concluida']);

        $base = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $e2018->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10123',
            'nome_urna' => 'JURAILTON SANTOS',
            'uf' => 'BA',
            'votos_total' => 150,
            'situacao_eleicao' => 'ELEITO',
        ]);
        $comparada = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $e2022->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10123',
            'nome_urna' => 'JURAILTON SANTOS',
            'uf' => 'BA',
            'votos_total' => 150,
            'situacao_eleicao' => 'ELEITO',
        ]);

        return [$politico, $cargo, $partido, $base, $comparada];
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
