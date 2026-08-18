<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\HistoricoAcompanhados;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\HistoricoAcompanhadosService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaHistoricoAcompanhadosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function pagina_e_restrita_aos_quatro_historicos_especiais(): void
    {
        Livewire::test(HistoricoAcompanhados::class)
            ->assertSee('Histórico oficial especial')
            ->assertDontSee('@if(', false)
            ->assertDontSee('@endif', false)
            ->assertSee('Márcio Marinho')
            ->assertSee('Rogéria Santos')
            ->assertSee('José de Arimatéia')
            ->assertSee('Jurailton Santos')
            ->assertDontSee('Lula')
            ->assertDontSee('Flávio Bolsonaro');
    }

    #[Test]
    public function evolucao_compara_apenas_eleicoes_do_mesmo_cargo(): void
    {
        $politico = Politico::query()->where('slug', 'marcio-marinho')->firstOrFail();
        $cargoFederal = Cargo::query()->where('nome', 'Deputado Federal')->firstOrFail();
        $cargoEstadual = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = Partido::query()->firstOrCreate(['sigla' => 'TESTE'], ['nome' => 'Teste']);

        $this->candidatura($politico, $cargoFederal, $partido, 2018, 100, true);
        $this->candidatura($politico, $cargoEstadual, $partido, 2020, 999, false);
        $this->candidatura($politico, $cargoFederal, $partido, 2022, 150, true);

        $resumo = app(HistoricoAcompanhadosService::class)->resumo('marcio-marinho');
        $eleicoes = collect($resumo['cards']->first()['eleicoes']);
        $federal2022 = $eleicoes->first(fn (array $item) => $item['ano'] === 2022 && $item['cargo'] === 'Deputado Federal');
        $estadual2020 = $eleicoes->first(fn (array $item) => $item['ano'] === 2020 && $item['cargo'] === 'Deputado Estadual');

        $this->assertSame(50, $federal2022['delta']);
        $this->assertSame(50.0, $federal2022['delta_percentual']);
        $this->assertSame(2018, $federal2022['delta_ano_base']);
        $this->assertNull($estadual2020['delta']);
    }

    #[Test]
    public function candidatura_2026_sem_resultado_nao_e_exibida_com_zero_votos(): void
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );
        $eleicao = Eleicao::query()->create([
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições 2026',
            'uf' => 'BA',
        ]);

        Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'tse_sq_candidato' => '2026-TESTE',
            'numero_urna' => '10123',
            'nome_urna' => 'JURAILTON SANTOS',
            'uf' => 'BA',
            'origem' => 'tse_dados_abertos',
            'situacao_registro' => '#NE',
            'votos_total' => 0,
        ]);

        $resumo = app(HistoricoAcompanhadosService::class)->resumo('jurailton-santos', '', 2026);
        $eleicao2026 = collect($resumo['cards']->first()['eleicoes'])->first();

        $this->assertNull($eleicao2026['votos']);
        $this->assertFalse($eleicao2026['tem_resultado']);
        $this->assertSame('Situação ainda não disponibilizada', $eleicao2026['situacao']);

        Livewire::test(HistoricoAcompanhados::class)
            ->set('politico', 'jurailton-santos')
            ->set('ano', '2026')
            ->assertSee('Sem votos oficiais')
            ->assertDontSee('0 votos');
    }

    private function candidatura(Politico $politico, Cargo $cargo, Partido $partido, int $ano, int $votos, bool $eleito): Candidatura
    {
        $eleicao = Eleicao::query()->create([
            'ano' => $ano,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleição '.$ano,
            'uf' => 'BA',
        ]);

        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => (string) (1000 + $ano),
            'nome_urna' => $politico->nome_publico,
            'uf' => 'BA',
            'origem' => 'tse_dados_abertos',
            'tse_sq_candidato' => 'sq-'.$ano.'-'.$cargo->id,
            'situacao_registro' => 'APTO',
            'situacao_eleicao' => $eleito ? 'ELEITO' : 'NÃO ELEITO',
            'votos_total' => $votos,
            'eleito' => $eleito,
        ]);
    }
}
