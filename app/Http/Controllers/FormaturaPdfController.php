<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unp\OficioFormatura as Oficio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FormaturaPdfController extends Controller
{
    // Este método é similar ao prepareOficioData do seu componente Livewire
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'curso'])->findOrFail($id);

        $oficio->numero_oficio = 'Ofício Nº ' . (100 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: FORMATURA DO CURSO DE ' . mb_strtoupper($oficio->curso->nome, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' .  mb_strtoupper($oficio->presidio->diretor,'UTF-8');

        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = 'formatura do Curso de ' . $oficio->curso->nome . ', dia ' . $eventoData->translatedFormat('d \d\e F \d\e Y') . ', às ' . $eventoData->format('H:i') . 'h';

        return $oficio;
    }

    // Método para exibir a view do PDF
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            // Retorna apenas a view do PDF, sem o layout principal da aplicação
            return view('livewire.unp.oficios.pdf.oficio-formatura-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            // Em caso de erro, você pode redirecionar ou mostrar uma página de erro
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício para visualização de PDF: ' . $e->getMessage());
        }
    }
}