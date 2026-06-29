<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioTrabalho as Oficio;
use App\Models\Unp\Oficios\Convidado; // Necessário para a lista de evangelistas
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrabalhoPdfController extends Controller
{
    // Este método é similar ao prepareOficioData do seu componente Livewire OficioTrabalho
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);

        $oficio->numero_oficio = 'Ofício Nº ' . (250 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ACESSO PARA TRABALHO DE EVANGELISMO';
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $oficio->evento_formatado = Carbon::parse($oficio->dia_hora_evento)->translatedFormat('l, \d\i\a d \d\e F, \à\s H:i\h');
        
        // Carregar os dados dos evangelistas com a máscara de CPF
        $oficio->lista_evangelistas = Convidado::whereIn('id', $oficio->evangelistas)->get()->map(function ($evangelista) {
            $evangelista->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $evangelista->cpf);
            return $evangelista;
        });

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício de Trabalho
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            // Retorna apenas a view do PDF, sem o layout principal da aplicação
            return view('livewire.unp.oficios.pdf.oficio-trabalho-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar o ofício de trabalho para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício de trabalho para PDF: ' . $e->getMessage());
        }
    }
}