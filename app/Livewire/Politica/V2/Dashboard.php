<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\PoliticaDashboardService;
use App\Services\Politica\V2\TseSyncRequestService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Política — Dashboard')]
class Dashboard extends Component
{
    public function solicitarAtualizacaoTse2026(): void
    {
        $resultado = app(TseSyncRequestService::class)->solicitar(
            2026,
            'BA',
            'espelho',
            'candidaturas',
            'manual',
            auth()->id(),
        );

        $mensagem = match ($resultado['reason']) {
            'already_pending' => 'Já existe uma atualização TSE pendente ou em execução.',
            'cooldown' => 'Uma atualização foi solicitada recentemente. Aguarde antes de solicitar novamente.',
            default => 'Atualização TSE solicitada. O scheduler processará a fila automaticamente.',
        };

        session()->flash('politica_tse_sync_message', $mensagem);
    }

    public function render(PoliticaDashboardService $service)
    {
        $dados = $service->resumo();
        $dados['tse_sync'] = app(TseSyncRequestService::class)->estado(2026, 'BA', 'candidaturas');

        return view('livewire.politica.v2.dashboard', $dados);
    }
}
