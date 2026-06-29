<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioEvento as Oficio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventoPdfController extends Controller
{
     // Este método é similar ao prepareOficioData do seu componente Livewire OficioEvento
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);

        $oficio->numero_oficio = 'Ofício Nº ' . (150 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ' . mb_strtoupper($oficio->assunto, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = $oficio->assunto . ', ' . $eventoData->translatedFormat('l, \d\i\a d \d\e F \d\e Y, \à\s H:i\h');

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício de Evento
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            // Retorna apenas a view do PDF, sem o layout principal da aplicação
            return view('livewire.unp.oficios.pdf.oficio-evento-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar o ofício de evento para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício de evento para PDF: ' . $e->getMessage());
        }
    }
}
