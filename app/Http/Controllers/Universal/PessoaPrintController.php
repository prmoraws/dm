<?php

namespace App\Http\Controllers\Universal;

use App\Http\Controllers\Controller;
use App\Models\Universal\Pessoa;

class PessoaPrintController extends Controller
{
    /**
     * Exibe a ficha de voluntário para impressão.
     *
     * @param  \App\Models\Universal\Pessoa  $pessoa
     * @return \Illuminate\View\View
     */
    public function showFichaVoluntario(Pessoa $pessoa)
    {
        // Carrega todos os relacionamentos necessários para a ficha
        $voluntario = $pessoa->load('bloco', 'regiao', 'igreja', 'cargo', 'categoria', 'grupo', 'cidade.estado');
        
        // Retorna a view Blade com os dados do voluntário
        return view('livewire.universal.pdf.voluntario', compact('voluntario'));
    }
}