<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Livewire\Politica\V2\EspelhoOperacionalEdit;
use App\Livewire\Politica\V2\MapaInterativo;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaV2ExperienciaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function espelho_legislativo_prioriza_republicanos_e_executivo_mostra_todos(): void
    {
        $cidade = Cidade::query()->create(['nome' => 'Salvador', 'latitude' => -12.97, 'longitude' => -38.50]);
        $eleicao = Eleicao::query()->create(['ano' => 2026, 'turno' => 1, 'tipo' => 'geral', 'descricao' => 'Eleições Gerais 2026']);
        $deputado = Cargo::query()->create(['tse_codigo' => '0006', 'nome' => 'Deputado Federal', 'ordem' => 40]);
        $presidente = Cargo::query()->create(['tse_codigo' => '0001', 'nome' => 'Presidente', 'ordem' => 10]);
        $republicanos = Partido::query()->create(['numero' => 10, 'sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos']);
        $outro = Partido::query()->create(['numero' => 99, 'sigla' => 'OUTRO', 'nome' => 'Outro']);

        $rep = Politico::query()->create(['nome_completo' => 'Candidato Republicanos', 'nome_publico' => 'Candidato Republicanos', 'slug' => 'candidato-republicanos']);
        $repZero = Politico::query()->create(['nome_completo' => 'Republicanos Sem Votos', 'nome_publico' => 'Republicanos Sem Votos', 'slug' => 'republicanos-sem-votos']);
        $depOutro = Politico::query()->create(['nome_completo' => 'Deputado Outro', 'nome_publico' => 'Deputado Outro', 'slug' => 'deputado-outro']);
        $presA = Politico::query()->create(['nome_completo' => 'Presidente A', 'nome_publico' => 'Presidente A', 'slug' => 'presidente-a']);
        $presB = Politico::query()->create(['nome_completo' => 'Presidente B', 'nome_publico' => 'Presidente B', 'slug' => 'presidente-b']);

        $cRep = Candidatura::query()->create(['politico_id' => $rep->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $deputado->id, 'partido_id' => $republicanos->id, 'votos_total' => 100, 'origem' => 'tse']);
        Candidatura::query()->create(['politico_id' => $repZero->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $deputado->id, 'partido_id' => $republicanos->id, 'votos_total' => 0, 'origem' => 'tse']);
        $cOutro = Candidatura::query()->create(['politico_id' => $depOutro->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $deputado->id, 'partido_id' => $outro->id, 'votos_total' => 90, 'origem' => 'tse']);
        $cPresA = Candidatura::query()->create(['politico_id' => $presA->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $presidente->id, 'partido_id' => $republicanos->id, 'votos_total' => 200, 'origem' => 'tse']);
        $cPresB = Candidatura::query()->create(['politico_id' => $presB->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $presidente->id, 'partido_id' => $outro->id, 'votos_total' => 180, 'origem' => 'tse']);

        foreach ([[$cRep, 100], [$cOutro, 90], [$cPresA, 200], [$cPresB, 180]] as [$candidatura, $votos]) {
            ResultadoMunicipal::query()->create([
                'eleicao_id' => $eleicao->id,
                'candidatura_id' => $candidatura->id,
                'cidade_id' => $cidade->id,
                'votos' => $votos,
            ]);
        }

        Livewire::test(EspelhoInteligente::class, ['cidade' => $cidade])
            ->set('cargoId', $deputado->id)
            ->assertSee('Candidato Republicanos')
            ->assertSee('Republicanos Sem Votos')
            ->assertDontSee('Deputado Outro')
            ->assertSee('Filtro: REPUBLICANOS')
            ->set('escopoCandidatos', 'todos')
            ->assertSee('Deputado Outro')
            ->set('cargoId', $presidente->id)
            ->assertSee('Presidente A')
            ->assertSee('Presidente B')
            ->assertSet('escopoCandidatos', 'todos');
    }

    #[Test]
    public function editor_operacional_grava_na_tabela_v2_e_marca_revisao(): void
    {
        $cidade = Cidade::query()->create(['nome' => 'Feira de Santana']);

        Livewire::test(EspelhoOperacionalEdit::class, ['cidade' => $cidade])
            ->set('presidente_local', 'Responsável Feira')
            ->set('indicacao_bispo', 'Coordenação')
            ->set('filiados_republicanos', 42)
            ->set('observacoes', 'Revisado no teste')
            ->call('save')
            ->assertHasNoErrors();

        $espelho = EspelhoOperacional::query()->where('cidade_id', $cidade->id)->firstOrFail();
        $this->assertSame('Responsável Feira', $espelho->presidente_local);
        $this->assertSame(42, $espelho->filiados_republicanos);
        $this->assertNotNull($espelho->revisado_em);
    }

    #[Test]
    public function mapa_v2_renderiza_municipios_e_resultado_sem_depender_do_mapa_legado(): void
    {
        $cidade = Cidade::query()->create([
            'nome' => 'Salvador',
            'ibge_code' => 2927408,
            'latitude' => -12.97,
            'longitude' => -38.50,
            'populacao' => 2400000,
        ]);
        $eleicao = Eleicao::query()->create(['ano' => 2022, 'turno' => 1, 'tipo' => 'geral', 'descricao' => 'Eleições Gerais 2022']);
        $cargo = Cargo::query()->create(['tse_codigo' => '0006', 'nome' => 'Deputado Federal', 'ordem' => 40]);
        $politico = Politico::query()->create(['nome_completo' => 'Mapa Candidato', 'nome_publico' => 'Mapa Candidato', 'slug' => 'mapa-candidato']);
        $candidatura = Candidatura::query()->create(['politico_id' => $politico->id, 'eleicao_id' => $eleicao->id, 'cargo_id' => $cargo->id, 'votos_total' => 500, 'origem' => 'legacy_v1']);
        ResultadoMunicipal::query()->create(['eleicao_id' => $eleicao->id, 'candidatura_id' => $candidatura->id, 'cidade_id' => $cidade->id, 'votos' => 500]);

        Livewire::test(MapaInterativo::class)
            ->assertSee('Eleição')
            ->assertSee('Mapa Candidato')
            ->assertSee('Escolher município e abrir o espelho')
            ->set('candidaturaId', $candidatura->id)
            ->assertSee('SALVADOR');
    }
}
