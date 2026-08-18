<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\ResultadoZona;
use App\Models\Politica\V2\Zona;
use App\Services\Politica\V2\EspelhoCidadeRelatorioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaEspelhoCidadeRelatorioTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function relatorio_da_cidade_reune_operacional_historico_zonas_ranking_e_auditoria_do_favorito(): void
    {
        [$cidade, $politico, $cargo, $partido, $c2018, $c2022] = $this->cenarioHistorico();
        $outraCidade = Cidade::query()->create(['nome' => 'Outra Cidade', 'ibge_code' => 2999999]);

        ResultadoMunicipal::query()->create(['eleicao_id' => $c2018->eleicao_id, 'candidatura_id' => $c2018->id, 'cidade_id' => $cidade->id, 'votos' => 100, 'percentual' => 8.5]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $c2018->eleicao_id, 'candidatura_id' => $c2018->id, 'cidade_id' => $outraCidade->id, 'votos' => 400]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $c2022->id, 'cidade_id' => $cidade->id, 'votos' => 150, 'percentual' => 12.5, 'posicao' => 2]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $c2022->id, 'cidade_id' => $outraCidade->id, 'votos' => 850]);

        $concorrente = Politico::query()->create(['nome_completo' => 'Concorrente', 'nome_publico' => 'Concorrente', 'slug' => 'concorrente-relatorio']);
        $cConcorrente = Candidatura::query()->create([
            'politico_id' => $concorrente->id,
            'eleicao_id' => $c2022->eleicao_id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '1001',
            'votos_total' => 1200,
            'origem' => 'tse_dados_abertos',
            'tse_sq_candidato' => '2022002',
        ]);
        ResultadoMunicipal::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $cConcorrente->id, 'cidade_id' => $cidade->id, 'votos' => 200]);

        $zona1 = Zona::query()->create(['cidade_id' => $cidade->id, 'numero' => 1, 'tse_codigo' => '001']);
        $zona2 = Zona::query()->create(['cidade_id' => $cidade->id, 'numero' => 2, 'tse_codigo' => '002']);
        ResultadoZona::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $c2022->id, 'zona_id' => $zona1->id, 'votos' => 70]);
        ResultadoZona::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $c2022->id, 'zona_id' => $zona2->id, 'votos' => 80]);

        EspelhoOperacional::query()->create([
            'cidade_id' => $cidade->id,
            'presidente_local' => 'Presidente Local',
            'indicacao_bispo' => 'Indicação Teste',
            'filiados_republicanos' => 77,
            'observacoes' => 'Contexto operacional revisado.',
            'revisado_em' => now(),
        ]);

        EspelhoInteligencia::query()->create([
            'cidade_id' => $cidade->id,
            'eleicao_id' => $c2022->eleicao_id,
            'candidatura_id' => $c2022->id,
            'contexto_chave' => 'eleicao:'.$c2022->eleicao_id.':candidatura:'.$c2022->id,
            'classificacao' => 'favorito',
            'prioridade' => 1,
            'meta_votos' => 200,
            'observacoes' => 'Favorito do município.',
        ]);

        $dados = app(EspelhoCidadeRelatorioService::class)->gerar($cidade, $c2022->id);

        $this->assertSame('SALVADOR', $dados['cidade']['nome']);
        $this->assertSame($politico->nome_publico, $dados['candidato']['nome']);
        $this->assertSame(150, $dados['candidato']['votos_municipio']);
        $this->assertSame(15.0, $dados['candidato']['participacao_nos_votos_do_candidato']);
        $this->assertSame('Presidente Local', $dados['operacional']['presidente_local']);
        $this->assertSame('favorito', $dados['favorito']['classificacao']);
        $this->assertCount(2, $dados['historico']);
        $this->assertSame(50, $dados['historico'][1]['delta']);
        $this->assertSame(150, collect($dados['zonas'])->sum('votos'));
        $this->assertTrue(collect($dados['ranking'])->contains(fn (array $i) => $i['favorito'] && $i['nome'] === $politico->nome_publico));
        $this->assertTrue($dados['auditoria']['municipios_conferem_total']);
        $this->assertTrue($dados['auditoria']['zonas_conferem_municipio']);
    }

    #[Test]
    public function ausencia_de_linha_municipal_permanece_nula_no_relatorio_e_nao_vira_zero(): void
    {
        $cidade = Cidade::query()->create(['nome' => 'Cidade Sem Linha', 'ibge_code' => 2999998]);
        $eleicao = Eleicao::query()->create(['ano' => 2026, 'turno' => 1, 'tipo' => 'geral', 'descricao' => '2026']);
        $cargo = Cargo::query()->create(['nome' => 'Deputado Estadual', 'ordem' => 30]);
        $partido = Partido::query()->create(['numero' => 10, 'sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos']);
        $politico = Politico::query()->create(['nome_completo' => 'Sem Linha', 'nome_publico' => 'Sem Linha', 'slug' => 'sem-linha-relatorio']);
        $candidatura = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10123',
            'votos_total' => 0,
            'origem' => 'tse_dados_abertos',
            'tse_sq_candidato' => '2026001',
        ]);

        $dados = app(EspelhoCidadeRelatorioService::class)->gerar($cidade, $candidatura->id);

        $this->assertNull($dados['candidato']['votos_municipio']);
        $this->assertNull($dados['candidato']['percentual_municipio']);
        $this->assertFalse($dados['auditoria']['tem_resultado_municipal']);
        $this->assertNull($dados['auditoria']['municipios_conferem_total']);
        $this->assertSame('Sem linha oficial', $dados['historico'][0]['tem_linha_municipal'] ? 'Com linha' : 'Sem linha oficial');
    }

    #[Test]
    public function espelho_permite_selecionar_marcar_favorito_e_habilita_as_duas_exportacoes(): void
    {
        [$cidade, $politico, $cargo, $partido, $c2018, $c2022] = $this->cenarioHistorico();
        ResultadoMunicipal::query()->create(['eleicao_id' => $c2022->eleicao_id, 'candidatura_id' => $c2022->id, 'cidade_id' => $cidade->id, 'votos' => 150]);

        Livewire::test(EspelhoInteligente::class, ['cidade' => $cidade])
            ->set('cargoId', $cargo->id)
            ->set('candidaturaRelatorioId', $c2022->id)
            ->assertSee('Relatório robusto por cidade')
            ->assertSee('Exportar PDF')
            ->assertSee('Exportar Excel')
            ->call('marcarFavoritoRelatorio')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('politica_espelho_inteligencia', [
            'cidade_id' => $cidade->id,
            'candidatura_id' => $c2022->id,
            'classificacao' => 'favorito',
            'prioridade' => 1,
        ]);
    }

    #[Test]
    public function paginas_principais_da_politica_definem_section_title_usada_pelo_layout(): void
    {
        $arquivos = [
            'livewire/politica/city-dashboard.blade.php',
            'livewire/politica/v2/dashboard.blade.php',
            'livewire/politica/v2/acompanhamento-prioritario.blade.php',
            'livewire/politica/v2/politico-show.blade.php',
            'livewire/politica/v2/espelho-inteligente.blade.php',
            'livewire/politica/v2/espelho-operacional-edit.blade.php',
            'livewire/politica/v2/mapa-interativo.blade.php',
            'livewire/politica/v2/dados-oficiais.blade.php',
            'livewire/politica/v2/eleicoes-2026.blade.php',
            'livewire/politica/v2/historico-acompanhados.blade.php',
            'livewire/politica/v2/comparativo-territorial.blade.php',
            'livewire/politica/v2/inteligencia-territorial.blade.php',
            'livewire/politica/v2/painel-executivo.blade.php',
        ];

        foreach ($arquivos as $arquivo) {
            $conteudo = file_get_contents(resource_path('views/'.$arquivo));
            $this->assertStringStartsWith("@section('title'", $conteudo, $arquivo);
        }
    }

    private function cenarioHistorico(): array
    {
        $cidade = Cidade::query()->create(['nome' => 'Salvador', 'ibge_code' => 2927408, 'populacao' => 2400000, 'cadeiras_camara' => 43]);
        $politico = Politico::query()->create(['nome_completo' => 'Candidato Favorito', 'nome_publico' => 'Candidato Favorito', 'slug' => 'candidato-favorito-relatorio']);
        $cargo = Cargo::query()->create(['nome' => 'Deputado Federal', 'ordem' => 40]);
        $partido = Partido::query()->create(['numero' => 10, 'sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos']);
        $e2018 = Eleicao::query()->create(['ano' => 2018, 'turno' => 1, 'tipo' => 'geral', 'descricao' => '2018']);
        $e2022 = Eleicao::query()->create(['ano' => 2022, 'turno' => 1, 'tipo' => 'geral', 'descricao' => '2022']);

        $c2018 = Candidatura::query()->create([
            'politico_id' => $politico->id, 'eleicao_id' => $e2018->id, 'cargo_id' => $cargo->id, 'partido_id' => $partido->id,
            'numero_urna' => '1010', 'votos_total' => 500, 'situacao_eleicao' => 'ELEITO', 'origem' => 'tse_dados_abertos', 'tse_sq_candidato' => '2018001',
        ]);
        $c2022 = Candidatura::query()->create([
            'politico_id' => $politico->id, 'eleicao_id' => $e2022->id, 'cargo_id' => $cargo->id, 'partido_id' => $partido->id,
            'numero_urna' => '1010', 'votos_total' => 1000, 'situacao_eleicao' => 'ELEITO', 'origem' => 'tse_dados_abertos', 'tse_sq_candidato' => '2022001',
        ]);

        return [$cidade, $politico, $cargo, $partido, $c2018, $c2022];
    }
}
