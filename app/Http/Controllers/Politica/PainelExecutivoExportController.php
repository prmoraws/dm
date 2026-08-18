<?php

namespace App\Http\Controllers\Politica;

use App\Exports\Politica\PainelExecutivoExport;
use App\Http\Controllers\Controller;
use App\Services\Politica\V2\PainelExecutivoRelatorioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PainelExecutivoExportController extends Controller
{
    public function pdf(Request $request, PainelExecutivoRelatorioService $relatorio)
    {
        $dados = $relatorio->gerar($this->politico($request));
        $nome = $this->nomeArquivo($dados['politicoSelecionado'] ?? 'todos', 'pdf');

        return Pdf::loadView('politica.v2.pdf.painel-executivo', $dados)
            ->setPaper('a4', 'landscape')
            ->download($nome);
    }

    public function excel(Request $request, PainelExecutivoRelatorioService $relatorio)
    {
        $dados = $relatorio->gerar($this->politico($request));
        $nome = $this->nomeArquivo($dados['politicoSelecionado'] ?? 'todos', 'xlsx');

        return Excel::download(new PainelExecutivoExport($dados), $nome);
    }

    private function politico(Request $request): string
    {
        $valor = trim((string) $request->query('politico', 'todos'));
        return $valor !== '' ? $valor : 'todos';
    }

    private function nomeArquivo(string $politico, string $extensao): string
    {
        $recorte = $politico === 'todos' ? 'todos' : Str::slug($politico);
        return 'politica-painel-executivo-'.$recorte.'-'.now()->format('Ymd-His').'.'.$extensao;
    }
}
