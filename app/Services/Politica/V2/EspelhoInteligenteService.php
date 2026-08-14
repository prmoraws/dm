<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\ResultadoMunicipal;
use Illuminate\Support\Collection;

class EspelhoInteligenteService
{
    /**
     * Resume o desempenho eleitoral agregado da cidade.
     * Quando cargoId é informado, evita comparar candidaturas de cargos diferentes.
     */
    public function resumoCidade(
        Cidade $cidade,
        ?int $eleicaoId = null,
        ?int $cargoId = null,
        int $limiteRanking = 50,
        ?string $partidoSigla = null
    ): array
    {
        $query = ResultadoMunicipal::query()
            ->with(['candidatura.politico', 'candidatura.partido', 'candidatura.cargo'])
            ->where('cidade_id', $cidade->id);

        if ($eleicaoId !== null) {
            $query->where('eleicao_id', $eleicaoId);
        }

        if ($cargoId !== null) {
            $query->whereHas('candidatura', fn ($q) => $q->where('cargo_id', $cargoId));
        }

        if ($partidoSigla !== null) {
            $partidoSigla = mb_strtoupper(trim($partidoSigla), 'UTF-8');
            $legacyIdsDoPartido = collect(config('politica.migracao_v1.partidos_legacy', []))
                ->filter(fn ($sigla) => mb_strtoupper((string) $sigla, 'UTF-8') === $partidoSigla)
                ->keys()
                ->map(fn ($id) => (int) $id)
                ->all();

            $query->where(function ($q) use ($partidoSigla, $legacyIdsDoPartido): void {
                $q->whereHas('candidatura.partido', fn ($partido) => $partido->where('sigla', $partidoSigla));

                if ($legacyIdsDoPartido !== []) {
                    $q->orWhereHas('candidatura', fn ($candidatura) => $candidatura->whereIn('legacy_candidato_id', $legacyIdsDoPartido));
                }
            });
        }

        $limiteRanking = max(2, min($limiteRanking, 100));
        $totalResultados = (clone $query)->count();
        $totalVotosCandidatos = (int) (clone $query)->sum('votos');

        /** @var Collection<int, ResultadoMunicipal> $resultados */
        $resultados = $query
            ->orderByDesc('votos')
            ->limit($limiteRanking)
            ->get();
        $lider = $resultados->first();
        $segundo = $resultados->skip(1)->first();
        $origens = $resultados
            ->map(fn (ResultadoMunicipal $resultado) => $resultado->candidatura?->origem ?? 'manual')
            ->filter()
            ->unique()
            ->values();

        return [
            'cidade_id' => $cidade->id,
            'cidade' => $cidade->nome,
            'eleicao_id' => $eleicaoId,
            'cargo_id' => $cargoId,
            'partido_filtro' => $partidoSigla,
            'total_votos_candidatos' => $totalVotosCandidatos,
            'lider' => $this->resultadoParaResumo($lider),
            'segundo' => $this->resultadoParaResumo($segundo),
            'diferenca_votos' => $lider && $segundo ? max(0, (int) $lider->votos - (int) $segundo->votos) : null,
            'ranking' => $resultados->map(fn (ResultadoMunicipal $resultado) => $this->resultadoParaResumo($resultado))->values()->all(),
            'ranking_total_resultados' => $totalResultados,
            'ranking_limitado' => $totalResultados > $limiteRanking,
            'qualidade' => [
                'origens' => $origens->all(),
                'possui_dados_legados' => $origens->contains('legacy_v1'),
                'percentuais_oficiais_completos' => $resultados->isNotEmpty()
                    && $resultados->every(fn (ResultadoMunicipal $resultado) => $resultado->percentual !== null),
            ],
        ];
    }

    /**
     * Espelho profissional: separa contexto operacional interno de dados eleitorais derivados.
     */
    public function panoramaCidade(
        Cidade $cidade,
        ?int $eleicaoId = null,
        ?int $cargoId = null,
        ?string $partidoSigla = null
    ): array
    {
        $cidade->loadMissing('espelhoOperacional');
        $operacional = $cidade->espelhoOperacional;
        $eleitoral = $this->resumoCidade($cidade, $eleicaoId, $cargoId, 50, $partidoSigla);

        return [
            'cidade' => [
                'id' => $cidade->id,
                'nome' => $cidade->nome,
                'ibge_code' => $cidade->ibge_code,
                'populacao' => $cidade->populacao !== null ? (int) $cidade->populacao : null,
                'cadeiras_camara' => $cidade->cadeiras_camara !== null ? (int) $cidade->cadeiras_camara : null,
                'latitude' => $cidade->latitude !== null ? (float) $cidade->latitude : null,
                'longitude' => $cidade->longitude !== null ? (float) $cidade->longitude : null,
            ],
            'operacional' => $operacional ? [
                'presidente_local' => $operacional->presidente_local,
                'indicacao_bispo' => $operacional->indicacao_bispo,
                'filiados_republicanos' => $operacional->filiados_republicanos,
                'observacoes' => $operacional->observacoes,
                'revisado_em' => $operacional->revisado_em?->toIso8601String(),
            ] : null,
            'eleitoral' => $eleitoral,
            'alertas' => $this->alertasDoPanorama($eleitoral, $operacional !== null),
        ];
    }

    /**
     * Retorna o desempenho territorial de uma candidatura, priorizando dados já consolidados no banco.
     */
    public function desempenhoCandidatura(Candidatura $candidatura, int $limite = 20): array
    {
        $candidatura->loadMissing(['politico', 'partido', 'cargo']);
        $limite = max(1, min($limite, 100));

        $base = ResultadoMunicipal::query()
            ->with('cidade')
            ->where('candidatura_id', $candidatura->id);

        $totalMunicipios = (clone $base)->count();
        $resultados = $base
            ->orderByDesc('votos')
            ->limit($limite)
            ->get();

        $totalVotos = (int) $candidatura->votos_total;
        $top5 = (int) ResultadoMunicipal::query()
            ->where('candidatura_id', $candidatura->id)
            ->orderByDesc('votos')
            ->limit(5)
            ->pluck('votos')
            ->sum();

        return [
            'candidatura_id' => $candidatura->id,
            'politico' => $candidatura->politico?->nome_publico,
            'cargo' => $candidatura->cargo?->nome,
            'partido' => $candidatura->partido?->sigla,
            'origem' => $candidatura->origem,
            'total_votos' => $totalVotos,
            'total_municipios_com_votos' => $totalMunicipios,
            'concentracao_top5_percentual' => $totalVotos > 0 ? round(($top5 / $totalVotos) * 100, 2) : null,
            'municipios' => $resultados->map(fn (ResultadoMunicipal $resultado) => [
                'cidade_id' => $resultado->cidade_id,
                'cidade' => $resultado->cidade?->nome,
                'votos' => (int) $resultado->votos,
                'percentual' => $resultado->percentual !== null ? (float) $resultado->percentual : null,
                'posicao' => $resultado->posicao,
                'participacao_nos_votos_do_candidato' => $totalVotos > 0
                    ? round(((int) $resultado->votos / $totalVotos) * 100, 2)
                    : null,
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
            'politico_slug' => $resultado->candidatura?->politico?->slug,
            'cargo' => $resultado->candidatura?->cargo?->nome,
            'partido' => $resultado->candidatura?->partido?->sigla,
            'origem' => $resultado->candidatura?->origem,
            'votos' => (int) $resultado->votos,
            'percentual' => $resultado->percentual !== null ? (float) $resultado->percentual : null,
            'posicao' => $resultado->posicao,
        ];
    }

    private function alertasDoPanorama(array $eleitoral, bool $possuiOperacional): array
    {
        $alertas = [];

        if (! $possuiOperacional) {
            $alertas[] = 'Espelho operacional ainda não revisado.';
        }

        if (($eleitoral['qualidade']['possui_dados_legados'] ?? false) === true) {
            $alertas[] = 'Há dados migrados da Política V1; serão substituídos/confirmados por fontes oficiais nas próximas sincronizações.';
        }

        if (($eleitoral['ranking'] ?? []) !== []
            && ($eleitoral['qualidade']['percentuais_oficiais_completos'] ?? false) === false) {
            $alertas[] = 'Percentuais oficiais indisponíveis para parte deste recorte; votos absolutos continuam válidos conforme a origem registrada.';
        }

        return $alertas;
    }
}
