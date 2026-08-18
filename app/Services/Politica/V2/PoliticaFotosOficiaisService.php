<?php

namespace App\Services\Politica\V2;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\FonteEstado;
use App\Models\Politica\V2\Politico;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use ZipArchive;

class PoliticaFotosOficiaisService
{
    public function sincronizar(int $ano = 2026, bool $force = false, ?string $slug = null): array
    {
        if (! (bool) config('politica.tse.photos.enabled', true)) {
            return ['atualizadas' => 0, 'inalteradas' => 0, 'ausentes' => 0, 'fontes' => [], 'itens' => []];
        }

        $alvos = $this->alvos($ano, $slug);
        $resumo = ['atualizadas' => 0, 'inalteradas' => 0, 'ausentes' => 0, 'fontes' => [], 'itens' => []];

        foreach (['BR', 'BA'] as $abrangencia) {
            $grupo = $alvos->where('abrangencia', $abrangencia)->values();
            if ($grupo->isEmpty()) {
                continue;
            }

            // Se o TSE não mudou (304), mas o arquivo local sumiu, precisamos baixar de novo.
            $forcarFonte = $force || $grupo->contains(fn (array $alvo) => ! $this->arquivoLocalValido($alvo['slug']));
            $download = $this->baixar($ano, $abrangencia, $forcarFonte);
            $resumo['fontes'][$abrangencia] = $download['status'];

            if ($download['status'] === 304) {
                $reconciliado = $this->reconciliarGrupoLocal($grupo, $ano, $abrangencia);
                $resumo['atualizadas'] += $reconciliado['atualizadas'];
                $resumo['ausentes'] += $reconciliado['ausentes'];
                $resumo['inalteradas'] += max(0, $grupo->count() - $reconciliado['atualizadas'] - $reconciliado['ausentes']);
                $resumo['itens'] = array_merge($resumo['itens'], $reconciliado['itens']);
                continue;
            }

            $resultado = $this->importarArquivo($download['path'], $ano, $abrangencia, $slug);
            $resumo['atualizadas'] += $resultado['atualizadas'];
            $resumo['ausentes'] += $resultado['ausentes'];
            $resumo['itens'] = array_merge($resumo['itens'], $resultado['itens']);
            @unlink($download['path']);
        }

        return $resumo;
    }

    public function importarArquivo(string $zipPath, int $ano, string $abrangencia, ?string $slug = null): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('A extensão PHP ZipArchive é necessária para importar as fotos oficiais do TSE.');
        }

        $alvos = $this->alvos($ano, $slug)->where('abrangencia', strtoupper($abrangencia))->values();
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException("Não foi possível abrir o ZIP de fotos do TSE: {$zipPath}");
        }

        $destino = $this->diretorioDestino();
        if (! is_dir($destino) && ! mkdir($destino, 0775, true) && ! is_dir($destino)) {
            $zip->close();
            throw new RuntimeException("Não foi possível criar o diretório {$destino}.");
        }

        $atualizadas = 0;
        $ausentes = 0;
        $itens = [];

        foreach ($alvos as $alvo) {
            $indice = $this->localizarFoto($zip, $alvo['sq']);
            if ($indice === null) {
                $ausentes++;
                $itens[] = $this->itemResumo($alvo, 'não encontrada', null, null);
                continue;
            }

            $bytes = $zip->getFromIndex($indice);
            if (! $this->jpegValido($bytes)) {
                $ausentes++;
                $itens[] = $this->itemResumo($alvo, 'JPEG inválido', null, is_string($bytes) ? strlen($bytes) : null);
                continue;
            }

            $arquivo = $destino.'/'.$alvo['slug'].'.jpg';
            $tmp = $arquivo.'.part';
            if (file_put_contents($tmp, $bytes, LOCK_EX) === false || ! rename($tmp, $arquivo)) {
                @unlink($tmp);
                $zip->close();
                throw new RuntimeException("Não foi possível gravar a foto oficial de {$alvo['nome']}.");
            }

            $fotoUrl = $this->persistirVinculo($alvo, $ano, strtoupper($abrangencia), $bytes);
            $atualizadas++;
            $itens[] = $this->itemResumo($alvo, 'atualizada', $fotoUrl, strlen($bytes));
        }

        $zip->close();

        return ['atualizadas' => $atualizadas, 'ausentes' => $ausentes, 'itens' => $itens];
    }

    private function reconciliarGrupoLocal(Collection $grupo, int $ano, string $abrangencia): array
    {
        $atualizadas = 0;
        $ausentes = 0;
        $itens = [];

        foreach ($grupo as $alvo) {
            $arquivo = $this->diretorioDestino().'/'.$alvo['slug'].'.jpg';
            $bytes = is_file($arquivo) ? file_get_contents($arquivo) : false;
            if (! $this->jpegValido($bytes)) {
                $ausentes++;
                $itens[] = $this->itemResumo($alvo, 'arquivo local ausente', null, null);
                continue;
            }

            $fotoUrl = $this->persistirVinculo($alvo, $ano, strtoupper($abrangencia), $bytes);
            $atualizadas++;
            $itens[] = $this->itemResumo($alvo, 'reconciliada', $fotoUrl, strlen($bytes));
        }

        return compact('atualizadas', 'ausentes', 'itens');
    }

    private function persistirVinculo(array $alvo, int $ano, string $abrangencia, string $bytes): string
    {
        $destinoRelativo = $this->destinoRelativo();
        $hash = hash('sha256', $bytes);
        $fotoUrl = '/'.$destinoRelativo.'/'.$alvo['slug'].'.jpg?v='.substr($hash, 0, 12);

        $politico = Politico::query()->find($alvo['politico_id']);
        if ($politico) {
            $links = is_array($politico->links) ? $politico->links : [];
            $links['foto_oficial'] = [
                'fonte' => 'TSE - Dados Abertos',
                'ano' => $ano,
                'abrangencia' => $abrangencia,
                'sq_candidato' => $alvo['sq'],
                'licenca' => 'Creative Commons Attribution',
                'url' => $this->url($ano, $abrangencia),
                'arquivo' => '/'.$destinoRelativo.'/'.$alvo['slug'].'.jpg',
                'sha256' => $hash,
                'sincronizado_em' => now()->toIso8601String(),
            ];
            $politico->update([
                'foto_url' => $fotoUrl,
                'links' => $links,
            ]);
        }

        // Redundância intencional: páginas centradas na candidatura também recebem a foto.
        Candidatura::query()
            ->where('politico_id', $alvo['politico_id'])
            ->where('tse_sq_candidato', $alvo['sq'])
            ->whereHas('eleicao', fn ($query) => $query->where('ano', $ano))
            ->update(['foto_url' => $fotoUrl]);

        return $fotoUrl;
    }

    private function alvos(int $ano, ?string $slug = null): Collection
    {
        $slugs = array_keys((array) config('politica.tse.prioritarios_aliases', []));
        $slug = trim((string) $slug);
        if ($slug !== '') {
            $slugs = array_values(array_filter($slugs, fn (string $item) => $item === $slug));
        }

        return Politico::query()
            ->whereIn('slug', $slugs)
            ->with(['candidaturas' => fn ($query) => $query
                ->whereNotNull('tse_sq_candidato')
                ->whereHas('eleicao', fn ($q) => $q->where('ano', $ano))
                ->with(['eleicao', 'cargo'])])
            ->get()
            ->map(function (Politico $politico) {
                $candidatura = $politico->candidaturas->first();
                if (! $candidatura) {
                    return null;
                }

                return [
                    'politico_id' => $politico->id,
                    'slug' => $politico->slug,
                    'nome' => $politico->nome_publico,
                    'sq' => (string) $candidatura->tse_sq_candidato,
                    'abrangencia' => $candidatura->cargo?->nome === 'Presidente' ? 'BR' : strtoupper((string) ($candidatura->uf ?: 'BA')),
                ];
            })
            ->filter(fn ($item) => $item && in_array($item['abrangencia'], ['BR', 'BA'], true))
            ->values();
    }

    private function localizarFoto(ZipArchive $zip, string $sq): ?int
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nome = (string) $zip->getNameIndex($i);
            if (! preg_match('/\.(jpe?g)$/i', $nome)) {
                continue;
            }
            if (str_contains(basename($nome), $sq)) {
                return $i;
            }
        }

        return null;
    }

    private function jpegValido(mixed $bytes): bool
    {
        return is_string($bytes)
            && strlen($bytes) >= 256
            && str_starts_with($bytes, "\xFF\xD8\xFF");
    }

    private function arquivoLocalValido(string $slug): bool
    {
        $arquivo = $this->diretorioDestino().'/'.$slug.'.jpg';
        if (! is_file($arquivo) || filesize($arquivo) < 256) {
            return false;
        }

        $handle = fopen($arquivo, 'rb');
        if (! is_resource($handle)) {
            return false;
        }
        $magic = fread($handle, 3);
        fclose($handle);

        return $magic === "\xFF\xD8\xFF";
    }

    private function itemResumo(array $alvo, string $status, ?string $url, ?int $bytes): array
    {
        return [
            'slug' => $alvo['slug'],
            'nome' => $alvo['nome'],
            'sq' => $alvo['sq'],
            'abrangencia' => $alvo['abrangencia'],
            'status' => $status,
            'url' => $url,
            'bytes' => $bytes,
        ];
    }

    private function baixar(int $ano, string $abrangencia, bool $force): array
    {
        $url = $this->url($ano, $abrangencia);
        $fonte = FonteEstado::query()->firstOrCreate(
            ['chave' => "tse_dados_abertos:fotos:{$ano}:{$abrangencia}"],
            ['fonte' => 'TSE_DADOS_ABERTOS', 'tipo_arquivo' => 'fotos_candidatos', 'url' => $url]
        );
        $fonte->url = $url;

        $headers = ['User-Agent' => 'DOMO-Politica/2.0'];
        if (! $force) {
            if ($fonte->etag) $headers['If-None-Match'] = $fonte->etag;
            if ($fonte->last_modified) $headers['If-Modified-Since'] = $fonte->last_modified;
        }

        $dir = storage_path("app/private/politica/tse/{$ano}");
        if (! is_dir($dir)) mkdir($dir, 0775, true);
        $tmp = $dir."/fotos_{$abrangencia}_{$ano}.zip.part";

        $response = Http::withHeaders($headers)
            ->connectTimeout((int) config('politica.tse.connect_timeout', 10))
            ->timeout((int) config('politica.tse.download_timeout', 300))
            ->retry(2, 750, throw: false)
            ->withOptions(['sink' => $tmp])
            ->get($url);

        $fonte->ultimo_check_em = now();
        $fonte->http_status = $response->status();

        if ($response->status() === 304) {
            @unlink($tmp);
            $fonte->fill(['ultimo_sucesso_em' => now(), 'erros_consecutivos' => 0, 'ultimo_erro' => null])->save();
            return ['status' => 304, 'path' => null];
        }

        if (! $response->successful() || ! is_file($tmp) || filesize($tmp) === 0) {
            @unlink($tmp);
            $fonte->erros_consecutivos = ((int) $fonte->erros_consecutivos) + 1;
            $fonte->ultimo_erro = "HTTP {$response->status()}";
            $fonte->save();
            throw new RuntimeException("Falha ao baixar fotos oficiais {$abrangencia}/{$ano} do TSE: HTTP {$response->status()}.");
        }

        $fonte->fill([
            'etag' => $response->header('ETag') ?: $fonte->etag,
            'last_modified' => $response->header('Last-Modified') ?: $fonte->last_modified,
            'ultimo_sucesso_em' => now(),
            'ultima_alteracao_em' => now(),
            'erros_consecutivos' => 0,
            'ultimo_erro' => null,
        ])->save();

        return ['status' => $response->status(), 'path' => $tmp];
    }

    private function diretorioDestino(): string
    {
        return public_path($this->destinoRelativo());
    }

    private function destinoRelativo(): string
    {
        return trim((string) config('politica.tse.photos.public_path', 'images/politica/oficiais'), '/');
    }

    private function url(int $ano, string $abrangencia): string
    {
        $template = (string) config('politica.tse.photos.url_template');
        return sprintf($template, $ano, $ano, strtoupper($abrangencia));
    }
}
