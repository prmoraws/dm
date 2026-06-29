<?php

namespace App\Livewire\Universal;

use Livewire\Component;

class CaptacaoSucesso extends Component
{
    public function render()
    {
        return view('livewire.universal.captacao-sucesso')
            ->layout('layouts.guest'); // IMPORTANTE: Use o layout de visitante
    }
}
