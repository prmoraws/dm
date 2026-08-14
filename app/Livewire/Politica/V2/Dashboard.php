<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\PoliticaDashboardService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Política — Dashboard')]
class Dashboard extends Component
{
    public function render(PoliticaDashboardService $service)
    {
        return view('livewire.politica.v2.dashboard', $service->resumo());
    }
}
