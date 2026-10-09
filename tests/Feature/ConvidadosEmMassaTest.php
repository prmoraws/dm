<?php

namespace Tests\Feature;

use App\Livewire\Unp\Oficios\Convidados;
use App\Services\Unp\ConvidadosEmMassa;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ConvidadosEmMassaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Banco temporário exclusivo: nenhum banco local ou de produção é usado.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('presidios', function (Blueprint $t) {
            $t->id(); $t->string('nome'); $t->timestamps();
        });
        Schema::create('convidados', function (Blueprint $t) {
            $t->id(); $t->string('nome'); $t->string('cpf')->unique(); $t->string('rg')->nullable(); $t->string('classe'); $t->timestamps();
        });
        foreach (['oficio_cops', 'oficio_eventos', 'oficio_formaturas', 'oficio_cursos', 'oficio_trabalhos', 'oficio_gerals', 'oficio_credencials'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id(); $t->unsignedBigInteger('presidio_id'); $t->dateTime('data_oficio')->nullable();
                $t->string('assunto')->nullable(); $t->string('evento')->nullable(); $t->string('nome')->nullable();
                $t->json('convidados')->nullable(); $t->timestamps();
            });
        }
        $migration = require database_path('migrations/2026_10_09_230000_create_unp_oficio_convidados_table.php');
        $migration->up();
        DB::table('presidios')->insert([['id' => 1, 'nome' => 'Presídio A'], ['id' => 2, 'nome' => 'Presídio B']]);
        DB::table('oficio_cops')->insert(['id' => 1, 'presidio_id' => 1, 'data_oficio' => '2026-10-01', 'convidados' => '[]']);
    }

    private function service(): ConvidadosEmMassa { return app(ConvidadosEmMassa::class); }

    public function test_filtra_presidio_e_ordena_tipos_por_data(): void
    {
        DB::table('oficio_eventos')->insert([
            ['id' => 1, 'presidio_id' => 1, 'data_oficio' => '2026-10-09'],
            ['id' => 2, 'presidio_id' => 2, 'data_oficio' => '2026-10-10'],
        ]);
        DB::table('oficio_credencials')->insert(['id' => 1, 'presidio_id' => 1, 'created_at' => '2026-10-08 12:00:00']);
        $this->assertSame(['evento:1', 'credencial:1', 'cop:1'], array_column($this->service()->oficios(1), 'value'));
        $this->assertStringContainsString('09/10/2026', $this->service()->oficios(1)[0]['text']);
    }

    public function test_importa_reutiliza_cpf_e_preserva_lista_cop(): void
    {
        DB::table('convidados')->insert(['id' => 1, 'nome' => 'CADASTRO ORIGINAL', 'cpf' => '52998224725', 'rg' => 'ORIGINAL', 'classe' => 'esposas']);
        DB::table('oficio_cops')->where('id', 1)->update(['convidados' => '[1]']);
        $texto = "Nome diferente;529.982.247-25;NOVO;organização\nOutra Pessoa;11144477735";
        $result = $this->service()->salvar(1, 'cop:1', $texto, 'convidados');
        $this->assertSame(1, $result['novos']);
        $this->assertDatabaseHas('convidados', ['id' => 1, 'nome' => 'CADASTRO ORIGINAL', 'rg' => 'ORIGINAL', 'classe' => 'esposas']);
        $this->assertCount(2, json_decode(DB::table('oficio_cops')->value('convidados'), true));
        $again = $this->service()->salvar(1, 'cop:1', $texto, 'convidados');
        $this->assertSame(0, $again['novos']);
        $this->assertSame(0, $again['vinculados']);
        $this->assertDatabaseCount('convidados', 2);
        $this->assertDatabaseCount('unp_oficio_convidados', 2);
    }

    public function test_vincula_oficio_geral_sem_alterar_seu_conteudo(): void
    {
        DB::table('oficio_gerals')->insert(['id' => 7, 'presidio_id' => 1, 'data_oficio' => '2026-10-09', 'assunto' => 'Assunto original']);
        $this->service()->salvar(1, 'geral:7', 'Maria Silva;52998224725', 'convidados');
        $this->assertDatabaseHas('unp_oficio_convidados', ['oficio_tipo' => 'geral', 'oficio_id' => 7]);
        $this->assertDatabaseHas('oficio_gerals', ['id' => 7, 'assunto' => 'Assunto original']);
        $this->assertCount(1, $this->service()->convidados(1, 'geral:7'));
    }

    public function test_rejeita_oficio_de_outro_presidio_sem_gravar(): void
    {
        try {
            $this->service()->salvar(2, 'cop:1', 'Maria Silva;52998224725', 'convidados');
            $this->fail('Deveria rejeitar o presídio incorreto.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('massaOficio', $e->errors());
        }
        $this->assertDatabaseCount('convidados', 0);
    }

    public function test_linha_invalida_impede_importacao_parcial(): void
    {
        try {
            $this->service()->salvar(1, 'cop:1', "Maria Silva;52998224725\nPessoa inválida;00000000000", 'convidados');
            $this->fail('Deveria rejeitar o CPF inválido.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('massaTexto', $e->errors());
        }
        $this->assertDatabaseCount('convidados', 0);
        $this->assertDatabaseCount('unp_oficio_convidados', 0);
    }

    public function test_rejeita_cpf_duplicado_com_e_sem_mascara(): void
    {
        $this->expectException(ValidationException::class);
        $this->service()->linhas("Maria Silva;52998224725\nMaria Silva;529.982.247-25", 'convidados');
    }

    public function test_aceita_colunas_de_planilha_e_classe_padrao(): void
    {
        $linhas = $this->service()->linhas("Maria Silva\t52998224725\tRG123", 'organização');
        $this->assertSame('organização', $linhas[0]['classe']);
        $this->assertSame('RG123', $linhas[0]['rg']);
    }

    public function test_falha_no_banco_reverte_cadastros_e_vinculos(): void
    {
        DB::unprepared("CREATE TRIGGER falha_importacao BEFORE INSERT ON convidados WHEN NEW.cpf = '111.444.777-35' BEGIN SELECT RAISE(ABORT, 'falha simulada'); END;");
        try {
            $this->service()->salvar(1, 'cop:1', "Maria Silva;52998224725\nJoão Silva;11144477735", 'convidados');
            $this->fail('Deveria falhar no segundo cadastro.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertStringContainsString('falha simulada', $e->getMessage());
        }
        $this->assertDatabaseCount('convidados', 0);
        $this->assertDatabaseCount('unp_oficio_convidados', 0);
    }

    public function test_tela_revisa_salva_e_limpa_oficio_ao_trocar_presidio(): void
    {
        Livewire::test(Convidados::class)
            ->set('massaPresidio', '1')->set('massaOficio', 'cop:1')
            ->set('massaTexto', 'Maria Silva;52998224725')
            ->call('revisarMassa')->assertHasNoErrors()->assertSee('Novo cadastro')
            ->call('salvarMassa')->assertHasNoErrors()->assertSet('massaTexto', '')
            ->assertSee('MARIA SILVA')
            ->set('massaPresidio', '2')->assertSet('massaOficio', '')->assertSet('massaPrevia', []);
        $this->assertDatabaseCount('unp_oficio_convidados', 1);
    }
}
