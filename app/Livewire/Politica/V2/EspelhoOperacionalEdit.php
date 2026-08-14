<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\EspelhoOperacional;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Política — Editar Espelho Operacional')]
class EspelhoOperacionalEdit extends Component
{
    public Cidade $cidade;

    public ?string $presidente_local = null;
    public ?string $indicacao_bispo = null;
    public ?int $filiados_republicanos = null;
    public ?string $observacoes = null;

    public function mount(Cidade $cidade): void
    {
        $this->cidade = $cidade;
        $espelho = EspelhoOperacional::query()->where('cidade_id', $cidade->id)->first();

        if ($espelho) {
            $this->presidente_local = $espelho->presidente_local;
            $this->indicacao_bispo = $espelho->indicacao_bispo;
            $this->filiados_republicanos = $espelho->filiados_republicanos;
            $this->observacoes = $espelho->observacoes;
        }
    }

    protected function rules(): array
    {
        return [
            'presidente_local' => ['nullable', 'string', 'max:255'],
            'indicacao_bispo' => ['nullable', 'string', 'max:255'],
            'filiados_republicanos' => ['nullable', 'integer', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        EspelhoOperacional::query()->updateOrCreate(
            ['cidade_id' => $this->cidade->id],
            $validated + ['revisado_em' => now()]
        );

        Cache::forget('politica:v2:dashboard');
        session()->flash('success', 'Espelho operacional atualizado com sucesso.');

        $this->redirectRoute('politica.espelho.inteligente', ['cidade' => $this->cidade->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.politica.v2.espelho-operacional-edit');
    }
}
