<?php

namespace App\Livewire\Politica;

use App\Models\Politica\Cidade;
use App\Models\Politica\Candidato;
use App\Models\Politica\Espelho;
use App\Models\Politica\VotacaoDetalhada;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Editar Espelho Político')]
class EspelhoManager extends Component
{
    public Cidade $cidade;
    public ?Espelho $espelho;

    // Campos do formulário
    public ?string $presidente_local = null;
    public ?string $indicacao_bispo = null;
    public ?int $filiados_republicanos = null;
    public ?string $prefeito_atual_nome = null;
    public ?string $prefeito_atual_partido = null;
    public ?int $prefeito_atual_votos = null;
    public ?string $observacoes = null;
    
    // Listas de candidatos
    public $deputadosFederaisDaCidade = [];
    public $deputadosEstaduaisDaCidade = [];
    public $vereadoresDaCidade = [];

    // Variáveis separadas para cada caixa de seleção
    public array $selectedFederais = [];
    public array $selectedEstaduais = [];
    public array $selectedVereadores = [];

    // Propriedade para o nosso painel de debug
    public array $debugInfo = [];

    public function mount(Cidade $cidade)
    {
        $this->cidade = $cidade;
        $this->espelho = $cidade->espelho()->firstOrNew();
        $this->fill($this->espelho->toArray());
        
        $this->loadCandidatosDaCidade();

        $favoritoIds = $cidade->candidatosFavoritos()->pluck('candidato_id');
        $this->selectedFederais = $this->deputadosFederaisDaCidade->pluck('id')->intersect($favoritoIds)->values()->toArray();
        $this->selectedEstaduais = $this->deputadosEstaduaisDaCidade->pluck('id')->intersect($favoritoIds)->values()->toArray();
        $this->selectedVereadores = $this->vereadoresDaCidade->pluck('id')->intersect($favoritoIds)->values()->toArray();
    }

    public function loadCandidatosDaCidade()
    {
        $this->deputadosFederaisDaCidade = $this->getCandidatosPorCargo('DEPUTADO FEDERAL');
        $this->deputadosEstaduaisDaCidade = $this->getCandidatosPorCargo('DEPUTADO ESTADUAL');
        $this->vereadoresDaCidade = $this->getCandidatosPorCargo('VEREADOR');

        // Preenche nossa variável de debug com os resultados da busca
        $this->debugInfo['Cidade Sendo Editada (ID)'] = $this->cidade->id;
        $this->debugInfo['Dep. Federais Encontrados na Cidade'] = $this->deputadosFederaisDaCidade->count();
        $this->debugInfo['Dep. Estaduais Encontrados na Cidade'] = $this->deputadosEstaduaisDaCidade->count();
        $this->debugInfo['Vereadores Encontrados na Cidade'] = $this->vereadoresDaCidade->count();
    }

    private function getCandidatosPorCargo(string $cargo)
    {
        $candidatoIds = VotacaoDetalhada::query()
            ->join('politica_locais_votacao', 'politica_votacao_detalhada.local_votacao_id', '=', 'politica_locais_votacao.id')
            ->where('politica_votacao_detalhada.cargo', $cargo)
            ->where('politica_locais_votacao.cidade_id', $this->cidade->id)
            ->distinct()
            ->pluck('politica_votacao_detalhada.candidato_id');

        return Candidato::whereIn('id', $candidatoIds)->orderBy('nome')->get();
    }

    public function save()
    {
        $validated = $this->validate([
            'presidente_local' => 'nullable|string|max:255',
            'indicacao_bispo' => 'nullable|string|max:255',
            'filiados_republicanos' => 'nullable|integer',
            'observacoes' => 'nullable|string',
        ]);

        $this->cidade->espelho()->updateOrCreate(['cidade_id' => $this->cidade->id], $validated);
        
        $allSelectedIds = array_merge($this->selectedFederais, $this->selectedEstaduais, $this->selectedVereadores);
        $this->cidade->candidatosFavoritos()->sync(array_unique($allSelectedIds));

        session()->flash('success', 'Espelho político atualizado com sucesso!');
        return $this->redirectRoute('politica.cidade.view', ['cidade' => $this->cidade->id]);
    }

    public function render()
    {
        return view('livewire.politica.espelho-manager');
    }
}