<?php

namespace Tests\Feature;

use App\Livewire\Unp\Oficios\Anexos;
use App\Models\Unp\Oficios\OficioAnexo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class AnexosConvidadosOficioTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
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
        $migration = require database_path('migrations/2025_06_20_191427_create_oficio_anexos_table.php');
        $migration->up();
        DB::table('presidios')->insert([['id' => 1, 'nome' => 'Presídio A'], ['id' => 2, 'nome' => 'Presídio B']]);
        DB::table('oficio_cops')->insert([
            ['id' => 1, 'presidio_id' => 1, 'data_oficio' => '2026-10-01', 'convidados' => '[1,2,3,4]'],
            ['id' => 2, 'presidio_id' => 2, 'data_oficio' => '2026-10-09', 'convidados' => '[2]'],
        ]);
        DB::table('oficio_eventos')->insert(['id' => 1, 'presidio_id' => 1, 'data_oficio' => '2026-10-09']);
        foreach (['esposas', 'convidados', 'comunicação', 'organização'] as $i => $classe) {
            DB::table('convidados')->insert(['id' => $i + 1, 'nome' => 'Pessoa ' . ($i + 1), 'cpf' => 'CPF-' . ($i + 1), 'classe' => $classe]);
            DB::table('unp_oficio_convidados')->insert(['oficio_tipo' => 'evento', 'oficio_id' => 1, 'convidado_id' => $i + 1]);
        }
    }

    public function test_carrega_quatro_categorias_e_salva_novo_anexo(): void
    {
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')
            ->assertHasNoErrors()->assertSet('esposas_selected', [1])
            ->assertSet('convidados_selected', [2])->assertSet('comunicacao_selected', [3])
            ->assertSet('organizacao_selected', [4])->assertSee('Selecionados: 1 esposas')
            ->call('store')->assertHasNoErrors()->assertSet('isOpen', false);
        $anexo = OficioAnexo::firstOrFail();
        $this->assertSame([1], $anexo->esposas);
        $this->assertSame([2], $anexo->convidados);
        $this->assertSame([3], $anexo->comunicacao);
        $this->assertSame([4], $anexo->organizacao);
        $this->assertStringContainsString('Presídio A', $anexo->titulo);
        $this->assertDatabaseCount('convidados', 4);
        $this->assertDatabaseCount('unp_oficio_convidados', 4);
    }

    public function test_oficios_seguem_ordem_e_filtro_da_tela_de_convidados(): void
    {
        $component = Livewire::test(Anexos::class)->call('create')->set('anexoPresidio', '1');
        $this->assertSame(['evento:1', 'cop:1'], array_column($component->instance()->opcoesOrigemAnexo()['oficios'], 'value'));
        $this->assertSame(1, preg_match(
            '/<select[^>]*id="anexo-oficio"[^>]*>(.*?)<\/select>/s',
            $component->html(),
            $select
        ));
        preg_match_all(
            '/<option[^>]*value="([^"]+)"[^>]*>(.*?)<\/option>/s',
            $select[1],
            $options,
            PREG_SET_ORDER
        );
        $this->assertSame(['evento:1', 'cop:1'], array_column($options, 1));
        $this->assertStringContainsString('09/10/2026', $options[0][2]);
        $this->assertStringContainsString('01/10/2026', $options[1][2]);
    }

    public function test_trocar_presidio_limpa_selecao_anterior(): void
    {
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')
            ->set('anexoPresidio', '2')->assertSet('anexoOficio', '')
            ->assertSet('esposas_selected', [])->assertSet('convidados_selected', [])
            ->assertSet('comunicacao_selected', [])->assertSet('organizacao_selected', [])
            ->assertSet('titulo', '');
    }

    public function test_oficio_de_outro_presidio_e_rejeitado(): void
    {
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'cop:2')
            ->assertHasErrors(['anexoOficio'])->assertSet('convidados_selected', []);
        $this->assertDatabaseCount('oficio_anexos', 0);
    }

    public function test_inclui_convidados_legados_do_cop_sem_duplicar(): void
    {
        DB::table('unp_oficio_convidados')->insert(['oficio_tipo' => 'cop', 'oficio_id' => 1, 'convidado_id' => 2]);
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'cop:1')
            ->assertSet('esposas_selected', [1])->assertSet('convidados_selected', [2])
            ->assertSet('comunicacao_selected', [3])->assertSet('organizacao_selected', [4]);
    }

    public function test_permite_revisar_selecao_e_preserva_titulo_manual(): void
    {
        Livewire::test(Anexos::class)->call('create')->set('titulo', 'Minha lista personalizada')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')
            ->assertSet('titulo', 'Minha lista personalizada')->set('comunicacao_selected', [])
            ->call('store')->assertHasNoErrors();
        $this->assertSame([], OficioAnexo::firstOrFail()->comunicacao);
        $this->assertSame('Minha lista personalizada', OficioAnexo::firstOrFail()->titulo);
    }

    public function test_editar_anexo_existente_preserva_lista_e_limpa_origem(): void
    {
        $anexo = OficioAnexo::create(['titulo' => 'Lista anterior', 'data' => '2026-10-01', 'esposas' => [], 'convidados' => [2], 'comunicacao' => [], 'organizacao' => []]);
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')
            ->call('edit', $anexo->id)->assertSet('anexoPresidio', '')->assertSet('anexoOficio', '')
            ->assertSet('esposas_selected', [])->assertSet('convidados_selected', [2])
            ->set('titulo', 'Lista anterior revisada')->call('store')->assertHasNoErrors();
        $this->assertSame([2], $anexo->fresh()->convidados);
        $this->assertDatabaseCount('oficio_anexos', 1);
    }

    public function test_rejeita_id_inexistente_sem_salvar_anexo(): void
    {
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')
            ->set('convidados_selected', [999])->call('store')
            ->assertHasErrors(['convidados_selected.0']);
        $this->assertDatabaseCount('oficio_anexos', 0);
    }

    public function test_anexo_salvo_e_uma_copia_independente_da_lista_do_oficio(): void
    {
        Livewire::test(Anexos::class)->call('create')
            ->set('anexoPresidio', '1')->set('anexoOficio', 'evento:1')->call('store')->assertHasNoErrors();
        DB::table('unp_oficio_convidados')->where('oficio_tipo', 'evento')->delete();
        $this->assertSame([2], OficioAnexo::firstOrFail()->convidados);
        $this->assertSame([3], OficioAnexo::firstOrFail()->comunicacao);
    }
}
