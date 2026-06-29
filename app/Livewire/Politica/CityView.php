<?php

namespace App\Livewire\Politica;

use App\Models\Politica\Cidade;
use App\Models\Politica\VotacaoDetalhada;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
class CityView extends Component
{
    public Cidade $cidade;
    
    // Arrays separados para cada tipo de estatística
    public array $federalDeputiesStats = [];
    public array $stateDeputiesStats = [];
    public array $councilorsStats = [];

    #[Title('Espelho Político')]
    public function mount(Cidade $cidade)
    {
        $this->cidade = $cidade;
        $this->cidade->load('espelho', 'candidatosFavoritos');
        $this->title = 'Espelho: ' . $this->cidade->nome;

        // Calcula as estatísticas para cada cargo
        $this->federalDeputiesStats = $this->calculateVotingStats('DEPUTADO FEDERAL');
        $this->stateDeputiesStats = $this->calculateVotingStats('DEPUTADO ESTADUAL');
        $this->councilorsStats = $this->calculateVotingStats('VEREADOR');
    }

    private function calculateVotingStats(string $cargo): array
    {
        $stats = [];
        
        // Filtra apenas os candidatos favoritos que concorreram ao cargo especificado
        $candidatesForCargo = $this->cidade->candidatosFavoritos->filter(function ($candidato) use ($cargo) {
            return $candidato->votacoes()->where('cargo', $cargo)->exists();
        });

        foreach ($candidatesForCargo as $candidato) {
            $details = VotacaoDetalhada::with('localVotacao')
                ->select(
                    'local_votacao_id',
                    DB::raw('SUM(votos_recebidos) as total_votos')
                )
                ->where('candidato_id', $candidato->id)
                ->where('cargo', $cargo) // Garante que estamos pegando votos do cargo correto
                ->whereHas('localVotacao', fn ($q) => $q->where('cidade_id', $this->cidade->id))
                ->groupBy('local_votacao_id')
                ->orderBy('total_votos', 'desc')
                ->get();

            if ($details->isNotEmpty()) {
                $stats[] = [
                    'name' => $candidato->nome,
                    'total_votes' => $details->sum('total_votos'),
                    'details' => $details,
                ];
            }
        }
        return $stats;
    }

    public function clearEspelho()
    {
        // Pega o espelho a partir da relação com a cidade
        $espelho = $this->cidade->espelho;

        // Verifica se existe um espelho para esta cidade antes de tentar atualizá-lo
        if ($espelho) {
            // Apaga os campos manuais
            $espelho->update([
                'presidente_local' => null,
                'indicacao_bispo' => null,
                'filiados_republicanos' => null,
                'observacoes' => null,
            ]);
        }

        // Remove a associação com todos os candidatos favoritos
        $this->cidade->candidatosFavoritos()->sync([]);

        session()->flash('success', 'Os dados manuais do espelho foram limpos com sucesso!');
        
        // Recarrega a página atual
        return $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.politica.city-view');
    }
}