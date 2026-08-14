<?php

namespace Tests\Feature;

use App\Models\Politica\Bairro;
use App\Models\Politica\Cidade;
use App\Models\Politica\Espelho;
use App\Models\Politica\LocalVotacao;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\EspelhoOperacional;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\ResultadoSecao;
use App\Services\Politica\V2\LegacyV1MigrationService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaV2MigracaoLegadoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function migra_prioritario_agregando_duplicidades_e_preservando_rastreabilidade(): void
    {
        $this->seed(PoliticaV2Seeder::class);

        $cidade = Cidade::query()->create([
            'nome' => 'Salvador',
            'ibge_code' => 2927408,
            'populacao' => 2_400_000,
        ]);
        $bairro = Bairro::query()->create(['cidade_id' => $cidade->id, 'nome' => 'BAIRRO NÃO INFORMADO']);
        $local = LocalVotacao::query()->create([
            'cidade_id' => $cidade->id,
            'bairro_id' => $bairro->id,
            'nome' => 'ESCOLA TESTE',
            'endereco' => 'Zona: 1 / Seção: 10',
        ]);

        DB::table('politica_candidatos')->insert([
            'id' => 4623,
            'nome' => 'ROGERIA DE ALMEIDA PEREIRA DOS SANTOS',
            'partido' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('politica_votacao_detalhada')->insert([
            [
                'local_votacao_id' => $local->id,
                'candidato_id' => 4623,
                'ano_eleicao' => 2022,
                'cargo' => 'DEPUTADO FEDERAL',
                'votos_recebidos' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'local_votacao_id' => $local->id,
                'candidato_id' => 4623,
                'ano_eleicao' => 2022,
                'cargo' => 'DEPUTADO FEDERAL',
                'votos_recebidos' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Espelho::query()->create([
            'cidade_id' => $cidade->id,
            'presidente_local' => 'Responsável Local',
            'indicacao_bispo' => 'Indicação Teste',
            'filiados_republicanos' => 120,
            'prefeito_atual_nome' => 'Prefeito Legado',
            'prefeito_atual_partido' => 'XYZ',
            'prefeito_atual_votos' => 50000,
            'observacoes' => 'Observação histórica',
        ]);

        DB::table('cidade_candidato')->insert([
            'cidade_id' => $cidade->id,
            'candidato_id' => 4623,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(LegacyV1MigrationService::class);
        $stats = $service->migrar('prioritarios');

        $politico = Politico::query()->where('slug', 'rogeria-santos')->firstOrFail();
        $candidatura = Candidatura::query()->where('legacy_candidato_id', 4623)->firstOrFail();

        $this->assertSame('ROGERIA DE ALMEIDA PEREIRA DOS SANTOS', $politico->nome_completo);
        $this->assertSame('legacy_v1', $candidatura->origem);
        $this->assertSame(5, $candidatura->votos_total);
        $this->assertNull($candidatura->cidade_id, 'Deputado federal tem abrangência estadual, não municipal.');

        $this->assertSame(1, ResultadoSecao::query()->where('candidatura_id', $candidatura->id)->count());
        $this->assertSame(5, (int) ResultadoSecao::query()->where('candidatura_id', $candidatura->id)->value('votos'));
        $this->assertSame(5, (int) ResultadoMunicipal::query()->where('candidatura_id', $candidatura->id)->value('votos'));

        $operacional = EspelhoOperacional::query()->where('cidade_id', $cidade->id)->firstOrFail();
        $this->assertSame('Responsável Local', $operacional->presidente_local);
        $this->assertSame('Prefeito Legado', $operacional->dados_publicos_legados['prefeito_atual_nome']);
        $this->assertDatabaseHas('politica_espelho_inteligencia', [
            'cidade_id' => $cidade->id,
            'candidatura_id' => $candidatura->id,
            'classificacao' => 'acompanhamento',
        ]);
        $this->assertSame(1, $stats['favoritos_convertidos']);

        $validacao = $service->validarMigracao('prioritarios');
        $this->assertTrue($validacao['ok']);
        $this->assertSame(5, $validacao['candidaturas'][0]['v1']);
        $this->assertSame(5, $validacao['candidaturas'][0]['v2_secoes']);
        $this->assertSame(5, $validacao['candidaturas'][0]['v2_municipios']);

        $contagens = [
            'candidaturas' => Candidatura::query()->count(),
            'secoes' => DB::table('politica_secoes')->count(),
            'resultados_secoes' => ResultadoSecao::query()->count(),
            'resultados_municipais' => ResultadoMunicipal::query()->count(),
        ];

        $service->migrar('prioritarios');

        $this->assertSame($contagens['candidaturas'], Candidatura::query()->count());
        $this->assertSame($contagens['secoes'], DB::table('politica_secoes')->count());
        $this->assertSame($contagens['resultados_secoes'], ResultadoSecao::query()->count());
        $this->assertSame($contagens['resultados_municipais'], ResultadoMunicipal::query()->count());
        $this->assertSame(5, (int) ResultadoSecao::query()->where('candidatura_id', $candidatura->id)->value('votos'));
    }

    #[Test]
    public function candidatura_municipal_recebe_cidade_e_partido_do_legado(): void
    {
        $this->seed(PoliticaV2Seeder::class);

        $cidade = Cidade::query()->create(['nome' => 'Feira de Santana', 'ibge_code' => 2910800]);
        $bairro = Bairro::query()->create(['cidade_id' => $cidade->id, 'nome' => 'CENTRO']);
        $local = LocalVotacao::query()->create([
            'cidade_id' => $cidade->id,
            'bairro_id' => $bairro->id,
            'nome' => 'COLÉGIO TESTE',
            'endereco' => 'Zona: 2 / Seção: 20',
        ]);

        DB::table('politica_candidatos')->insert([
            'id' => 5000,
            'nome' => 'CANDIDATO MUNICIPAL TESTE',
            'partido' => 'REPUBLICANOS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('politica_votacao_detalhada')->insert([
            'local_votacao_id' => $local->id,
            'candidato_id' => 5000,
            'ano_eleicao' => 2024,
            'cargo' => 'VEREADOR',
            'votos_recebidos' => 17,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app(LegacyV1MigrationService::class)->migrar('todos');

        $candidatura = Candidatura::query()->where('legacy_candidato_id', 5000)->with(['partido', 'cargo'])->firstOrFail();
        $this->assertSame($cidade->id, $candidatura->cidade_id);
        $this->assertSame('REPUBLICANOS', $candidatura->partido?->sigla);
        $this->assertSame('Vereador', $candidatura->cargo?->nome);
        $this->assertSame(17, $candidatura->votos_total);
    }
}
