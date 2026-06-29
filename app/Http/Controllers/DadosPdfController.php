<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\DadosCurso as OficioDados; // Certifique-se de usar o alias correto
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DadosPdfController extends Controller
{
    // Este método é similar ao prepareDadosData do seu componente Livewire DadosCurso
    private function prepareDadosData($id)
    {
        // O with(['presidio', 'curso.instrutor', 'informacaoCurso']) é crucial para carregar os relacionamentos
        return OficioDados::with(['presidio', 'curso.instrutor', 'informacaoCurso'])->findOrFail($id);
    }

    // Método para exibir a view do PDF HTML para Dados de Curso
    public function showPdfView($id)
    {
        try {
            $dados = $this->prepareDadosData($id);
            // Retorna apenas a view do PDF, sem o layout principal da aplicação
            return view('livewire.unp.oficios.pdf.dados-curso-pdf', ['selectedDados' => $dados]);
        } catch (\Exception $e) {
            // Em caso de erro, redireciona ou mostra uma página de erro
            Log::error('Erro ao carregar os dados do curso para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar os dados do curso para PDF: ' . $e->getMessage());
        }
    }
}