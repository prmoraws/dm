<?php

namespace App\Livewire\Unp\Oficios\Concerns;

use App\Models\Unp\Presidio;
use App\Services\Unp\ConvidadosEmMassa;
use Illuminate\Support\Facades\Schema;

trait ImportaConvidadosEmMassa
{
    public string $massaPresidio = '';
    public string $massaOficio = '';
    public string $massaTexto = '';
    public string $massaClasse = 'convidados';
    #[\Livewire\Attributes\Locked]
    public array $massaPrevia = [];

    public function updatedMassaPresidio(): void
    {
        $this->massaOficio = '';
        $this->massaPrevia = [];
        $this->resetErrorBag();
    }

    public function updatedMassaOficio(): void { $this->massaPrevia = []; $this->resetErrorBag(); }
    public function updatedMassaTexto(): void { $this->massaPrevia = []; }
    public function updatedMassaClasse(): void { $this->massaPrevia = []; }

    private function validaDestinoMassa(): void
    {
        $this->validate([
            'massaPresidio' => 'required|integer|exists:presidios,id',
            'massaOficio' => 'required|string|max:80',
        ], [
            'massaPresidio.required' => 'Selecione o presídio.',
            'massaOficio.required' => 'Selecione o ofício.',
        ]);
    }

    public function revisarMassa(): void
    {
        $this->massaPrevia = [];
        $this->validaDestinoMassa();
        $this->massaPrevia = app(ConvidadosEmMassa::class)->previa((int) $this->massaPresidio, $this->massaOficio, $this->massaTexto, $this->massaClasse);
    }

    public function salvarMassa(): void
    {
        $this->validaDestinoMassa();
        if (!$this->massaPrevia) {
            $this->addError('massaTexto', 'Revise a lista antes de salvar.');
            return;
        }
        $service = app(ConvidadosEmMassa::class);
        $previaAtual = $service->previa((int) $this->massaPresidio, $this->massaOficio, $this->massaTexto, $this->massaClasse);
        if ($previaAtual !== $this->massaPrevia) {
            $this->massaPrevia = $previaAtual;
            $this->addError('massaTexto', 'A lista ou os cadastros mudaram. Confira a prévia e salve novamente.');
            return;
        }
        $result = $service->salvar((int) $this->massaPresidio, $this->massaOficio, $this->massaTexto, $this->massaClasse);
        $this->massaTexto = '';
        $this->massaPrevia = [];
        $this->resetPage();
        session()->flash('message', $result['novos'] . ' novos cadastros e ' . $result['vinculados'] . ' novos vínculos com o ofício.');
    }

    public function dadosMassa(): array
    {
        $service = app(ConvidadosEmMassa::class);
        $disponivel = Schema::hasTable('unp_oficio_convidados');
        $oficios = $this->massaPresidio ? $service->oficios((int) $this->massaPresidio) : [];
        $valid = in_array($this->massaOficio, array_column($oficios, 'value'), true);
        return [
            'disponivel' => $disponivel,
            'presidios' => Presidio::orderBy('nome')->get(['id', 'nome']),
            'oficios' => $oficios,
            'convidados' => $disponivel && $valid ? $service->convidados((int) $this->massaPresidio, $this->massaOficio) : collect(),
        ];
    }
}
