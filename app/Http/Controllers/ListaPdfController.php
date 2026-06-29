<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\ListaCertificado as Lista;
use App\Models\Unp\Oficios\Reeducando;
use Illuminate\Http\Request;

class ListaPdfController extends Controller
{
    /**
     * Prepara os dados da lista, incluindo a formatação do documento do reeducando.
     */
    private function prepareListaData($id)
    {
        $lista = Lista::with(['curso.presidio', 'curso.instrutor'])->findOrFail($id);

        // Carrega os reeducandos e adiciona uma propriedade 'documento_formatado' em cada um.
        $lista->lista_reeducandos = Reeducando::whereIn('id', $lista->reeducandos ?? [])->orderBy('nome')->get()->map(function ($reeducando) {
            
            // CORREÇÃO: Lógica robusta para identificar, limpar e formatar CPF ou RG.
            $documentoFormatado = 'N/A'; // Valor padrão

            // Prioriza o CPF
            if (!empty($reeducando->cpf)) {
                $cpfLimpo = preg_replace('/\D/', '', $reeducando->cpf); // Remove tudo que não for dígito
                if (strlen($cpfLimpo) === 11) {
                    $documentoFormatado = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfLimpo);
                }
            } 
            // Se não encontrou um CPF válido, usa o RG (se existir)
            elseif (!empty($reeducando->rg)) {
                // Apenas exibe o RG como está, já que a formatação pode variar muito.
                $documentoFormatado = $reeducando->rg;
            }
            
            // Adiciona a nova propriedade ao objeto para ser usada na view
            $reeducando->documento_formatado = $documentoFormatado;
            
            return $reeducando;
        });

        return $lista;
    }

    /**
     * Mostra a view do PDF HTML para Lista de Certificados.
     */
    public function showPdfView($id)
    {
        try {
            $lista = $this->prepareListaData($id);
            return view('livewire.unp.oficios.pdf.lista-certificado-pdf', ['selectedLista' => $lista]);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar a lista de certificados para PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar a lista para PDF.');
        }
    }
}