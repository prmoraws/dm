<?php

namespace App\Http\Controllers;
use App\Models\Unp\Oficios\OficioCredencial as Oficio; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CredencialPdfController extends Controller
{
   // Este método é similar ao prepareOficioData do seu componente Livewire OficioCredencial
    private function prepareOficioData($id)
    {
        $oficio = Oficio::with('presidio')->findOrFail($id);

        // setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese'); // Se precisar para formatação de data mais complexa

        $oficio->numero_oficio = 'Ofício Nº ' . (200 + $oficio->id) . '/2025_UNP-IURD/BA';
        $oficio->data_formatada = 'Salvador – BA, ' . Carbon::parse($oficio->created_at)->translatedFormat('d \d\e F \d\e Y');
        $oficio->destinatario = 'AO ILMO. SR. DIRETOR ' . mb_strtoupper($oficio->presidio->nome, 'UTF-8');
        $oficio->diretor_formatado = 'ILMO. DR.: ' . mb_strtoupper($oficio->presidio->diretor, 'UTF-8');

        // Formatação de CPF e RG com máscara, se não vierem já mascarados do DB
        // Certifique-se que o CPF/RG no DB não tem caracteres de máscara se você for aplicar aqui.
        // Se já vier formatado, estas linhas não são estritamente necessárias, mas servem como garantia.
        $oficio->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $oficio->cpf);
        if ($oficio->rg) {
            // Esta máscara é mais genérica, pode precisar de ajuste dependendo do formato exato do RG.
            // Ex: para 00.000.000-0, usar '/(\d{2})(\d{3})(\d{3})(\d{1})/', '$1.$2.$3-$4'
            $oficio->rg = preg_replace('/(\d{1,2})(\d{3})(\d{3})(\d{1})/', '$1.$2.$3-$4', $oficio->rg);
        }

        return $oficio;
    }

    // Método para exibir a view do PDF HTML para Ofício de Credencial
    public function showPdfView($id)
    {
        try {
            $oficio = $this->prepareOficioData($id);
            // Retorna apenas a view do PDF, sem o layout principal da aplicação
            return view('livewire.unp.oficios.pdf.oficio-credencial-pdf', ['selectedOficio' => $oficio]);
        } catch (\Exception $e) {
            // Em caso de erro, redireciona ou mostra uma página de erro
            Log::error('Erro ao carregar o ofício de credencial para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o ofício de credencial para PDF: ' . $e->getMessage());
        }
    }
}