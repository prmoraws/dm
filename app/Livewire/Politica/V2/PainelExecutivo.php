<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\PainelExecutivoService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Painel Executivo')]
class PainelExecutivo extends Component
{
    #[Url(except: 'todos')]
    public string $politico = 'todos';

    public function updatedPolitico(): void
    {
        $this->politico = app(PainelExecutivoService::class)->normalizarPolitico($this->politico);
    }

    public function render(PainelExecutivoService $service)
    {
        return view('livewire.politica.v2.painel-executivo', $service->painel($this->politico));
    }
}
