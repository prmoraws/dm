<?php

namespace Tests\Feature;

use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\Politico;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaV2EstruturaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tabelas_centrais_da_politica_v2_existem(): void
    {
        foreach ([
            'politica_politicos', 'politica_partidos', 'politica_cargos', 'politica_eleicoes',
            'politica_candidaturas', 'politica_filiacoes', 'politica_mandatos', 'politica_acompanhamentos',
            'politica_zonas', 'politica_secoes', 'politica_resultados_municipais', 'politica_resultados_zonas',
            'politica_resultados_secoes', 'politica_fontes_estado', 'politica_apuracoes',
            'politica_apuracao_candidaturas', 'politica_apuracao_historico', 'politica_espelho_inteligencia',
            'politica_espelho_operacional', 'politica_migracoes_dados',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} não foi criada.");
        }
    }

    #[Test]
    public function cidades_expoem_coordenadas_necessarias_para_o_mapa_v2(): void
    {
        $this->assertTrue(Schema::hasColumns('politica_cidades', [
            'nome', 'ibge_code', 'latitude', 'longitude', 'populacao',
        ]));
    }

    #[Test]
    public function candidaturas_separam_politico_eleicao_cargo_e_partido(): void
    {
        $this->assertTrue(Schema::hasColumns('politica_candidaturas', [
            'politico_id', 'eleicao_id', 'cargo_id', 'partido_id', 'tse_sq_candidato',
            'numero_urna', 'nome_urna', 'cidade_id', 'origem', 'legacy_candidato_id', 'origem_chave',
            'situacao_registro', 'situacao_eleicao',
            'votos_total', 'percentual_total', 'eleito', 'sincronizado_em',
        ]));
    }

    #[Test]
    public function apuracao_tem_estado_consolidado_e_historico_separados(): void
    {
        $this->assertTrue(Schema::hasColumns('politica_apuracoes', [
            'eleicao_id', 'cargo_id', 'abrangencia_tipo', 'abrangencia_chave',
            'tse_idg', 'secoes_total', 'secoes_totalizadas', 'percentual_secoes',
            'totalizacao_final', 'sincronizado_em',
        ]));

        $this->assertTrue(Schema::hasColumns('politica_apuracao_historico', [
            'apuracao_id', 'candidatura_id', 'capturado_em', 'votos', 'percentual', 'percentual_secoes',
        ]));
    }

    #[Test]
    public function models_v2_expoem_relacionamentos_essenciais(): void
    {
        $politico = new Politico();
        $candidatura = new Candidatura();
        $eleicao = new Eleicao();
        $apuracao = new Apuracao();
        $espelho = new EspelhoInteligencia();

        $this->assertSame('politica_politicos', $politico->getTable());
        $this->assertSame('politica_candidaturas', $candidatura->getTable());
        $this->assertTrue(method_exists($politico, 'candidaturas'));
        $this->assertTrue(method_exists($politico, 'mandatos'));
        $this->assertTrue(method_exists($politico, 'acompanhamento'));
        $this->assertTrue(method_exists($candidatura, 'cidade'));
        $this->assertTrue(method_exists($candidatura, 'resultadosMunicipais'));
        $this->assertTrue(method_exists($eleicao, 'apuracoes'));
        $this->assertTrue(method_exists($apuracao, 'historico'));
        $this->assertTrue(method_exists($espelho, 'cidade'));
    }

    #[Test]
    public function seeder_prioritario_usa_flavio_bolsonaro_e_nao_jair_bolsonaro(): void
    {
        $this->seed(PoliticaV2Seeder::class);

        $this->assertDatabaseCount('politica_acompanhamentos', 8);
        $this->assertDatabaseHas('politica_politicos', ['nome_publico' => 'Flávio Bolsonaro']);
        $this->assertDatabaseMissing('politica_politicos', ['nome_publico' => 'Jair Bolsonaro']);

        $flavio = Politico::query()->where('nome_publico', 'Flávio Bolsonaro')->firstOrFail();
        $this->assertTrue(Acompanhamento::query()
            ->where('politico_id', $flavio->id)
            ->where('grupo', 'presidencia')
            ->where('ativo', true)
            ->exists());
    }
}
