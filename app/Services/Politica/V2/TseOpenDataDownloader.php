<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\FonteEstado;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TseOpenDataDownloader
{
    public function download(string $tipo, int $ano, bool $force = false): array
    {
        $url = $this->url($tipo, $ano);
        $disk = Storage::disk(config('politica.tse.disk', 'local'));
        $relative = trim((string) config('politica.tse.path', 'politica/tse'), '/')."/{$ano}/{$tipo}_{$ano}.zip";
        $absolute = $disk->path($relative);
        $tmp = $absolute.'.part';

        if (! is_dir(dirname($absolute))) {
            mkdir(dirname($absolute), 0775, true);
        }

        $fonte = FonteEstado::query()->firstOrCreate(
            ['chave' => "tse_dados_abertos:{$tipo}:{$ano}"],
            ['fonte' => 'TSE_DADOS_ABERTOS', 'tipo_arquivo' => $tipo, 'url' => $url]
        );
        $fonte->url = $url;
        $fonte->save();

        $headers = ['User-Agent' => 'DOMO-Politica/2.0'];
        if (! $force && is_file($absolute)) {
            if ($fonte->etag) {
                $headers['If-None-Match'] = $fonte->etag;
            }
            if ($fonte->last_modified) {
                $headers['If-Modified-Since'] = $fonte->last_modified;
            }
        }

        $response = Http::withHeaders($headers)
            ->connectTimeout((int) config('politica.tse.connect_timeout', 10))
            ->timeout((int) config('politica.tse.download_timeout', 300))
            ->retry(2, 750, throw: false)
            ->withOptions(['sink' => $tmp])
            ->get($url);

        $fonte->ultimo_check_em = now();
        $fonte->http_status = $response->status();

        if ($response->status() === 304 && is_file($absolute)) {
            @unlink($tmp);
            $fonte->erros_consecutivos = 0;
            $fonte->ultimo_erro = null;
            $fonte->ultimo_sucesso_em = now();
            $fonte->save();

            return ['path' => $absolute, 'changed' => false, 'status' => 304, 'url' => $url];
        }

        if (! $response->successful()) {
            @unlink($tmp);
            $fonte->erros_consecutivos = ((int) $fonte->erros_consecutivos) + 1;
            $fonte->ultimo_erro = "HTTP {$response->status()}";
            $fonte->save();
            throw new RuntimeException("Falha ao baixar {$tipo} {$ano} do TSE: HTTP {$response->status()}.");
        }

        if (! is_file($tmp) || filesize($tmp) === 0) {
            @unlink($tmp);
            throw new RuntimeException('O TSE respondeu sem conteúdo de arquivo utilizável.');
        }

        $hash = hash_file('sha256', $tmp);
        $previousHash = is_file($absolute) ? hash_file('sha256', $absolute) : null;
        $changed = $previousHash !== $hash;

        if ($changed || ! is_file($absolute)) {
            if (is_file($absolute)) {
                @unlink($absolute);
            }
            if (! rename($tmp, $absolute)) {
                throw new RuntimeException('Não foi possível concluir a gravação atômica do arquivo TSE.');
            }
        } else {
            @unlink($tmp);
        }

        $meta = is_array($fonte->meta) ? $fonte->meta : [];
        $fonte->fill([
            'etag' => $response->header('ETag') ?: $fonte->etag,
            'last_modified' => $response->header('Last-Modified') ?: $fonte->last_modified,
            'ultimo_sucesso_em' => now(),
            'ultima_alteracao_em' => $changed ? now() : $fonte->ultima_alteracao_em,
            'erros_consecutivos' => 0,
            'ultimo_erro' => null,
            'meta' => array_merge($meta, [
                'sha256' => $hash,
                'bytes' => filesize($absolute),
                'arquivo' => $relative,
            ]),
        ])->save();

        return ['path' => $absolute, 'changed' => $changed, 'status' => $response->status(), 'url' => $url];
    }


    public function cleanup(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        $disk = Storage::disk(config('politica.tse.disk', 'local'));
        $root = $disk->path(trim((string) config('politica.tse.path', 'politica/tse'), '/'));
        $realRoot = realpath($root) ?: $root;
        $realPath = realpath($path);

        if ($realPath === false) {
            return false;
        }

        $normalizedRoot = rtrim($realRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        if (! str_starts_with($realPath, $normalizedRoot)) {
            return false;
        }

        return @unlink($realPath);
    }

    public function cleanupCache(?int $ano = null): array
    {
        $disk = Storage::disk(config('politica.tse.disk', 'local'));
        $base = trim((string) config('politica.tse.path', 'politica/tse'), '/');
        $directory = $ano ? $base.'/'.$ano : $base;
        $files = 0;
        $bytes = 0;

        if (! $disk->exists($directory)) {
            return ['files' => 0, 'bytes' => 0];
        }

        foreach ($disk->allFiles($directory) as $relative) {
            $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
            if (! in_array($extension, ['zip', 'csv', 'part'], true) && ! str_ends_with(strtolower($relative), '.zip.part')) {
                continue;
            }

            $size = 0;
            try {
                $size = (int) $disk->size($relative);
            } catch (\Throwable) {
                // limpeza continua mesmo sem metadado de tamanho
            }

            if ($disk->delete($relative)) {
                $files++;
                $bytes += $size;
            }
        }

        return ['files' => $files, 'bytes' => $bytes];
    }

    public function url(string $tipo, int $ano): string
    {
        $template = config("politica.tse.sources.{$tipo}");
        if (! is_string($template) || $template === '') {
            throw new RuntimeException("Fonte TSE não configurada para o tipo {$tipo}.");
        }

        return sprintf($template, $ano, $ano);
    }
}
