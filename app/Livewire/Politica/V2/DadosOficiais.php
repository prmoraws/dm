<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\FonteEstado;
use App\Models\Politica\V2\TseImportacao;
use App\Services\Politica\V2\PoliticaStorageService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DadosOficiais extends Component
{
    public function render()
    {
        $porAno = Candidatura::query()
            ->join('politica_eleicoes as e', 'e.id', '=', 'politica_candidaturas.eleicao_id')
            ->join('politica_cargos as c', 'c.id', '=', 'politica_candidaturas.cargo_id')
            ->where('politica_candidaturas.origem', 'tse_dados_abertos')
            ->select('e.ano', 'c.nome as cargo')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('e.ano', 'c.nome')
            ->orderByDesc('e.ano')
            ->orderBy('c.nome')
            ->get()
            ->groupBy('ano');

        $importacoes = TseImportacao::query()
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $fontes = FonteEstado::query()
            ->where('fonte', 'TSE_DADOS_ABERTOS')
            ->orderBy('chave')
            ->get();

        $metricas = [
            'candidaturas_oficiais' => Candidatura::query()->where('origem', 'tse_dados_abertos')->count(),
            'politicos_com_tse' => Candidatura::query()->where('origem', 'tse_dados_abertos')->distinct()->count('politico_id'),
            'municipios_com_codigo_tse' => DB::table('politica_cidades')->whereNotNull('tse_codigo')->count(),
            'importacoes_ok' => TseImportacao::query()->where('status', 'concluida')->count(),
        ];

        $storageService = app(PoliticaStorageService::class);
        $armazenamento = $storageService->snapshot();
        $armazenamento['politica_human'] = $storageService->human($armazenamento['politica_bytes']);
        $armazenamento['database_human'] = $storageService->human($armazenamento['database_bytes']);
        $armazenamento['warning_human'] = $storageService->human($armazenamento['warning_bytes']);
        $armazenamento['hard_limit_human'] = $storageService->human($armazenamento['hard_limit_bytes']);

        return view('livewire.politica.v2.dados-oficiais', compact('porAno', 'importacoes', 'fontes', 'metricas', 'armazenamento'));
    }
}
