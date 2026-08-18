<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\Eleicoes2026;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\Eleicoes2026Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaEleicoes2026Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function painel_separa_executivo_senado_e_recorte_republicanos_sem_criar_ranking(): void
    {
        $eleicao = Eleicao::query()->create([
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2026',
            'uf' => 'BA',
        ]);

        $presidente = Cargo::query()->create(['nome' => 'Presidente', 'ordem' => 10]);
        $governador = Cargo::query()->create(['nome' => 'Governador', 'ordem' => 20]);
        $senador = Cargo::query()->create(['nome' => 'Senador', 'ordem' => 30]);
        $federal = Cargo::query()->create(['nome' => 'Deputado Federal', 'ordem' => 40]);
        $estadual = Cargo::query()->create(['nome' => 'Deputado Estadual', 'ordem' => 50]);

        $pt = Partido::query()->create(['numero' => 13, 'sigla' => 'PT', 'nome' => 'PT']);
        $pl = Partido::query()->create(['numero' => 22, 'sigla' => 'PL', 'nome' => 'PL']);
        $republicanos = Partido::query()->create(['numero' => 10, 'sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos']);

        $lula = $this->politico('Lula', 'lula-teste');
        Acompanhamento::query()->create(['politico_id' => $lula->id, 'grupo' => 'presidencia', 'prioridade' => 1, 'ordem' => 1, 'ativo' => true]);
        $this->candidatura($eleicao, $presidente, $pt, $lula, '13', 'LULA', 'BR', '1001');
        $this->candidatura($eleicao, $presidente, $pl, $this->politico('Presidente Dois', 'presidente-dois'), '22', 'PRESIDENTE DOIS', 'BR', '1002');
        $this->candidatura($eleicao, $governador, $pt, $this->politico('Governador Um', 'governador-um'), '13', 'GOVERNADOR UM', 'BA', '2001');
        $this->candidatura($eleicao, $governador, $pl, $this->politico('Governador Dois', 'governador-dois'), '22', 'GOVERNADOR DOIS', 'BA', '2002');
        $this->candidatura($eleicao, $senador, $republicanos, $this->politico('Senador Rep', 'senador-rep'), '101', 'SENADOR REP', 'BA', '3001');
        $this->candidatura($eleicao, $federal, $republicanos, $this->politico('Federal Rep', 'federal-rep'), '1010', 'FEDERAL REP', 'BA', '4001');
        $this->candidatura($eleicao, $estadual, $republicanos, $this->politico('Estadual Rep', 'estadual-rep'), '10101', 'ESTADUAL REP', 'BA', '5001');

        $resumo = app(Eleicoes2026Service::class)->resumo();

        $this->assertSame(2, $resumo['presidencia']['total']);
        $this->assertSame(2, $resumo['governo']['total']);
        $this->assertSame(1, $resumo['senado']['total']);
        $this->assertSame(3, $resumo['republicanos']['total']);
        $this->assertSame(1, $resumo['metricas']['acompanhadas']);
        $this->assertSame('REPUBLICANOS', $resumo['republicanos']['partido']);
        $this->assertFalse($resumo['coletor']['live_enabled']);
        $this->assertSame(60, $resumo['coletor']['poll_seconds']);

        Livewire::test(Eleicoes2026::class)
            ->assertSee('Eleições 2026')
            ->assertSee('Presidência')
            ->assertSee('Governo da Bahia')
            ->assertSee('Senado · Bahia')
            ->assertSee('Recorte econômico do banco: REPUBLICANOS')
            ->assertSee('LULA')
            ->assertSee('GOVERNADOR DOIS')
            ->assertSee('SENADOR REP')
            ->assertSee('FEDERAL REP')
            ->assertSee('Coletor TSE: protegido / desligado')
            ->assertSee('Registro não é resultado')
            ->assertDontSee('ranking');
    }

    private function politico(string $nome, string $slug): Politico
    {
        return Politico::query()->create([
            'nome_completo' => $nome,
            'nome_publico' => $nome,
            'slug' => $slug,
        ]);
    }

    private function candidatura(Eleicao $eleicao, Cargo $cargo, Partido $partido, Politico $politico, string $numero, string $urna, string $uf, string $sq): Candidatura
    {
        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'tse_sq_candidato' => $sq,
            'numero_urna' => $numero,
            'nome_urna' => $urna,
            'uf' => $uf,
            'origem' => 'tse_dados_abertos',
            'situacao_registro' => '#NE',
            'sincronizado_em' => now(),
        ]);
    }
}
