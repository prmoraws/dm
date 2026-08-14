<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\V2\Acompanhamento;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Acompanhamento')]
class AcompanhamentoPrioritario extends Component
{
    #[Url(except: '')]
    public string $busca = '';

    #[Url(except: '')]
    public string $grupo = '';

    public function render()
    {
        $acompanhamentos = Acompanhamento::query()
            ->with([
                'politico.candidaturas' => fn ($query) => $query
                    ->with(['eleicao', 'cargo', 'partido'])
                    ->orderByDesc('eleicao_id'),
            ])
            ->where('ativo', true)
            ->when($this->grupo !== '', fn ($query) => $query->where('grupo', $this->grupo))
            ->when($this->busca !== '', function ($query) {
                $busca = '%'.trim($this->busca).'%';
                $query->whereHas('politico', fn ($politico) => $politico
                    ->where('nome_publico', 'like', $busca)
                    ->orWhere('nome_completo', 'like', $busca));
            })
            ->orderBy('ordem')
            ->get();

        $grupos = Acompanhamento::query()
            ->where('ativo', true)
            ->distinct()
            ->orderBy('grupo')
            ->pluck('grupo');

        return view('livewire.politica.v2.acompanhamento-prioritario', [
            'acompanhamentos' => $acompanhamentos,
            'grupos' => $grupos,
        ]);
    }
}
