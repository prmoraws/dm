<?php

namespace App\Livewire\Universal;

use App\Models\Universal\PastorUnp;
use App\Models\Universal\CarroUnp;
use App\Models\Universal\Bloco;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EdicaoCarroPublica extends Component
{
    use WithFileUploads;

    // Controle de Fluxo
    public $step = 1; // 1: Identificação, 2: Edição
    public $pastor_unp_id;
    public $nascimento;

    // Dados do Carro
    public $carro_id, $bloco_id, $modelo, $ano, $placa, $km, $demanda;
    public $foto_frente, $foto_tras, $foto_direita, $foto_esquerda, $foto_dentro, $foto_cambio;
    public $foto_frente_atual, $foto_tras_atual, $foto_direita_atual, $foto_esquerda_atual, $foto_dentro_atual, $foto_cambio_atual;

    // Coleções
    public $pastorOptions = [], $blocoOptions = [], $yearOptions = [];

    public function mount()
    {
        // ALTERAÇÃO: 'carro' mudou para 'carroUnp' para bater com o Model
        $this->pastorOptions = \App\Models\Universal\PastorUnp::has('carroUnp')->orderBy('nome')->get();

        $this->blocoOptions = \App\Models\Universal\Bloco::orderBy('nome')->get();

        $currentYear = date('Y');
        for ($i = 0; $i <= 25; $i++) {
            $this->yearOptions[] = $currentYear - $i;
        }
    }

    public function verificarIdentidade()
    {
        $this->validate([
            'pastor_unp_id' => 'required|exists:pastor_unps,id',
            'nascimento' => 'required|date',
        ], [
            'pastor_unp_id.required' => 'Selecione seu nome na lista.',
            'nascimento.required' => 'Informe sua data de nascimento para validar.',
        ]);

        $pastor = PastorUnp::where('id', $this->pastor_unp_id)
            ->where('nascimento', $this->nascimento)
            ->first();

        if ($pastor) {
            $this->carregarCarro($pastor->id);
            $this->step = 2;
        } else {
            session()->flash('error', 'Data de nascimento incorreta para o pastor selecionado.');
        }
    }

    public function carregarCarro($pastorId)
    {
        $pastor = \App\Models\Universal\PastorUnp::findOrFail($pastorId);

        // Acessando o carro através da relação definida no seu Model
        $carro = $pastor->carroUnp;

        if (!$carro) {
            session()->flash('error', 'Nenhum veículo encontrado para este cadastro.');
            $this->step = 1;
            return;
        }

        $this->carro_id = $carro->id;
        $this->bloco_id = $carro->bloco_id;
        $this->modelo = $carro->modelo;
        $this->ano = $carro->ano;
        $this->placa = $carro->placa;
        $this->km = $carro->km;
        $this->demanda = $carro->demanda;

        // Fotos atuais para exibição
        $this->foto_frente_atual = $carro->foto_frente;
        $this->foto_tras_atual = $carro->foto_tras;
        $this->foto_direita_atual = $carro->foto_direita;
        $this->foto_esquerda_atual = $carro->foto_esquerda;
        $this->foto_dentro_atual = $carro->foto_dentro;
        $this->foto_cambio_atual = $carro->foto_cambio;
    }

    public function update()
    {
        $this->validate([
            'modelo' => 'required|string',
            'ano' => 'required',
            'placa' => 'required|string|max:10',
            'km' => 'nullable|integer',
            'foto_frente' => 'nullable|image|max:5120',
            'foto_tras' => 'nullable|image|max:5120',
        ]);

        $carro = CarroUnp::findOrFail($this->carro_id);

        $data = [
            'modelo' => $this->modelo,
            'ano' => $this->ano,
            'placa' => $this->placa,
            'km' => $this->km,
            'demanda' => $this->demanda,
            'bloco_id' => $this->bloco_id,
        ];

        // Processamento de Fotos (Similar ao seu modal original)
        $photoFields = ['frente', 'tras', 'direita', 'esquerda', 'dentro', 'cambio'];
        foreach ($photoFields as $field) {
            $property = 'foto_' . $field;
            $currentPath = 'foto_' . $field . '_atual';

            if ($this->$property) {
                if ($carro->$currentPath) {
                    Storage::disk('public_disk')->delete($carro->$currentPath);
                }
                $data['foto_' . $field] = $this->$property->store('carros', 'public_disk');
            }
        }

        $carro->update($data);

        session()->flash('message', 'Dados do veículo atualizados com sucesso!');
        return redirect()->route('carro.public.edit');
    }

    public function render()
    {
        return view('livewire.universal.edicao-carro-publica')->layout('layouts.guest');
    }
}
