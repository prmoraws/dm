<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\ComparativoTerritorialService;
use App\Services\Politica\V2\InteligenciaTerritorialService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Inteligência Territorial')]
class InteligenciaTerritorial extends Component
{
    #[Url(except: '')]
    public string $politico = '';

    #[Url(except: '')]
    public string $cargo = '';

    #[Url(as: 'base', except: '')]
    public string $anoBase = '';

    #[Url(as: 'comparada', except: '')]
    public string $anoComparada = '';

    #[Url(except: 'todos')]
    public string $sinal = 'todos';

    #[Url(except: '')]
    public string $busca = '';

    #[Url(except: 'relevancia_desc')]
    public string $ordem = 'relevancia_desc';

    public int $pagina = 1;

    public function mount(): void
    {
        $this->normalizar();
    }

    public function updatedPolitico(): void
    {
        $this->cargo = '';
        $this->anoBase = '';
        $this->anoComparada = '';
        $this->sinal = 'todos';
        $this->pagina = 1;
        $this->normalizar();
    }

    public function updatedCargo(): void
    {
        $this->anoBase = '';
        $this->anoComparada = '';
        $this->sinal = 'todos';
        $this->pagina = 1;
        $this->normalizar();
    }

    public function updatedAnoBase(): void
    {
        $this->pagina = 1;
        $this->normalizar();
    }

    public function updatedAnoComparada(): void
    {
        $this->pagina = 1;
        $this->normalizar();
    }

    public function updatedSinal(): void
    {
        $this->pagina = 1;
    }

    public function updatedBusca(): void
    {
        $this->pagina = 1;
    }

    public function updatedOrdem(): void
    {
        $this->pagina = 1;
    }

    public function paginaAnterior(): void
    {
        $this->pagina = max(1, $this->pagina - 1);
    }

    public function proximaPagina(int $paginas): void
    {
        $this->pagina = min(max(1, $paginas), $this->pagina + 1);
    }

    private function normalizar(): void
    {
        $service = app(ComparativoTerritorialService::class);
        $selecao = $service->normalizarSelecao($this->politico, $this->cargo, $this->anoBase, $this->anoComparada);
        $this->politico = $selecao['politico'];
        $this->cargo = $selecao['cargo'];
        $this->anoBase = $selecao['ano_base'];
        $this->anoComparada = $selecao['ano_comparada'];
    }

    public function render(InteligenciaTerritorialService $service)
    {
        return view('livewire.politica.v2.inteligencia-territorial', $service->painel(
            $this->politico,
            $this->cargo,
            $this->anoBase,
            $this->anoComparada,
            $this->sinal,
            $this->busca,
            $this->ordem,
            $this->pagina,
        ));
    }
}
