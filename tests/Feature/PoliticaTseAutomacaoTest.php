<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\Dashboard;
use App\Models\Politica\V2\TseSolicitacao;
use App\Services\Politica\V2\TseSyncRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaTseAutomacaoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function botao_do_dashboard_enfileira_atualizacao_sem_executar_download_na_requisicao(): void
    {
        Livewire::test(Dashboard::class)
            ->assertSee('Atualizar TSE agora')
            ->call('solicitarAtualizacaoTse2026');

        $this->assertDatabaseHas('politica_tse_solicitacoes', [
            'ano' => 2026,
            'uf' => 'BA',
            'somente' => 'candidaturas',
            'origem' => 'manual',
            'status' => 'pendente',
        ]);

        $this->assertDatabaseCount('politica_tse_importacoes', 0);
    }

    #[Test]
    public function solicitacoes_duplicadas_pendentes_nao_sao_criadas(): void
    {
        $service = app(TseSyncRequestService::class);

        $primeira = $service->solicitar(2026, 'BA', 'espelho', 'candidaturas', 'manual');
        $segunda = $service->solicitar(2026, 'BA', 'espelho', 'candidaturas', 'manual');

        $this->assertTrue($primeira['created']);
        $this->assertFalse($segunda['created']);
        $this->assertSame('already_pending', $segunda['reason']);
        $this->assertSame(1, TseSolicitacao::query()->count());
    }

    #[Test]
    public function comando_de_fila_sem_pendencias_registra_heartbeat_do_scheduler(): void
    {
        Cache::forget(TseSyncRequestService::HEARTBEAT_KEY);

        $this->artisan('politica:tse-processar-solicitacoes', ['--limite' => 1])
            ->assertSuccessful();

        $this->assertNotNull(Cache::get(TseSyncRequestService::HEARTBEAT_KEY));
        $this->assertTrue(app(TseSyncRequestService::class)->estado()['scheduler_ativo']);
    }

    #[Test]
    public function comando_de_solicitacao_automatica_cria_pedido_persistente(): void
    {
        $this->artisan('politica:tse-solicitar-atualizacao', [
            'ano' => 2026,
            '--uf' => 'BA',
            '--escopo' => 'espelho',
            '--somente' => 'candidaturas',
            '--origem' => 'automatica',
        ])->assertSuccessful();

        $this->assertDatabaseHas('politica_tse_solicitacoes', [
            'ano' => 2026,
            'origem' => 'automatica',
            'status' => 'pendente',
        ]);
    }
}
