<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\FonteEstado;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TseConditionalHttpClient
{
    /**
     * Busca um JSON usando validação condicional HTTP para reduzir tráfego.
     * A URL é mantida em politica_fontes_estado para permitir alterar endpoints sem deploy.
     */
    public function fetch(FonteEstado $fonte): array
    {
        if (! $fonte->url) {
            throw new RuntimeException("A fonte {$fonte->chave} não possui URL configurada.");
        }

        $request = $this->request();

        if ($fonte->etag) {
            $request = $request->withHeader('If-None-Match', $fonte->etag);
        }

        if ($fonte->last_modified) {
            $request = $request->withHeader('If-Modified-Since', $fonte->last_modified);
        }

        $response = $request->get($fonte->url);
        $agora = now();

        $fonte->forceFill([
            'ultimo_check_em' => $agora,
            'http_status' => $response->status(),
        ]);

        if ($response->status() === 304) {
            $fonte->forceFill([
                'erros_consecutivos' => 0,
                'ultimo_erro' => null,
                'ultimo_sucesso_em' => $agora,
            ])->save();

            return [
                'changed' => false,
                'not_modified' => true,
                'status' => 304,
                'data' => null,
            ];
        }

        if (! $response->successful()) {
            $fonte->forceFill([
                'erros_consecutivos' => ((int) $fonte->erros_consecutivos) + 1,
                'ultimo_erro' => "HTTP {$response->status()}",
            ])->save();

            return [
                'changed' => false,
                'not_modified' => false,
                'status' => $response->status(),
                'data' => null,
            ];
        }

        $data = $response->json();
        if (! is_array($data)) {
            $fonte->forceFill([
                'erros_consecutivos' => ((int) $fonte->erros_consecutivos) + 1,
                'ultimo_erro' => 'Resposta recebida não é um JSON válido.',
            ])->save();

            throw new RuntimeException("Resposta inválida da fonte {$fonte->chave}.");
        }

        $metaAnterior = is_array($fonte->meta) ? $fonte->meta : [];
        $hash = hash('sha256', $response->body());
        $changed = ($metaAnterior['content_hash'] ?? null) !== $hash;

        $fonte->forceFill([
            'etag' => $response->header('ETag') ?: $fonte->etag,
            'last_modified' => $response->header('Last-Modified') ?: $fonte->last_modified,
            'ultimo_sucesso_em' => $agora,
            'ultima_alteracao_em' => $changed ? $agora : $fonte->ultima_alteracao_em,
            'erros_consecutivos' => 0,
            'ultimo_erro' => null,
            'meta' => array_merge($metaAnterior, ['content_hash' => $hash]),
        ])->save();

        return [
            'changed' => $changed,
            'not_modified' => false,
            'status' => $response->status(),
            'data' => $data,
        ];
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->withHeaders([
                'User-Agent' => 'DOMO-Politica/2.0',
            ])
            ->connectTimeout((int) config('politica.apuracao.connect_timeout', 3))
            ->timeout((int) config('politica.apuracao.request_timeout', 8))
            ->retry(2, 250, throw: false);
    }
}
