<?php

namespace App\Livewire\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\FonteEstado;
use App\Models\Politica\V2\TseImportacao;
use App\Services\Politica\V2\PoliticaStorageService;
use App\Services\Politica\V2\TseSyncRequestService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DadosOficiais extends Component
{
    public function solicitarAtualizacaoTse2026(): void
    {
        $resultado = app(TseSyncRequestService::class)->solicitar(
            2026,
            'BA',
            'espelho',
            'candidaturas',
            'manual',
            auth()->id(),
        );

        $mensagem = match ($resultado['reason']) {
            'already_pending' => 'Já existe uma atualização TSE pendente ou em execução.',
            'cooldown' => 'Uma atualização foi solicitada recentemente. Aguarde antes de solicitar novamente.',
            default => 'Atualização TSE solicitada. O scheduler processará a fila automaticamente.',
        };

        session()->flash('politica_tse_sync_message', $mensagem);
    }

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

        $candidaturas2026 = Candidatura::query()
            ->where('origem', 'tse_dados_abertos')
            ->whereHas('eleicao', fn ($query) => $query->where('ano', 2026))
            ->count();

        $ultimaSincronizacao2026 = Candidatura::query()
            ->where('origem', 'tse_dados_abertos')
            ->whereHas('eleicao', fn ($query) => $query->where('ano', 2026))
            ->max('sincronizado_em');

        $timezone2026 = (string) config('politica.tse.registro_2026.timezone', 'America/Bahia');
        $prazoRegistro2026 = CarbonImmutable::parse(
            (string) config('politica.tse.registro_2026.prazo', '2026-08-15 19:00:00'),
            $timezone2026
        );

        $registro2026 = [
            'total' => $candidaturas2026,
            'ultima_sincronizacao' => $ultimaSincronizacao2026 ? CarbonImmutable::parse($ultimaSincronizacao2026)->setTimezone($timezone2026) : null,
            'antes_prazo' => CarbonImmutable::now($timezone2026)->lt($prazoRegistro2026),
            'prazo' => $prazoRegistro2026,
        ];

        $tseSync = app(TseSyncRequestService::class)->estado(2026, 'BA', 'candidaturas');

        $storageService = app(PoliticaStorageService::class);
        $armazenamento = $storageService->snapshot();
        $armazenamento['politica_human'] = $storageService->human($armazenamento['politica_bytes']);
        $armazenamento['database_human'] = $storageService->human($armazenamento['database_bytes']);
        $armazenamento['warning_human'] = $storageService->human($armazenamento['warning_bytes']);
        $armazenamento['hard_limit_human'] = $storageService->human($armazenamento['hard_limit_bytes']);

        return view('livewire.politica.v2.dados-oficiais', compact('porAno', 'importacoes', 'fontes', 'metricas', 'armazenamento', 'registro2026', 'tseSync'));
    }
}
