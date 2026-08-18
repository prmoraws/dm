<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\Eleicoes2026Service;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Política — Eleições 2026')]
class Eleicoes2026 extends Component
{
    public function render(Eleicoes2026Service $service)
    {
        return view('livewire.politica.v2.eleicoes-2026', $service->resumo());
    }
}
