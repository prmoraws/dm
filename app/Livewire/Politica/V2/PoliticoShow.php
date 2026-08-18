<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\EspelhoInteligenteService;
use App\Services\Politica\V2\PoliticaHistoricoOficialService;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Política — Perfil')]
class PoliticoShow extends Component
{
    public Politico $politico;

    #[Url(as: 'candidatura', except: null)]
    public ?int $candidaturaId = null;

    public function mount(Politico $politico): void
    {
        $this->politico = $politico;

        if ($this->candidaturaId === null) {
            $this->candidaturaId = Candidatura::query()
                ->where('politico_id', $politico->id)
                ->join('politica_eleicoes', 'politica_eleicoes.id', '=', 'politica_candidaturas.eleicao_id')
                ->orderByDesc('politica_eleicoes.ano')
                ->orderByDesc('politica_eleicoes.turno')
                ->orderByDesc('politica_candidaturas.id')
                ->value('politica_candidaturas.id');
        }
    }

    public function render(EspelhoInteligenteService $espelho, PoliticaHistoricoOficialService $historico)
    {
        $this->politico->load([
            'acompanhamento',
            'mandatos' => fn ($query) => $query->with(['cargo', 'partido'])->orderByDesc('ano_inicio')->orderByDesc('data_inicio'),
            'filiacoes' => fn ($query) => $query->with('partido')->orderByDesc('ano_inicio')->orderByDesc('data_inicio'),
            'candidaturas' => fn ($query) => $query
                ->with(['eleicao', 'cargo', 'partido'])
                ->withCount(['resultadosMunicipais', 'resultadosZonas'])
                ->orderByDesc('eleicao_id'),
        ]);

        $candidaturaSelecionada = null;
        $desempenho = null;

        if ($this->candidaturaId !== null) {
            $candidaturaSelecionada = Candidatura::query()
                ->with(['eleicao', 'cargo', 'partido', 'politico'])
                ->withCount(['resultadosMunicipais', 'resultadosZonas'])
                ->where('politico_id', $this->politico->id)
                ->find($this->candidaturaId);

            if ($candidaturaSelecionada && ($candidaturaSelecionada->resultados_municipais_count > 0 || $candidaturaSelecionada->resultados_zonas_count > 0)) {
                $desempenho = $espelho->desempenhoCandidatura($candidaturaSelecionada, 20);
            }
        }

        $candidaturaOficial2026 = $this->politico->candidaturas
            ->first(fn (Candidatura $candidatura) => (int) $candidatura->eleicao?->ano === 2026 && $candidatura->isRegistroOficialTse());

        $timezone2026 = (string) config('politica.tse.registro_2026.timezone', 'America/Bahia');
        $prazoRegistro2026 = CarbonImmutable::parse(
            (string) config('politica.tse.registro_2026.prazo', '2026-08-15 19:00:00'),
            $timezone2026
        );

        $contexto2026 = [
            'antes_prazo' => CarbonImmutable::now($timezone2026)->lt($prazoRegistro2026),
            'prazo' => $prazoRegistro2026,
        ];

        return view('livewire.politica.v2.politico-show', [
            'candidaturaSelecionada' => $candidaturaSelecionada,
            'candidaturaOficial2026' => $candidaturaOficial2026,
            'contexto2026' => $contexto2026,
            'desempenho' => $desempenho,
            'historicoEspecial' => $historico->isEspecial($this->politico),
            'linhaDoTempo' => $historico->linhaDoTempo($this->politico),
            'notaFiliacoes' => $historico->notaFiliacoes($this->politico),
        ]);
    }
}
