<?php

namespace Tests\Feature;

use App\Livewire\Politica\CityDashboard;
use App\Livewire\Politica\V2\AcompanhamentoPrioritario;
use App\Livewire\Politica\V2\Dashboard;
use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Livewire\Politica\V2\PoliticoShow;
use App\Livewire\Politica\V2\MapaInterativo;
use App\Livewire\Politica\V2\EspelhoOperacionalEdit;
use App\Livewire\Politica\V2\DadosOficiais;
use App\Livewire\Politica\V2\Eleicoes2026;
use App\Livewire\Politica\V2\HistoricoAcompanhados;
use App\Livewire\Politica\V2\ComparativoTerritorial;
use App\Livewire\Politica\V2\InteligenciaTerritorial;
use App\Livewire\Politica\V2\PainelExecutivo;
use App\Livewire\Politica\V2\QualidadeDados;
use App\Http\Controllers\Politica\PainelExecutivoExportController;
use App\Http\Controllers\Politica\EspelhoCidadeExportController;
use App\Http\Controllers\Politica\PoliticaQualidadeDadosExportController;
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
        $this->assertSame(MapaInterativo::class, Route::getRoutes()->getByName('politica.mapa')?->getActionName());
        $this->assertSame(EspelhoOperacionalEdit::class, Route::getRoutes()->getByName('politica.espelho.edit')?->getActionName());
        $this->assertSame(DadosOficiais::class, Route::getRoutes()->getByName('politica.dados-oficiais')?->getActionName());
        $this->assertSame(Eleicoes2026::class, Route::getRoutes()->getByName('politica.eleicoes-2026')?->getActionName());
        $this->assertSame(HistoricoAcompanhados::class, Route::getRoutes()->getByName('politica.historico-acompanhados')?->getActionName());
        $this->assertSame(ComparativoTerritorial::class, Route::getRoutes()->getByName('politica.comparativo-territorial')?->getActionName());
        $this->assertSame(InteligenciaTerritorial::class, Route::getRoutes()->getByName('politica.inteligencia-territorial')?->getActionName());
        $this->assertSame(PainelExecutivo::class, Route::getRoutes()->getByName('politica.painel-executivo')?->getActionName());
        $this->assertSame(PainelExecutivoExportController::class.'@pdf', Route::getRoutes()->getByName('politica.painel-executivo.pdf')?->getActionName());
        $this->assertSame(PainelExecutivoExportController::class.'@excel', Route::getRoutes()->getByName('politica.painel-executivo.excel')?->getActionName());
        $this->assertSame(EspelhoCidadeExportController::class.'@pdf', Route::getRoutes()->getByName('politica.espelho.relatorio.pdf')?->getActionName());
        $this->assertSame(EspelhoCidadeExportController::class.'@excel', Route::getRoutes()->getByName('politica.espelho.relatorio.excel')?->getActionName());
        $this->assertSame(QualidadeDados::class, Route::getRoutes()->getByName('politica.qualidade-dados')?->getActionName());
        $this->assertSame(PoliticaQualidadeDadosExportController::class.'@pdf', Route::getRoutes()->getByName('politica.qualidade-dados.pdf')?->getActionName());
        $this->assertSame(PoliticaQualidadeDadosExportController::class.'@excel', Route::getRoutes()->getByName('politica.qualidade-dados.excel')?->getActionName());
    }
}
