<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\EspelhoOperacional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaTerritorioOperacionalReconcileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reconcilia_muquem_sem_apagar_auxiliar_e_sem_sobrescrever_dados(): void
    {
        $oficial = Cidade::query()->create([
            'nome' => 'MUQUÉM DO SÃO FRANCISCO',
            'ibge_code' => 2922300,
            'latitude' => -12.62,
            'longitude' => -43.54,
        ]);

        $auxiliar = Cidade::query()->create([
            'nome' => 'MUQUÉM DE SÃO FRANCISCO',
        ]);

        $operacional = EspelhoOperacional::query()->create([
            'cidade_id' => $auxiliar->id,
            'legacy_espelho_id' => 268,
            'presidente_local' => 'Presidente legado',
            'indicacao_bispo' => 'Indicação legado',
            'filiados_republicanos' => 29,
            'revisado_em' => now()->subYear(),
        ]);

        $inteligencia = EspelhoInteligencia::query()->create([
            'cidade_id' => $auxiliar->id,
            'classificacao' => 'acompanhamento',
            'prioridade' => 2,
            'observacoes' => 'Contexto preservado',
        ]);

        $codigoDryRun = Artisan::call('politica:reconciliar-territorio-operacional');

        $this->assertSame(0, $codigoDryRun);
        $this->assertSame($auxiliar->id, $operacional->fresh()->cidade_id);
        $this->assertSame($auxiliar->id, $inteligencia->fresh()->cidade_id);

        $codigoAplicar = Artisan::call('politica:reconciliar-territorio-operacional', [
            '--aplicar' => true,
        ]);

        $this->assertSame(0, $codigoAplicar);
        $this->assertSame($oficial->id, $operacional->fresh()->cidade_id);
        $this->assertSame($oficial->id, $inteligencia->fresh()->cidade_id);
        $this->assertDatabaseHas('politica_espelho_operacional', [
            'id' => $operacional->id,
            'cidade_id' => $oficial->id,
            'legacy_espelho_id' => 268,
            'presidente_local' => 'Presidente legado',
        ]);
        $this->assertDatabaseHas('politica_espelho_inteligencia', [
            'id' => $inteligencia->id,
            'cidade_id' => $oficial->id,
            'contexto_chave' => 'geral',
            'observacoes' => 'Contexto preservado',
        ]);
        $this->assertDatabaseHas('politica_cidades', [
            'id' => $auxiliar->id,
            'nome' => 'MUQUÉM DE SÃO FRANCISCO',
            'ibge_code' => null,
        ]);
    }
}
