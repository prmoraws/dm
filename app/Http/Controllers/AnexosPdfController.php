<?php

namespace App\Http\Controllers;

use App\Models\Unp\Oficios\OficioAnexo;
use App\Models\Unp\Oficios\Convidado; // Necessário para a lista de convidados
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnexosPdfController extends Controller
{
    // Este método é similar ao prepareAnexoData do seu componente Livewire Anexos
    private function prepareAnexoData($id)
    {
        $anexo = OficioAnexo::findOrFail($id);
        
        $convidadosIds = array_merge(
            $anexo->esposas ?? [],
            $anexo->convidados ?? [],
            $anexo->comunicacao ?? [],
            $anexo->organizacao ?? []
        );
        $convidadosData = Convidado::whereIn('id', $convidadosIds)->get()->keyBy('id');

        $anexo->listas_formatadas = [
            'PASTORES/ESPOSAS' => collect($anexo->esposas ?? [])->map(function($id) use ($convidadosData) {
                $convidado = $convidadosData->get($id);
                if ($convidado && $convidado->cpf) {
                    $convidado->nome = mb_strtoupper($convidado->nome, 'UTF-8');
                    $convidado->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $convidado->cpf);
                }
                return $convidado;
            })->filter(), // Usar filter() para remover nulos se algum ID não for encontrado
            'CONVIDADOS' => collect($anexo->convidados ?? [])->map(function($id) use ($convidadosData) {
                $convidado = $convidadosData->get($id);
                if ($convidado && $convidado->cpf) {
                    $convidado->nome = mb_strtoupper($convidado->nome, 'UTF-8');
                    $convidado->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $convidado->cpf);
                }
                return $convidado;
            })->filter(),
            'VOLUNTÁRIOS DA COMUNICAÇÃO' => collect($anexo->comunicacao ?? [])->map(function($id) use ($convidadosData) {
                $convidado = $convidadosData->get($id);
                if ($convidado && $convidado->cpf) {
                    $convidado->nome = mb_strtoupper($convidado->nome, 'UTF-8');
                    $convidado->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $convidado->cpf);
                }
                return $convidado;
            })->filter(),
            'VOLUNTÁRIOS DA ORGANIZAÇÃO' => collect($anexo->organizacao ?? [])->map(function($id) use ($convidadosData) {
                $convidado = $convidadosData->get($id);
                if ($convidado && $convidado->cpf) {
                    $convidado->nome = mb_strtoupper($convidado->nome, 'UTF-8');
                    $convidado->cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $convidado->cpf);
                }
                return $convidado;
            })->filter(),
        ];

        return $anexo;
    }

    // Método para exibir a view do PDF HTML para Anexos
    public function showPdfView($id)
    {
        try {
            $anexo = $this->prepareAnexoData($id);
            return view('livewire.unp.oficios.pdf.anexos-pdf', ['selectedAnexo' => $anexo]);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar o anexo para visualização de PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Não foi possível carregar o anexo para PDF: ' . $e->getMessage());
        }
    }
}