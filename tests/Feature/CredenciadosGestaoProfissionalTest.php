<?php

namespace Tests\Feature;

use App\Livewire\Universal\Credenciados;
use App\Models\User;
use App\Models\Universal\Bloco;
use App\Policies\CredenciadoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CredenciadosGestaoProfissionalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function central_possui_filtros_ordenacao_e_paginacao_persistentes(): void
    {
        $fonte = file_get_contents(app_path('Livewire/Universal/Credenciados.php'));

        foreach (['filtro_cargo', 'filtro_categoria', 'filtro_presidio', 'perPage', 'sortField', 'sortDirection'] as $recurso) {
            $this->assertStringContainsString($recurso, $fonte);
        }

        $this->assertStringContainsString('sortBy(', $fonte);
        $this->assertStringContainsString('paginate($perPage)', $fonte);
    }

    #[Test]
    public function gravacao_e_exclusao_usam_transacao_e_autorizacao(): void
    {
        $fonte = file_get_contents(app_path('Livewire/Universal/Credenciados.php'));

        $this->assertStringContainsString('DB::transaction', $fonte);
        $this->assertStringContainsString("authorize(\$existente ? 'update' : 'create'", $fonte);
        $this->assertStringNotContainsString('Ocorreu um erro inesperado: ', $fonte);
    }

    #[Test]
    public function policy_cobre_listagem_criacao_visualizacao_edicao_e_exclusao(): void
    {
        foreach (['viewAny', 'create', 'view', 'update', 'delete'] as $metodo) {
            $this->assertTrue(method_exists(CredenciadoPolicy::class, $metodo));
        }
    }

    #[Test]
    public function componente_profissional_continua_disponivel(): void
    {
        $this->assertTrue(class_exists(Credenciados::class));
        $this->assertFileExists(resource_path('views/livewire/universal/credenciados.blade.php'));
    }

    #[Test]
    public function tela_livewire_e_compilada_e_renderizada_sem_erro(): void
    {
        $bloco = Bloco::create(['nome' => 'Bloco Teste']);
        $usuario = User::create([
            'name' => 'Administrador Teste',
            'email' => 'credenciados-teste@example.com',
            'password' => 'password',
            'bloco_id' => $bloco->id,
        ]);

        Livewire::actingAs($usuario)
            ->test(Credenciados::class)
            ->assertOk()
            ->assertSee('Novo Credenciado');
    }
}
