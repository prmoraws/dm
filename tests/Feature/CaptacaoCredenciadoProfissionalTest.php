<?php

namespace Tests\Feature;

use App\Livewire\Universal\CaptacaoCredenciadoWizard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptacaoCredenciadoProfissionalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pagina_publica_de_captacao_renderiza_a_primeira_etapa(): void
    {
        $this->get(route('captacao.credenciado'))
            ->assertOk()
            ->assertSee('Cadastro de credenciado')
            ->assertSee('Vínculo com a igreja');
    }

    #[Test]
    public function wizard_exige_campos_organizacionais_antes_de_avancar(): void
    {
        Livewire::test(CaptacaoCredenciadoWizard::class)
            ->call('nextStep')
            ->assertHasErrors(['bloco_id', 'regiao_id', 'igreja_id', 'categoria_id', 'cargo_id'])
            ->assertSet('currentStep', 1);
    }

    #[Test]
    public function captacao_preserva_ciclo_da_credencial_e_privacidade(): void
    {
        $componente = file_get_contents(app_path('Livewire/Universal/CaptacaoCredenciadoWizard.php'));
        $view = file_get_contents(resource_path('views/livewire/universal/captacao-credenciado-wizard.blade.php'));

        foreach (['data_primeira_credencial', 'data_renovacao', 'data_vencimento', 'unidade_nao_faz'] as $campo) {
            $this->assertStringContainsString($campo, $componente);
            $this->assertStringContainsString($campo, $view);
        }

        $this->assertStringContainsString('DB::transaction', $componente);
        $this->assertStringContainsString("Storage::disk('public_disk')->delete", $componente);
        $this->assertStringContainsString("'aceitePrivacidade' => ['accepted']", $componente);
        $this->assertStringNotContainsString("'Erro ao salvar: ' .", $componente);
    }

    #[Test]
    public function dashboard_tem_filtro_por_presidio_e_alertas_do_mes(): void
    {
        $componente = file_get_contents(app_path('Livewire/Universal/CredenciadosDashboard.php'));
        $view = file_get_contents(resource_path('views/livewire/universal/credenciados-dashboard.blade.php'));

        $this->assertStringContainsString('presidioId', $componente);
        $this->assertStringContainsString('endOfMonth', $componente);
        $this->assertStringContainsString('Credenciais vencidas', $view);
        $this->assertStringContainsString('Vencem ainda neste mês', $view);
    }
}

