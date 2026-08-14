<?php

namespace Tests\Feature;

use App\Livewire\Politica\CityDashboard;
use App\Livewire\Politica\V2\AcompanhamentoPrioritario;
use App\Livewire\Politica\V2\Dashboard;
use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Livewire\Politica\V2\PoliticoShow;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaV2RotasTest extends TestCase
{
    #[Test]
    public function rotas_principais_apontam_para_componentes_v2_e_preservam_lista_de_cidades(): void
    {
        $this->assertSame(Dashboard::class, Route::getRoutes()->getByName('politica.dashboard')?->getActionName());
        $this->assertSame(AcompanhamentoPrioritario::class, Route::getRoutes()->getByName('politica.acompanhamento')?->getActionName());
        $this->assertSame(PoliticoShow::class, Route::getRoutes()->getByName('politica.politicos.show')?->getActionName());
        $this->assertSame(EspelhoInteligente::class, Route::getRoutes()->getByName('politica.espelho.inteligente')?->getActionName());
        $this->assertSame(CityDashboard::class, Route::getRoutes()->getByName('politica.cidades')?->getActionName());
    }
}
