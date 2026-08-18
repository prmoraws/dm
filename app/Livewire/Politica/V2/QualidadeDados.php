<?php

namespace App\Livewire\Politica\V2;

use App\Services\Politica\V2\PoliticaQualidadeDadosService;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Qualidade e Auditoria')]
class QualidadeDados extends Component
{
    #[Url(except: 'todos')]
    public string $nivel = 'todos';

    #[Url(except: 'todos')]
    public string $grupo = 'todos';

    #[Url(except: '')]
    public string $busca = '';

    public function atualizarAuditoria(PoliticaQualidadeDadosService $service): void
    {
        $service->limparCache();
        session()->flash('politica_qualidade_ok', 'Auditoria recalculada a partir do estado atual da base.');
    }

    public function render(PoliticaQualidadeDadosService $service)
    {
        return view('livewire.politica.v2.qualidade-dados', $service->painel(
            $this->nivel,
            $this->grupo,
            $this->busca,
        ));
    }
}
