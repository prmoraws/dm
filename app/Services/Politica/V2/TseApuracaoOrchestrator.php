<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\FonteEstado;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class TseApuracaoOrchestrator
{
    public function __construct(
        private readonly TseApuracaoUrlBuilder $urls,
        private readonly TseConditionalHttpClient $http,
        private readonly TseEa14Parser $ea14,
        private readonly TseApuracaoCollectorService $collector,
    ) {}

    public function status(int $ano = 2026, int $turno = 1, string $uf = 'BA'): array
    {
        $eleicao = $this->eleicao($ano, $turno);
        $cargos = $this->cargos();

        return [
            'live_enabled' => (bool) config('politica.apuracao.live_enabled', false),
            'schedule_enabled' => (bool) config('politica.apuracao.schedule_enabled', false),
            'poll_seconds' => (int) config('politica.apuracao.poll_seconds', 60),
            'max_requests_per_cycle' => (int) config('politica.apuracao.max_requests_per_cycle', 6),
            'eleicao_id' => $eleicao?->id,
            'tse_eleicao_codigo' => $eleicao?->tse_eleicao_codigo,
            'ea14_url' => $eleicao?->tse_eleicao_codigo ? $this->urls->ea14($eleicao) : null,
            'ea20' => $eleicao?->tse_eleicao_codigo
                ? $cargos->mapWithKeys(fn (Cargo $cargo) => [$cargo->nome => $this->urls->ea20($eleicao, $cargo, $uf)])->all()
                : [],
        ];
    }

    public function coletar(int $ano = 2026, int $turno = 1, string $uf = 'BA', bool $ignorarCooldown = false): array
    {
        if (! (bool) config('politica.apuracao.live_enabled', false)) {
            throw new RuntimeException('Coleta ao vivo desabilitada. Defina POLITICA_APURACAO_LIVE_ENABLED=true somente quando o TSE liberar o ambiente apropriado.');
        }

        $eleicao = $this->eleicao($ano, $turno);
        if (! $eleicao || ! $eleicao->tse_eleicao_codigo) {
            throw new RuntimeException("Eleição {$ano}/{$turno}º turno sem código TSE sincronizado.");
        }

        $lock = Cache::lock('politica:apuracao:coletor', max(30, (int) config('politica.apuracao.lock_seconds', 55)));

        try {
            return $lock->block(1, fn () => $this->coletarProtegido($eleicao, strtoupper($uf), $ignorarCooldown));
        } catch (LockTimeoutException) {
            return ['executado' => false, 'motivo' => 'outro_coletor_em_execucao', 'requests' => 0, 'atualizacoes' => []];
        }
    }

    private function coletarProtegido(Eleicao $eleicao, string $uf, bool $ignorarCooldown): array
    {
        $poll = max(60, (int) config('politica.apuracao.poll_seconds', 60));
        $ultimaExecucao = Cache::get('politica:apuracao:ultima_execucao');

        if (! $ignorarCooldown && $ultimaExecucao && now()->diffInSeconds($ultimaExecucao) < $poll) {
            return ['executado' => false, 'motivo' => 'cooldown', 'requests' => 0, 'atualizacoes' => []];
        }

        Cache::put('politica:apuracao:ultima_execucao', now(), $poll * 2);

        $maxRequests = max(1, min(6, (int) config('politica.apuracao.max_requests_per_cycle', 6)));
        $requests = 0;
        $atualizacoes = [];

        $fonteEa14 = FonteEstado::query()->firstOrCreate(
            ['chave' => "tse_apuracao:{$eleicao->ano}:{$eleicao->turno}:ea14"],
            [
                'fonte' => 'tse_resultados',
                'tipo_arquivo' => 'EA14',
                'url' => $this->urls->ea14($eleicao),
                'meta' => [],
            ],
        );
        if ($fonteEa14->url !== $this->urls->ea14($eleicao)) {
            $fonteEa14->update(['url' => $this->urls->ea14($eleicao)]);
        }

        $resposta = $this->http->fetch($fonteEa14);
        $requests++;
        $fonteEa14->refresh();
        $meta = is_array($fonteEa14->meta) ? $fonteEa14->meta : [];
        $agora = now();
        $grace = max(60, (int) config('politica.apuracao.recheck_grace_seconds', 180));

        if ($resposta['data']) {
            $acompanhamento = $this->ea14->parse($resposta['data'], $uf);
            $assinaturaBr = $this->assinatura($acompanhamento['br']);
            $assinaturaUf = $this->assinatura($acompanhamento['uf']);

            if ($assinaturaBr && $assinaturaBr !== ($meta['signal_br'] ?? null)) {
                $meta['signal_br'] = $assinaturaBr;
                $meta['pending_br_until'] = $agora->copy()->addSeconds($grace)->timestamp;
            }
            if ($assinaturaUf && $assinaturaUf !== ($meta['signal_uf'] ?? null)) {
                $meta['signal_uf'] = $assinaturaUf;
                $meta['pending_uf_until'] = $agora->copy()->addSeconds($grace)->timestamp;
            }

            $meta['ea14_idg'] = $acompanhamento['idg'];
            $meta['ea14_gerado_em'] = $acompanhamento['gerado_em'];
            $fonteEa14->forceFill(['idg' => $acompanhamento['idg'], 'meta' => $meta])->save();
        }

        $pendingBr = ((int) ($meta['pending_br_until'] ?? 0)) >= $agora->timestamp;
        $pendingUf = ((int) ($meta['pending_uf_until'] ?? 0)) >= $agora->timestamp;

        foreach ($this->cargos() as $cargo) {
            if ($requests >= $maxRequests) {
                break;
            }

            $abrangencia = $cargo->nome === 'Presidente' ? 'BR' : $uf;
            if ($abrangencia === 'BR' && ! $pendingBr) {
                continue;
            }
            if ($abrangencia !== 'BR' && ! $pendingUf) {
                continue;
            }

            $url = $this->urls->ea20($eleicao, $cargo, $uf);
            $fonte = FonteEstado::query()->firstOrCreate(
                ['chave' => "tse_apuracao:{$eleicao->ano}:{$eleicao->turno}:ea20:".strtolower(str_replace(' ', '_', $cargo->nome)).':'.strtolower($abrangencia)],
                [
                    'fonte' => 'tse_resultados',
                    'tipo_arquivo' => 'EA20',
                    'url' => $url,
                    'meta' => ['cargo' => $cargo->nome, 'abrangencia' => $abrangencia],
                ],
            );
            if ($fonte->url !== $url) {
                $fonte->update(['url' => $url]);
            }

            $resultado = $this->http->fetch($fonte);
            $requests++;

            if (! $resultado['changed'] || ! is_array($resultado['data'])) {
                continue;
            }

            $atualizacoes[] = $this->collector->persistir($eleicao, $cargo, $resultado['data'], $abrangencia);
        }

        return [
            'executado' => true,
            'motivo' => null,
            'requests' => $requests,
            'ea14_status' => $resposta['status'],
            'pending_br' => $pendingBr,
            'pending_uf' => $pendingUf,
            'atualizacoes' => $atualizacoes,
        ];
    }

    private function eleicao(int $ano, int $turno): ?Eleicao
    {
        return Eleicao::query()
            ->where('ano', $ano)
            ->where('turno', $turno)
            ->whereNotNull('tse_eleicao_codigo')
            ->orderByDesc('id')
            ->first();
    }

    private function cargos()
    {
        return Cargo::query()
            ->whereIn('nome', ['Presidente', 'Governador', 'Senador', 'Deputado Federal', 'Deputado Estadual'])
            ->orderBy('ordem')
            ->get();
    }

    private function assinatura(?array $item): ?string
    {
        if (! $item) {
            return null;
        }

        return hash('sha256', json_encode([
            $item['andamento'] ?? null,
            $item['ultima_totalizacao'] ?? null,
            $item['secoes_totalizadas'] ?? null,
            $item['secoes_nao_totalizadas'] ?? null,
        ]));
    }
}
