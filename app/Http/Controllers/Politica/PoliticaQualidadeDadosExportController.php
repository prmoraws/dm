<?php

namespace App\Http\Controllers\Politica;

use App\Exports\Politica\QualidadeDadosExport;
use App\Http\Controllers\Controller;
use App\Services\Politica\V2\PoliticaQualidadeDadosService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PoliticaQualidadeDadosExportController extends Controller
{
    public function pdf(Request $request, PoliticaQualidadeDadosService $service)
    {
        $dados = $this->dados($request, $service);

        return Pdf::loadView('politica.v2.pdf.qualidade-dados', $dados)
            ->setPaper('a4', 'landscape')
            ->download('politica-auditoria-'.now()->format('Ymd-His').'.pdf');
    }

    public function excel(Request $request, PoliticaQualidadeDadosService $service)
    {
        $dados = $this->dados($request, $service);

        return Excel::download(new QualidadeDadosExport($dados), 'politica-auditoria-'.now()->format('Ymd-His').'.xlsx');
    }

    /** @return array<string,mixed> */
    private function dados(Request $request, PoliticaQualidadeDadosService $service): array
    {
        return $service->painel(
            (string) $request->query('nivel', 'todos'),
            (string) $request->query('grupo', 'todos'),
            (string) $request->query('busca', ''),
        );
    }
}
