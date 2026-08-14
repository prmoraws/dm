<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\ApuracaoHistorico;
use Carbon\CarbonInterface;

class ApuracaoCheckpointService
{
    /**
     * Guarda histórico apenas das candidaturas acompanhadas e somente quando
     * houver mudança após o intervalo configurado. Isso limita o crescimento do banco.
     */
    public function registrar(Apuracao $apuracao, ?CarbonInterface $agora = null): int
    {
        $agora ??= now();
        $intervalo = max(30, (int) config('politica.apuracao.historico_checkpoint_seconds', 60));
        $gravados = 0;

        $atuais = $apuracao->candidaturas()
            ->with('candidatura.politico.acompanhamento')
            ->whereHas('candidatura.politico.acompanhamento', fn ($query) => $query->where('ativo', true))
            ->get();

        foreach ($atuais as $atual) {
            $ultimo = ApuracaoHistorico::query()
                ->where('apuracao_id', $apuracao->id)
                ->where('candidatura_id', $atual->candidatura_id)
                ->latest('capturado_em')
                ->first();

            if ($ultimo && $ultimo->capturado_em->diffInSeconds($agora) < $intervalo) {
                continue;
            }

            if ($ultimo
                && (int) $ultimo->votos === (int) $atual->votos
                && (string) $ultimo->percentual === (string) $atual->percentual
                && (string) $ultimo->percentual_secoes === (string) $apuracao->percentual_secoes) {
                continue;
            }

            ApuracaoHistorico::query()->create([
                'apuracao_id' => $apuracao->id,
                'candidatura_id' => $atual->candidatura_id,
                'capturado_em' => $agora,
                'votos' => $atual->votos,
                'percentual' => $atual->percentual,
                'percentual_secoes' => $apuracao->percentual_secoes,
            ]);
            $gravados++;
        }

        return $gravados;
    }
}
