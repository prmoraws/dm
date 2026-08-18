<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\ApuracaoCandidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Eleicao;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class TseApuracaoCollectorService
{
    public function __construct(
        private readonly TseEa20Parser $parser,
        private readonly ApuracaoCheckpointService $checkpoints,
        private readonly ApuracaoSnapshotService $snapshots,
        private readonly Eleicoes2026Service $eleicoes2026,
    ) {}

    public function persistir(Eleicao $eleicao, Cargo $cargo, array $payload, string $abrangencia = 'BA'): array
    {
        $dados = $this->parser->parse($payload);
        $abrangencia = strtoupper($abrangencia);
        $tipo = $abrangencia === 'BR' ? 'br' : 'uf';
        $agora = now();

        return DB::transaction(function () use ($eleicao, $cargo, $dados, $abrangencia, $tipo, $agora): array {
            $apuracao = Apuracao::query()->updateOrCreate(
                [
                    'eleicao_id' => $eleicao->id,
                    'cargo_id' => $cargo->id,
                    'abrangencia_chave' => $abrangencia,
                ],
                [
                    'abrangencia_tipo' => $tipo,
                    'uf' => $abrangencia === 'BR' ? null : $abrangencia,
                    'tse_idg' => $dados['idg'],
                    'gerado_tse_em' => $this->parseTseDateTime($dados['gerado_em']),
                    'status' => $this->status($dados['andamento']),
                    'secoes_total' => $dados['secoes_total'],
                    'secoes_totalizadas' => $dados['secoes_totalizadas'],
                    'secoes_nao_totalizadas' => $dados['secoes_nao_totalizadas'],
                    'percentual_secoes' => $dados['percentual_secoes'],
                    'eleitores_total' => $dados['eleitores_total'],
                    'comparecimento' => $dados['comparecimento'],
                    'abstencoes' => $dados['abstencoes'],
                    'totalizacao_final' => ($dados['andamento'] ?? null) === 'f',
                    'sincronizado_em' => $agora,
                ],
            );

            $sq = collect($dados['candidatos'])->pluck('sq_candidato')->filter()->unique()->values();
            $candidaturas = Candidatura::query()
                ->where('eleicao_id', $eleicao->id)
                ->where('cargo_id', $cargo->id)
                ->whereIn('tse_sq_candidato', $sq)
                ->get()
                ->keyBy(fn (Candidatura $item) => (string) $item->tse_sq_candidato);

            $vistos = [];
            $naoVinculados = [];

            foreach ($dados['candidatos'] as $item) {
                $candidatura = $candidaturas->get((string) $item['sq_candidato']);

                if (! $candidatura) {
                    $naoVinculados[] = $item['sq_candidato'];
                    continue;
                }

                ApuracaoCandidatura::query()->updateOrCreate(
                    [
                        'apuracao_id' => $apuracao->id,
                        'candidatura_id' => $candidatura->id,
                    ],
                    [
                        'posicao' => $item['posicao'],
                        'votos' => $item['votos'],
                        'percentual' => $item['percentual'],
                        'situacao' => $item['situacao'],
                        'eleito' => $item['eleito'],
                        'sincronizado_em' => $agora,
                    ],
                );

                $candidatura->forceFill([
                    'votos_total' => $item['votos'],
                    'percentual_total' => $item['percentual'],
                    'eleito' => $item['eleito'],
                ])->save();

                $vistos[] = $candidatura->id;
            }

            if ($vistos !== []) {
                $apuracao->candidaturas()->whereNotIn('candidatura_id', $vistos)->delete();
            }

            $historico = $this->checkpoints->registrar($apuracao, $agora);
            $snapshot = $this->snapshots->write(
                $apuracao,
                (int) config('politica.apuracao.snapshot_top', 50),
            );

            $this->eleicoes2026->esquecerCache();

            return [
                'apuracao_id' => $apuracao->id,
                'cargo' => $cargo->nome,
                'abrangencia' => $abrangencia,
                'status' => $apuracao->status,
                'percentual_secoes' => $apuracao->percentual_secoes !== null ? (float) $apuracao->percentual_secoes : null,
                'candidatos_payload' => count($dados['candidatos']),
                'candidatos_vinculados' => count($vistos),
                'candidatos_nao_vinculados' => count($naoVinculados),
                'sq_nao_vinculados' => array_slice(array_values(array_unique($naoVinculados)), 0, 20),
                'checkpoints_gravados' => $historico,
                'snapshot' => $snapshot,
            ];
        });
    }

    public function analisar(array $payload): array
    {
        $dados = $this->parser->parse($payload);

        return [
            'idg' => $dados['idg'],
            'andamento' => $dados['andamento'],
            'percentual_secoes' => $dados['percentual_secoes'],
            'candidatos' => count($dados['candidatos']),
            'amostra' => array_slice($dados['candidatos'], 0, 10),
        ];
    }

    private function status(?string $andamento): string
    {
        return match ($andamento) {
            'p' => 'em_andamento',
            'f' => 'finalizada',
            default => 'aguardando',
        };
    }

    private function parseTseDateTime(?string $valor): ?CarbonImmutable
    {
        if (! $valor) {
            return null;
        }

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i'] as $formato) {
            try {
                return CarbonImmutable::createFromFormat($formato, $valor, 'America/Bahia');
            } catch (\Throwable) {
                // tenta o próximo formato
            }
        }

        return null;
    }
}
