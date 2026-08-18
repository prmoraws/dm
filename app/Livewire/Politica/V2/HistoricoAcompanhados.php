<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\HistoricoAcompanhadosService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Histórico dos Acompanhados')]
class HistoricoAcompanhados extends Component
{
    #[Url(except: '')]
    public string $politico = '';

    #[Url(except: '')]
    public string $cargo = '';

    #[Url(except: '')]
    public string $ano = '';

    public function limparFiltros(): void
    {
        $this->reset(['politico', 'cargo', 'ano']);
    }

    public function render(HistoricoAcompanhadosService $service)
    {
        $ano = ctype_digit($this->ano) ? (int) $this->ano : null;

        return view('livewire.politica.v2.historico-acompanhados', $service->resumo(
            trim($this->politico),
            trim($this->cargo),
            $ano,
        ));
    }
}
