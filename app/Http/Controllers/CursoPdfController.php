<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioCurso as Oficio;
use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CursoPdfController extends Controller
{
    // Este método é similar ao prepareOficioData do seu componente Livewire OficioCurso
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with(['presidio', 'curso.instrutor'])->findOrFail($id);
        // setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese'); // Mantenha se estiver usando translatedFormat

        $oficio->numero_oficio = 'Ofício Nº ' . (600 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->created_at)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SENHOR DIRETOR DO ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');
        $oficio->assunto_formatado = 'ASSUNTO: SOLICITAÇÃO DE INÍCIO DE CURSO';

        $inicio = Carbon::parse($oficio->curso->inicio);
        $fim = Carbon::parse($oficio->curso->fim);
        $duracao = (int) max(1, ceil($inicio->diffInMonths($fim->addDay()))); // Adicionado addDay() para incluir o mês final

        $oficio->paragrafo_principal = "Com os cumprimentos de estilo, a Universal nos Presídios - UNP, vem, através de seu Pastor-Coordenador Geral que a este subscreve, requerer a autorização do início do curso de " . $oficio->curso->nome . ", com carga horaria de " . $oficio->curso->carga . " o curso será realizado: " . $oficio->curso->dia_hora . " nesta unidade prisional e deve conter em sala de aula " . $oficio->curso->reeducandos . " reeducados, com o início dia " . $inicio->translatedFormat('d \d\e F \d\e Y') . " até o dia " . $fim->translatedFormat('d \d\e F \d\e Y') . ", totalizando " . str_pad($duracao, 2, '0', STR_PAD_LEFT) . " meses de duração, terminando o curso iremos fazer a formatura no dia " . Carbon::parse($oficio->curso->formatura)->translatedFormat('d \d\e F \d\e Y') . ".";
        
        // Formatar CPF e RG do instrutor, se necessário
        if ($oficio->curso->instrutor) {
            if ($oficio->curso->instrutor->cpf) {
                $oficio->curso->instrutor->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $oficio->curso->instrutor->cpf);
            }
            if ($oficio->curso->instrutor->rg) {
                $oficio->curso->instrutor->rg = preg_replace('/(\d{1,2})(\d{3})(\d{3})(\d{1})/', '$1.$2.$3-$4', $oficio->curso->instrutor->rg);
            }
        }

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício de Curso
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            return view('livewire.unp.oficios.pdf.oficio-curso-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar o ofício de curso para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício de curso para PDF: ' . $e->getMessage());
        }
    }
}