<?php

namespace App\Http\Controllers\Universal;

use App\Http\Controllers\Controller;
use App\Models\Universal\PastorUnp;
use Illuminate\Http\Request;

class PastorUnpPrintController extends Controller
{
    public function show($id)
    {
        // Carrega o pastor com todos os seus relacionamentos, incluindo o carro
        $pastor = PastorUnp::with(['bloco', 'regiao', 'carroUnp'])->findOrFail($id);

        // Retorna a view de impressão, passando os dados do pastor
        return view('livewire.universal.print', compact('pastor'));
    }
}