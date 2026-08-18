<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\AcompanhamentoPrioritario;
use App\Livewire\Politica\V2\DadosOficiais;
use App\Livewire\Politica\V2\PoliticoShow;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaCandidatura2026InterfaceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function acompanhamento_distingue_registro_tse_2026_de_resultado_eleitoral(): void
    {
        [$politico] = $this->criarRegistroOficial2026();

        Livewire::test(AcompanhamentoPrioritario::class)
            ->assertSee('Candidato Teste')
            ->assertSee('Registro TSE 2026')
            ->assertSee('Situação ainda não disponibilizada')
            ->assertSee('Presidente · TESTE')
            ->assertDontSee('0 votos registrados');

        Livewire::test(PoliticoShow::class, ['politico' => $politico])
            ->assertSee('Registro de candidatura localizado no TSE')
            ->assertSee('Situação ainda não disponibilizada')
            ->assertSee('#NE')
            ->assertSee('280000000001')
            ->assertSee('Resultados territoriais ainda não disponíveis')
            ->assertDontSee('Votos totais');
    }

    #[Test]
    public function dados_oficiais_destaca_cobertura_2026_e_ultima_sincronizacao(): void
    {
        $this->criarRegistroOficial2026();

        Livewire::test(DadosOficiais::class)
            ->assertSee('Eleições 2026')
            ->assertSee('1 registros de candidatura importados do TSE')
            ->assertSee('Última sincronização 2026');
    }

    private function criarRegistroOficial2026(): array
    {
        $politico = Politico::query()->create([
            'nome_completo' => 'Candidato Teste',
            'nome_publico' => 'Candidato Teste',
            'slug' => 'candidato-teste-2026',
        ]);

        Acompanhamento::query()->create([
            'politico_id' => $politico->id,
            'grupo' => 'presidencia',
            'prioridade' => 1,
            'ordem' => 1,
            'ativo' => true,
        ]);

        $eleicao = Eleicao::query()->create([
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2026',
        ]);

        $cargo = Cargo::query()->create([
            'tse_codigo' => '0001',
            'nome' => 'Presidente',
            'ordem' => 10,
        ]);

        $partido = Partido::query()->create([
            'numero' => 99,
            'sigla' => 'TESTE',
            'nome' => 'Partido Teste',
        ]);

        $candidatura = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'tse_sq_candidato' => '280000000001',
            'numero_urna' => '99',
            'nome_urna' => 'CANDIDATO TESTE',
            'uf' => 'BR',
            'origem' => 'tse_dados_abertos',
            'situacao_registro' => '#NE',
            'votos_total' => 0,
            'sincronizado_em' => now(),
        ]);

        return [$politico, $candidatura];
    }
}
