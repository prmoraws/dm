<?php

namespace App\Http\Controllers\Politica;

use App\Exports\Politica\EspelhoCidadeExport;
use App\Http\Controllers\Controller;
use App\Models\Politica\Cidade;
use App\Services\Politica\V2\EspelhoCidadeRelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EspelhoCidadeExportController extends Controller
{
    public function pdf(Request $request, Cidade $cidade, EspelhoCidadeRelatorioService $service)
    {
        $dados = $service->gerar($cidade, $this->candidaturaId($request));

        return Pdf::loadView('politica.v2.pdf.espelho-cidade', $dados)
            ->setPaper('a4', 'portrait')
            ->download($this->nomeArquivo($dados, 'pdf'));
    }

    public function excel(Request $request, Cidade $cidade, EspelhoCidadeRelatorioService $service)
    {
        $dados = $service->gerar($cidade, $this->candidaturaId($request));

        return Excel::download(new EspelhoCidadeExport($dados), $this->nomeArquivo($dados, 'xlsx'));
    }

    private function candidaturaId(Request $request): int
    {
        $id = (int) $request->query('candidatura', 0);
        abort_if($id <= 0, 422, 'Selecione um candidato no Espelho Inteligente antes de exportar.');

        return $id;
    }

    /** @param array<string,mixed> $dados */
    private function nomeArquivo(array $dados, string $extensao): string
    {
        $cidade = Str::slug((string) ($dados['cidade']['nome'] ?? 'cidade'));
        $candidato = Str::slug((string) ($dados['candidato']['nome'] ?? 'candidato'));

        return "espelho-{$cidade}-{$candidato}-".now()->format('Ymd-His').".{$extensao}";
    }
}
