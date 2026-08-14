<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\ResultadoMunicipal;
use Illuminate\Support\Collection;

class EspelhoInteligenteService
{
    /**
     * Monta um resumo municipal usando apenas dados agregados.
     * Não cria perfil individual de eleitor e não duplica dados oficiais.
     */
    public function resumoCidade(Cidade $cidade, ?int $eleicaoId = null): array
    {
        $query = ResultadoMunicipal::query()
            ->with(['candidatura.politico', 'candidatura.partido'])
            ->where('cidade_id', $cidade->id);

        if ($eleicaoId !== null) {
            $query->where('eleicao_id', $eleicaoId);
        }

        /** @var Collection<int, ResultadoMunicipal> $resultados */
        $resultados = $query
            ->orderByDesc('votos')
            ->get();

        $totalVotosCandidatos = (int) $resultados->sum('votos');
        $lider = $resultados->first();
        $segundo = $resultados->skip(1)->first();

        return [
            'cidade_id' => $cidade->id,
            'cidade' => $cidade->nome,
            'eleicao_id' => $eleicaoId,
            'total_votos_candidatos' => $totalVotosCandidatos,
            'lider' => $this->resultadoParaResumo($lider),
            'segundo' => $this->resultadoParaResumo($segundo),
            'diferenca_votos' => $lider && $segundo ? max(0, (int) $lider->votos - (int) $segundo->votos) : null,
            'ranking' => $resultados->map(fn (ResultadoMunicipal $resultado) => $this->resultadoParaResumo($resultado))->values()->all(),
        ];
    }

    /**
     * Retorna o desempenho territorial de uma candidatura, priorizando dados já consolidados no banco.
     */
    public function desempenhoCandidatura(Candidatura $candidatura, int $limite = 20): array
    {
        $resultados = ResultadoMunicipal::query()
            ->with('cidade')
            ->where('candidatura_id', $candidatura->id)
            ->orderByDesc('votos')
            ->limit(max(1, min($limite, 100)))
            ->get();

        return [
            'candidatura_id' => $candidatura->id,
            'politico' => $candidatura->politico?->nome_publico,
            'total_votos' => (int) $candidatura->votos_total,
            'municipios' => $resultados->map(fn (ResultadoMunicipal $resultado) => [
                'cidade_id' => $resultado->cidade_id,
                'cidade' => $resultado->cidade?->nome,
                'votos' => (int) $resultado->votos,
                'percentual' => $resultado->percentual !== null ? (float) $resultado->percentual : null,
                'posicao' => $resultado->posicao,
            ])->all(),
        ];
    }

    private function resultadoParaResumo(?ResultadoMunicipal $resultado): ?array
    {
        if (! $resultado) {
            return null;
        }

        return [
            'candidatura_id' => $resultado->candidatura_id,
            'politico' => $resultado->candidatura?->politico?->nome_publico,
            'partido' => $resultado->candidatura?->partido?->sigla,
            'votos' => (int) $resultado->votos,
            'percentual' => $resultado->percentual !== null ? (float) $resultado->percentual : null,
            'posicao' => $resultado->posicao,
        ];
    }
}
