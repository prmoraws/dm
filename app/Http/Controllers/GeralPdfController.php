<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioGeral as Oficio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeralPdfController extends Controller
{
    // Este método é similar ao prepareOficioData do seu componente Livewire OficioGeral
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);

        $oficio->numero_oficio = 'Ofício Nº ' . (700 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ' . mb_strtoupper($oficio->assunto, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício Geral
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            return view('livewire.unp.oficios.pdf.oficio-geral-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar o ofício geral para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício geral para PDF: ' . $e->getMessage());
        }
    }
}