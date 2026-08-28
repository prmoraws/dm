<?php

namespace Tests\Feature;

use App\Models\Universal\CadastroTda;
use App\Models\Universal\CaptacaoTda;
use App\Models\Universal\TdaResponsavelLegal;
use App\Models\Universal\TdaTermoAceite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TdaTermosEstruturaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cadastro_e_captacao_possuem_campos_necessarios_aos_termos(): void
    {
        foreach (['captacao_tdas', 'cadastro_tdas'] as $tabela) {
            $this->assertTrue(Schema::hasColumns($tabela, [
                'endereco_igreja', 'nacionalidade', 'complemento', 'assinatura',
                'testemunha_nome', 'testemunha_rg', 'testemunha_assinatura',
            ]));
        }

        $this->assertTrue(Schema::hasColumn('cadastro_tdas', 'captacao_tda_id'));
    }

    #[Test]
    public function tabelas_de_responsavel_e_aceites_sao_auditaveis(): void
    {
        $this->assertTrue(Schema::hasColumns('tda_responsaveis_legais', [
            'captacao_tda_id', 'cadastro_tda_id', 'nome', 'cpf',
            'data_nascimento', 'estado_id', 'cidade_id',
        ]));

        $this->assertTrue(Schema::hasColumns('tda_termo_aceites', [
            'captacao_tda_id', 'cadastro_tda_id', 'tipo', 'versao',
            'hash_documento', 'hash_assinatura', 'aceito_em', 'ip_hash',
            'user_agent', 'dados_snapshot', 'pdf_assinado', 'revogado_em',
        ]));
    }

    #[Test]
    public function models_expoem_casts_relacionamentos_e_versoes(): void
    {
        $this->assertSame('array', (new TdaTermoAceite)->getCasts()['dados_snapshot']);
        $this->assertSame('date', (new TdaResponsavelLegal)->getCasts()['data_nascimento']);
        $this->assertCount(3, TdaTermoAceite::VERSOES);

        foreach ([new CaptacaoTda, new CadastroTda] as $model) {
            $this->assertTrue(method_exists($model, 'responsavelLegal'));
            $this->assertTrue(method_exists($model, 'termosAceitos'));
        }
    }
}
