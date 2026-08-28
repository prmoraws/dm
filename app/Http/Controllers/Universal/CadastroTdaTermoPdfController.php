<?php

namespace App\Http\Controllers\Universal;

use App\Http\Controllers\Controller;
use App\Models\Universal\CadastroTda;
use App\Models\Universal\TdaTermoAceite;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CadastroTdaTermoPdfController extends Controller
{
    use AuthorizesRequests;

    public function visualizarTodos(CadastroTda $cadastroTda)
    {
        return $this->responder($cadastroTda, null, false);
    }

    public function baixarTodos(CadastroTda $cadastroTda)
    {
        return $this->responder($cadastroTda, null, true);
    }

    public function visualizar(CadastroTda $cadastroTda, string $tipo)
    {
        return $this->responder($cadastroTda, $tipo, false);
    }

    public function baixar(CadastroTda $cadastroTda, string $tipo)
    {
        return $this->responder($cadastroTda, $tipo, true);
    }

    private function responder(CadastroTda $cadastroTda, ?string $tipo, bool $baixar)
    {
        $this->authorize('view', $cadastroTda);

        $tipos = array_keys(config('tda.termos', []));
        abort_if($tipo !== null && ! in_array($tipo, $tipos, true), 404);

        $cadastroTda->load([
            'bloco', 'regiao', 'igreja', 'estado', 'cidade',
            'responsavelLegal.estado', 'responsavelLegal.cidade',
        ]);

        $query = $cadastroTda->termosAceitos()
            ->whereNull('revogado_em');

        if ($tipo !== null) $query->where('tipo', $tipo);
        $aceites = $query->get()
            ->sortBy(fn (TdaTermoAceite $aceite) => array_search($aceite->tipo, $tipos, true))
            ->values();

        abort_if($aceites->isEmpty(), 404, 'Nenhum termo aceito foi encontrado para este cadastro.');

        $documentos = $this->prepararDocumentos($aceites);
        [$assinaturaVoluntario, $hashAssinatura] = $this->arquivoDiscoComHash($cadastroTda->assinatura);
        abort_if(! $assinaturaVoluntario, 422, 'A assinatura do voluntário não foi encontrada.');
        abort_if(
            $aceites->contains(fn (TdaTermoAceite $aceite) => ! hash_equals($aceite->hash_assinatura, $hashAssinatura)),
            409,
            'A assinatura armazenada não corresponde ao registro original dos termos.'
        );
        $assinaturaPastor = $this->arquivoPublicoDataUri(config('tda.pastor_responsavel.assinatura'));
        $aceiteAdesao = $aceites->firstWhere('tipo', TdaTermoAceite::ADESAO);
        $assinaturaTestemunha = null;
        if ($aceiteAdesao) {
            [$assinaturaTestemunha, $hashAssinaturaTestemunha] = $this->arquivoDiscoComHash($cadastroTda->testemunha_assinatura);
            abort_if(! $assinaturaTestemunha, 422, 'A assinatura da testemunha não foi encontrada.');
            $hashTestemunhaRegistrado = data_get($aceiteAdesao->dados_snapshot, 'testemunha.hash_assinatura');
            abort_if(
                ! $hashTestemunhaRegistrado || ! hash_equals($hashTestemunhaRegistrado, $hashAssinaturaTestemunha),
                409,
                'A assinatura da testemunha não corresponde ao registro original.'
            );
        }

        $pdf = Pdf::loadView('livewire.universal.pdf.termos-tda', [
            'pessoa' => $cadastroTda,
            'documentos' => $documentos,
            'assinaturaVoluntario' => $assinaturaVoluntario,
            'assinaturaPastor' => $assinaturaPastor,
            'assinaturaTestemunha' => $assinaturaTestemunha,
            'pastor' => config('tda.pastor_responsavel'),
        ])->setPaper('a4', 'portrait');

        $sufixo = $tipo ? Str::slug(config("tda.termos.{$tipo}.titulo")) : 'todos-os-termos';
        $nome = 'tda-'.Str::slug($cadastroTda->nome).'-'.$sufixo.'.pdf';

        return $baixar ? $pdf->download($nome) : $pdf->stream($nome);
    }

    private function prepararDocumentos(Collection $aceites): Collection
    {
        return $aceites->map(function (TdaTermoAceite $aceite): array {
            $configuracao = config("tda.termos.{$aceite->tipo}");
            abort_if(! $configuracao, 404);

            return [
                'aceite' => $aceite,
                'titulo' => $configuracao['titulo'],
                'versao' => $aceite->versao,
                'paginas' => collect($configuracao['paginas'])
                    ->map(fn (string $pagina) => $this->arquivoPublicoDataUri($pagina))
                    ->filter()
                    ->values(),
            ];
        });
    }

    private function arquivoDiscoComHash(?string $caminho): array
    {
        if (! $caminho) return [null, null];
        $disk = Storage::disk('public_disk');
        if (! $disk->exists($caminho)) return [null, null];
        $conteudo = $disk->get($caminho);
        $mime = $disk->mimeType($caminho) ?: 'image/png';
        return ['data:'.$mime.';base64,'.base64_encode($conteudo), hash('sha256', $conteudo)];
    }

    private function arquivoPublicoDataUri(?string $caminho): ?string
    {
        if (! $caminho) return null;
        $arquivo = public_path($caminho);
        if (! is_file($arquivo)) return null;
        $mime = mime_content_type($arquivo) ?: 'image/jpeg';
        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($arquivo));
    }
}
