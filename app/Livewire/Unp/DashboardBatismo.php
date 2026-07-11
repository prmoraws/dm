<?php

namespace App\Livewire\Unp;

use App\Models\Unp\Batismo;
use App\Models\Universal\Bloco;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Livewire\Component;

class DashboardBatismo extends Component
{
    public $searchBloco = '';
    
    // Propriedades para os cards informativos
    public $totalAno = 0;
    public $totalMes = 0;
    public $totalDia = 0;

    public function render()
    {
        $hoje = Carbon::today();

        // 1. Cálculos dos totais gerais do Estado (Soma da coluna quantidade)
        $this->totalAno = Batismo::whereYear('data_batismo', $hoje->year)->sum('quantidade');
        $this->totalMes = Batismo::whereYear('data_batismo', $hoje->year)
            ->whereMonth('data_batismo', $hoje->month)
            ->sum('quantidade');
        $this->totalDia = Batismo::whereDate('data_batismo', $hoje)->sum('quantidade');

        // 2. Filtro de blocos na barra de pesquisa
        $blocosMapeados = Bloco::query()
            ->when($this->searchBloco, function($query) {
                $query->where('nome', 'like', '%' . $this->searchBloco . '%');
            })
            ->orderBy('nome', 'asc')
            ->get()
            ->map(function($bloco) use ($hoje) {
                // Conta os batismos do bloco dentro do mês atual
                $bloco->total_mes_atual = Batismo::where('bloco_id', $bloco->id)
                    ->whereYear('data_batismo', $hoje->year)
                    ->whereMonth('data_batismo', $hoje->month)
                    ->sum('quantidade');
                return $bloco;
            });

        // 3. Carrega a tabela de quantidade no mês atual agrupada por Presídio
        $presidiosTabela = Presidio::query()
            ->orderBy('nome', 'asc')
            ->get()
            ->map(function($presidio) use ($hoje) {
                $presidio->total_mes_atual = Batismo::where('presidio_id', $presidio->id)
                    ->whereYear('data_batismo', $hoje->year)
                    ->whereMonth('data_batismo', $hoje->month)
                    ->sum('quantidade');
                return $presidio;
            })
            // Opcional: Se quiser listar apenas presídios que tiveram batismo no mês, descomente a linha abaixo:
            // ->filter(fn($p) => $p->total_mes_atual > 0)
            ;

        return view('livewire.unp.dashboard-batismo', [
            'blocosMapeados' => $blocosMapeados,
            'presidiosTabela' => $presidiosTabela,
        ]);
    }
}