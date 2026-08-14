<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\ApuracaoCandidatura;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApuracaoSnapshotService
{
    /**
     * Gera um snapshot pequeno: top N do cargo + todos os acompanhados prioritários.
     */
    public function build(Apuracao $apuracao, int $top = 50): array
    {
        $top = max(5, min($top, 100));

        $base = ApuracaoCandidatura::query()
            ->with(['candidatura.politico.acompanhamento', 'candidatura.partido'])
            ->where('apuracao_id', $apuracao->id);

        $topIds = (clone $base)
            ->orderByRaw('CASE WHEN posicao IS NULL THEN 1 ELSE 0 END')
            ->orderBy('posicao')
            ->orderByDesc('votos')
            ->limit($top)
            ->pluck('id');

        $prioritariosIds = (clone $base)
            ->whereHas('candidatura.politico.acompanhamento', fn ($query) => $query->where('ativo', true))
            ->pluck('id');

        $ids = $topIds->merge($prioritariosIds)->unique()->values();

        $candidatos = (clone $base)
            ->whereIn('id', $ids)
            ->orderByRaw('CASE WHEN posicao IS NULL THEN 1 ELSE 0 END')
            ->orderBy('posicao')
            ->orderByDesc('votos')
            ->get()
            ->map(fn (ApuracaoCandidatura $item) => [
                'candidatura_id' => $item->candidatura_id,
                'nome' => $item->candidatura?->politico?->nome_publico,
                'numero' => $item->candidatura?->numero_urna,
                'partido' => $item->candidatura?->partido?->sigla,
                'posicao' => $item->posicao,
                'votos' => (int) $item->votos,
                'percentual' => $item->percentual !== null ? (float) $item->percentual : null,
                'situacao' => $item->situacao,
                'eleito' => (bool) $item->eleito,
                'prioritario' => (bool) $item->candidatura?->politico?->acompanhamento?->ativo,
            ])->values()->all();

        return [
            'versao' => 1,
            'apuracao_id' => $apuracao->id,
            'eleicao_id' => $apuracao->eleicao_id,
            'cargo_id' => $apuracao->cargo_id,
            'abrangencia' => [
                'tipo' => $apuracao->abrangencia_tipo,
                'chave' => $apuracao->abrangencia_chave,
                'uf' => $apuracao->uf,
                'cidade_id' => $apuracao->cidade_id,
                'zona_id' => $apuracao->zona_id,
            ],
            'status' => $apuracao->status,
            'totalizacao_final' => (bool) $apuracao->totalizacao_final,
            'secoes' => [
                'total' => $apuracao->secoes_total,
                'totalizadas' => $apuracao->secoes_totalizadas,
                'percentual' => $apuracao->percentual_secoes !== null ? (float) $apuracao->percentual_secoes : null,
            ],
            'eleitorado' => [
                'total' => $apuracao->eleitores_total,
                'comparecimento' => $apuracao->comparecimento,
                'abstencoes' => $apuracao->abstencoes,
            ],
            'gerado_tse_em' => optional($apuracao->gerado_tse_em)->toIso8601String(),
            'sincronizado_em' => optional($apuracao->sincronizado_em)->toIso8601String(),
            'candidatos' => $candidatos,
        ];
    }

    public function write(Apuracao $apuracao, int $top = 50): string
    {
        $snapshot = $this->build($apuracao, $top);
        $disk = config('politica.apuracao.snapshot_disk', 'local');
        $basePath = trim(config('politica.apuracao.snapshot_path', 'politica/apuracao'), '/');
        $scope = Str::slug($apuracao->abrangencia_chave);
        $path = "{$basePath}/{$apuracao->eleicao_id}-{$apuracao->cargo_id}-{$scope}.json";

        Storage::disk($disk)->put(
            $path,
            json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        return $path;
    }
}
