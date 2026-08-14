<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\AcompanhamentoPrioritario;
use App\Livewire\Politica\V2\Dashboard;
use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Livewire\Politica\V2\PoliticoShow;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\PoliticaDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaV2TelasTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function telas_v2_renderizam_dados_existentes_sem_consultar_fontes_externas(): void
    {
        $cidade = Cidade::query()->create([
            'nome' => 'Salvador',
            'ibge_code' => 2927408,
            'populacao' => 2400000,
        ]);

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
        $cargo = Cargo::query()->create(['tse_codigo' => '0006', 'nome' => 'Deputado Federal', 'ordem' => 40]);
        $candidatura = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'votos_total' => 82012,
            'origem' => 'legacy_v1',
        ]);

        ResultadoMunicipal::query()->create([
            'eleicao_id' => $eleicao->id,
            'candidatura_id' => $candidatura->id,
            'cidade_id' => $cidade->id,
            'votos' => 10000,
        ]);

        EspelhoOperacional::query()->create([
            'cidade_id' => $cidade->id,
            'presidente_local' => 'Responsável Salvador',
        ]);

        app(PoliticaDashboardService::class)->esquecerCache();

        Livewire::test(Dashboard::class)
            ->assertSee('Municípios')
            ->assertSee('Rogéria Santos');

        Livewire::test(AcompanhamentoPrioritario::class)
            ->assertSee('Rogéria Santos')
            ->assertSee('Deputados Federais BA');

        Livewire::test(PoliticoShow::class, ['politico' => $politico])
            ->assertSee('Rogéria Santos')
            ->assertSee('82.012')
            ->assertSee('SALVADOR');

        Livewire::test(EspelhoInteligente::class, ['cidade' => $cidade])
            ->assertSee('Responsável Salvador')
            ->assertSee('Rogéria Santos')
            ->assertSee('10.000');
    }
}
