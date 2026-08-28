<?php

namespace Tests\Feature;

use App\Livewire\Universal\CredenciadosDashboard;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CredenciadosDashboardTest extends TestCase
{
    #[Test]
    public function componente_view_e_rota_do_dashboard_existem(): void
    {
        $this->assertTrue(class_exists(CredenciadosDashboard::class));
        $this->assertFileExists(resource_path('views/livewire/universal/credenciados-dashboard.blade.php'));
        $this->assertSame(
            CredenciadosDashboard::class,
            app('router')->getRoutes()->getByName('universal.credenciados.dashboard')?->getActionName()
        );
    }

    #[Test]
    public function dashboard_cobre_os_indicadores_operacionais_reais(): void
    {
        $fonte = file_get_contents(app_path('Livewire/Universal/CredenciadosDashboard.php'));

        foreach (['com_credencial', 'sem_credencial', 'validas', 'vencendo', 'vencidas', 'sem_validade', 'unidade_nao_faz'] as $indicador) {
            $this->assertStringContainsString("'{$indicador}'", $fonte);
        }

        $this->assertStringContainsString("user->bloco_id != 21", $fonte);
        $this->assertStringContainsString("COUNT(DISTINCT credenciados.id)", $fonte);
    }

    #[Test]
    public function ciclo_da_credencial_tem_migration_incremental_e_campos_no_model(): void
    {
        $migration = database_path('migrations/2026_08_28_120000_add_lifecycle_dates_to_credencial_presidios_table.php');
        $model = file_get_contents(app_path('Models/Universal/CredencialPresidio.php'));

        $this->assertFileExists($migration);
        $this->assertStringContainsString('data_primeira_credencial', $model);
        $this->assertStringContainsString('data_renovacao', $model);
        $this->assertStringContainsString('data_vencimento', $model);
    }
}
