<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioCop as Oficio;
use App\Models\Unp\Oficios\Convidado; // Necessário para a lista de convidados
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CopPdfController extends Controller
{
    // Este método é similar ao prepareOficioData do seu componente Livewire OficioCop
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'unidade'])->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8'); // Mantenha se estiver usando translatedFormat

        $oficio->numero_oficio = 'Ofício Nº ' . (300 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->data_oficio)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR DIRETOR DO ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: ACESSO DE CARROS E MEMBROS NO COMPLEXO';
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $eventoData = Carbon::parse($oficio->dia_hora_evento);
        $oficio->evento_formatado = 'Com os cumprimentos de estilo, a Universal nos Presídios - UNP, vem, através de seu Coordenador Geral que a este subscreve, requerer autorização de acesso no complexo penitenciário, aos membros para adentrar no complexo, <b>' . $eventoData->translatedFormat('l, \d\i\a d \d\e F \d\e Y') . ', a partir das ' . $eventoData->format('H:i\h') . '</b>, onde será realizado <b>' . $oficio->evento . '</b> na unidade ' . $oficio->unidade->nome . '.';

        // Carregar os dados dos convidados com a máscara de CPF
        $oficio->lista_convidados = Convidado::whereIn('id', $oficio->convidados)->get()->map(function ($convidado) {
            $convidado->nome = mb_strtoupper($convidado->nome, 'UTF-8');
            $convidado->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $convidado->cpf);
            return $convidado;
        });

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício COP
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            return view('livewire.unp.oficios.pdf.oficio-cop-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar o ofício COP para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício COP para PDF: ' . $e->getMessage());
        }
    }
}