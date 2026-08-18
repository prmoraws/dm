<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\PainelExecutivo;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\PainelExecutivoService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaPainelExecutivoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function escolhe_os_dois_pleitos_mais_recentes_do_mesmo_cargo_sem_cruzar_cargos(): void
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $deputado = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $prefeito = Cargo::query()->firstOrCreate(
            ['nome' => 'Prefeito'],
            ['esfera' => 'municipal', 'abrangencia' => 'municipio', 'ordem' => 10, 'ativo' => true]
        );
        $partido = $this->partido();
        $cidade = $this->cidade('Cidade Principal', 300001);

        foreach ([2014 => 40, 2018 => 60, 2022 => 90] as $ano => $votos) {
            $candidatura = $this->candidatura($politico, $deputado, $partido, $ano, $votos);
            $this->resultado($candidatura, $cidade, $votos);
        }

        $municipal = $this->candidatura($politico, $prefeito, $partido, 2020, 500);
        $this->resultado($municipal, $cidade, 500);

        $painel = app(PainelExecutivoService::class)->painel($politico->slug);
        $card = collect($painel['cards'])->first();

        $this->assertSame(1, $painel['resumo']['com_comparacao']);
        $this->assertSame('Deputado Estadual', $card['cargo']);
        $this->assertSame(2018, $card['ano_base']);
        $this->assertSame(2022, $card['ano_comparada']);
        $this->assertSame(30, $card['delta_total']);
    }

    #[Test]
    public function cobertura_incompleta_nao_converte_municipio_ausente_em_zero_no_resumo_executivo(): void
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = $this->partido();
        $base = $this->candidatura($politico, $cargo, $partido, 2018, 210);
        $comparada = $this->candidatura($politico, $cargo, $partido, 2022, 180);
        $a = $this->cidade('Cidade Comparavel', 300011);
        $b = $this->cidade('Cidade So Base', 300012);
        $c = $this->cidade('Cidade So Atual', 300013);

        $this->resultado($base, $a, 200);
        $this->resultado($base, $b, 10);
        $this->resultado($comparada, $a, 80);
        $this->resultado($comparada, $c, 100);

        $painel = app(PainelExecutivoService::class)->painel($politico->slug);
        $card = collect($painel['cards'])->first();

        $this->assertTrue($card['cobertura_incompleta']);
        $this->assertSame(1, $painel['resumo']['municipios_unicos']);
        $this->assertSame(1, $painel['resumo']['contextos_analisados']);
        $this->assertSame('CIDADE COMPARAVEL', $painel['atencao'][0]['nome']);
        $this->assertSame(-120, $painel['atencao'][0]['delta']);
        $this->assertNotContains('CIDADE SO BASE', collect($painel['atencao'])->pluck('nome')->all());
        $this->assertNotContains('CIDADE SO ATUAL', collect($painel['atencao'])->pluck('nome')->all());
    }

    #[Test]
    public function fila_do_espelho_e_unica_por_municipio_mesmo_com_sinais_de_dois_politicos(): void
    {
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = $this->partido();
        $cidade = $this->cidade('Cidade Compartilhada', 300021);

        foreach (['jurailton-santos', 'jose-de-arimateia'] as $slug) {
            $politico = Politico::query()->where('slug', $slug)->firstOrFail();
            $base = $this->candidatura($politico, $cargo, $partido, 2018, 100);
            $comparada = $this->candidatura($politico, $cargo, $partido, 2022, 50);
            $this->resultado($base, $cidade, 100);
            $this->resultado($comparada, $cidade, 50);
        }

        $painel = app(PainelExecutivoService::class)->painel('todos');

        $this->assertSame(2, $painel['resumo']['contextos_analisados']);
        $this->assertSame(1, $painel['resumo']['municipios_unicos']);
        $this->assertSame(1, $painel['resumo']['pendencias_espelho']);
        $this->assertSame('CIDADE COMPARTILHADA', $painel['pendenciasEspelho'][0]['nome']);
        $this->assertSame(2, $painel['pendenciasEspelho'][0]['contextos']);
        $this->assertSame('sem_registro', $painel['pendenciasEspelho'][0]['status']);
    }

    #[Test]
    public function componente_exibe_resumo_prioridades_e_fila_operacional_no_corpo_livewire(): void
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = $this->partido();
        $cidade = $this->cidade('Cidade Executiva', 300031);
        $base = $this->candidatura($politico, $cargo, $partido, 2018, 100);
        $comparada = $this->candidatura($politico, $cargo, $partido, 2022, 50);
        $this->resultado($base, $cidade, 100);
        $this->resultado($comparada, $cidade, 50);

        Livewire::test(PainelExecutivo::class)
            ->set('politico', $politico->slug)
            ->assertSee('Resumo executivo')
            ->assertSee('Prioridades de análise territorial e revisão de dados')
            ->assertSee('Situação dos acompanhados')
            ->assertSee('Fila de revisão do espelho operacional')
            ->assertSee('CIDADE EXECUTIVA')
            ->assertDontSee('@endif', false);
    }

    private function partido(): Partido
    {
        return Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );
    }

    private function candidatura(Politico $politico, Cargo $cargo, Partido $partido, int $ano, int $votosTotal): Candidatura
    {
        $eleicao = Eleicao::query()->create([
            'ano' => $ano,
            'turno' => 1,
            'tipo' => $cargo->nome === 'Prefeito' ? 'municipal' : 'geral',
            'descricao' => (string) $ano,
            'status' => 'concluida',
        ]);

        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10',
            'nome_urna' => $politico->nome_publico,
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
