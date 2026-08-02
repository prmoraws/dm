<?php

namespace Tests\Feature;

use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CadastroTdaEstruturaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tabela_captacao_tdas_existe_com_colunas_esperadas(): void
    {
        $this->assertTrue(Schema::hasTable('captacao_tdas'));

        $this->assertTrue(Schema::hasColumns('captacao_tdas', [
            'id', 'bloco_id', 'regiao_id', 'igreja_id', 'estado_id',
            'nome', 'celular', 'cpf', 'rg', 'cidade_id',
            'condicao_atual', 'sexo', 'dias_reunioes', 'dias_evangelizacao',
            'dias_trabalho_reuniao', 'colaborador', 'obreiro', 'levita',
            'status', 'motivo_rejeicao', 'revisado_por', 'revisado_em',
            'created_at', 'updated_at',
        ]));
    }

    /** @test */
    public function tabela_cadastro_tdas_existe_com_colunas_esperadas(): void
    {
        $this->assertTrue(Schema::hasTable('cadastro_tdas'));

        $this->assertTrue(Schema::hasColumns('cadastro_tdas', [
            'id', 'bloco_id', 'regiao_id', 'igreja_id', 'estado_id',
            'nome', 'celular', 'cpf', 'rg', 'cidade_id',
            'condicao_atual', 'sexo', 'dias_reunioes', 'dias_evangelizacao',
            'dias_trabalho_reuniao', 'colaborador', 'obreiro', 'levita',
            'created_at', 'updated_at',
        ]));

        // Tabela final não deve ter colunas de controle de fluxo
        $this->assertFalse(Schema::hasColumn('cadastro_tdas', 'status'));
        $this->assertFalse(Schema::hasColumn('cadastro_tdas', 'revisado_por'));
    }

    /** @test */
    public function model_captacao_tda_tem_casts_e_relacionamentos_corretos(): void
    {
        $model = new CaptacaoTda();

        $this->assertEquals('captacao_tdas', $model->getTable());
        $this->assertEquals('array', $model->getCasts()['dias_reunioes']);
        $this->assertEquals('boolean', $model->getCasts()['tem_filhos']);
        $this->assertEquals('date', $model->getCasts()['data_nascimento']);

        $this->assertTrue(method_exists($model, 'bloco'));
        $this->assertTrue(method_exists($model, 'regiao'));
        $this->assertTrue(method_exists($model, 'igreja'));
        $this->assertTrue(method_exists($model, 'estado'));
        $this->assertTrue(method_exists($model, 'cidade'));
        $this->assertTrue(method_exists($model, 'revisor'));
    }

    /** @test */
    public function model_cadastro_tda_tem_casts_e_relacionamentos_corretos(): void
    {
        $model = new CadastroTda();

        $this->assertEquals('cadastro_tdas', $model->getTable());
        $this->assertEquals('array', $model->getCasts()['dias_trabalho_reuniao']);
        $this->assertEquals('boolean', $model->getCasts()['colaborador']);

        $this->assertTrue(method_exists($model, 'bloco'));
        $this->assertTrue(method_exists($model, 'regiao'));
        $this->assertTrue(method_exists($model, 'igreja'));
        $this->assertTrue(method_exists($model, 'estado'));
        $this->assertTrue(method_exists($model, 'cidade'));
    }

    /** @test */
    public function cpf_e_unico_apenas_na_tabela_final(): void
    {
        $indexes = Schema::getIndexes('cadastro_tdas');
        $cpfUnique = collect($indexes)->first(fn ($idx) => $idx['name'] === 'cadastro_tdas_cpf_unique');

        $this->assertNotNull($cpfUnique, 'A tabela cadastro_tdas deve ter índice único em cpf.');
        $this->assertTrue($cpfUnique['unique']);

        $indexesPendente = Schema::getIndexes('captacao_tdas');
        $cpfUniquePendente = collect($indexesPendente)->first(fn ($idx) => str_contains($idx['name'], 'cpf') && $idx['unique']);

        $this->assertNull($cpfUniquePendente, 'A tabela captacao_tdas (pendente) não deve ter cpf único.');
    }
}
