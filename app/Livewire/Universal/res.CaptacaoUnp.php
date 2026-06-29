<?php

namespace App\Livewire\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\Captacao;
use App\Models\Universal\Regiao;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class CaptacaoUnp extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 7;

    public array $state = [];

    public $foto, $foto_esposa;
    public $foto_frente, $foto_tras, $foto_direita, $foto_esquerda, $foto_dentro, $foto_cambio;

    public $blocoOptions = [], $regiaoOptions = [];
    public $possuiCarro = null;

    public function mount()
    {
        $this->blocoOptions = Bloco::orderBy('nome')->get();
        $this->state = [
            'tipo_captacao' => 'completo', // PADRÃO: completo ou carro_only
            'nome' => '',
            'cpf' => '',
            'bloco_id' => '',
            'regiao_id' => '',
            'cargo' => '',
            'nascimento' => '',
            'email' => '',
            'whatsapp' => '',
            'telefone' => '',
            'chegada' => '',
            'entrada' => '',
            'preso' => '0',
            'trabalho' => [],
            'nome_esposa' => '',
            'obra' => '',
            'casado' => '',
            'consagrada_esposa' => '0',
            'preso_esposa' => '0',
            'trabalho_esposa' => [],
            'email_esposa' => '',
            'whatsapp_esposa' => '',
            'telefone_esposa' => '',
            'modelo' => '',
            'ano' => '',
            'placa' => '',
            'km' => '',
            'demanda' => ''
        ];
    }

    public function render()
    {
        return view('livewire.universal.captacao-unp')->layout('layouts.guest');
    }

    public function updatedStateBlocoId($blocoId)
    {
        $this->regiaoOptions = $blocoId ? Regiao::where('bloco_id', $blocoId)->orderBy('nome')->get() : [];
        $this->state['regiao_id'] = '';
    }

   public function nextStep()
{
    // Valida o passo atual antes de prosseguir
    $this->validateStep($this->step);

    // LÓGICA DE ATALHO (CONTORNO)
    if ($this->step === 1 && ($this->state['tipo_captacao'] ?? 'completo') === 'carro_only') {
        $this->step = 5; // Pula direto para o Passo 5 (Dados do Veículo)
        $this->possuiCarro = 'sim';
        return;
    }

    if ($this->step === 4 && $this->possuiCarro === 'nao') {
        $this->step = 6;
    } elseif ($this->step < $this->totalSteps) {
        $this->step++;
    }
}

public function previousStep()
{
    // LÓGICA DE VOLTA DO ATALHO
    if ($this->step === 5 && ($this->state['tipo_captacao'] ?? 'completo') === 'carro_only') {
        $this->step = 1;
        return;
    }

    if ($this->step === 6 && $this->possuiCarro === 'nao') {
        $this->step = 4;
    } elseif ($this->step > 1) {
        $this->step--;
    }
}

public function validateStep(int $step)
{
    $rules = [];
    $isCarroOnly = ($this->state['tipo_captacao'] ?? 'completo') === 'carro_only';

    switch ($step) {
        case 1:
            // Nome e CPF continuam obrigatórios para identificar o dono do carro
            $rules = [
                'state.tipo_captacao' => 'required',
                'state.nome' => 'required|string|max:255',
                'state.cpf' => 'required|string|max:20|unique:captacoes,cpf'
            ];
            break;
        case 2:
            if (!$isCarroOnly) { // Só valida se NÃO for apenas carro
                $rules = [
                    'state.bloco_id' => 'required',
                    'state.regiao_id' => 'required',
                    'state.cargo' => 'required',
                    'state.whatsapp' => 'required',
                ];
            }
            break;
        case 5:
            $rules = [
                'state.modelo' => 'required',
                'state.placa' => 'required',
                'foto_frente' => 'required|image|max:5120',
            ];
            break;
    }

    if (!empty($rules)) {
        $this->validate($rules, [
            'state.nome.required' => 'Identifique-se informando seu nome.',
            'state.cpf.required' => 'O CPF é obrigatório para o registro.',
        ]);
    }
}

    public function goToStep($stepNumber)
    {
        if ($stepNumber < $this->step) {
            $this->step = $stepNumber;
        }
    }

    

    public function submit()
{
    $isCarroOnly = ($this->state['tipo_captacao'] ?? 'completo') === 'carro_only';

    // Validação final apenas dos campos essenciais para o modo escolhido
    $rules = [
        'foto' => $isCarroOnly ? 'nullable' : 'required|image|max:5120',
    ];
    
    $this->validate($rules);

    try {
        // Armazenamento das fotos (usando null-safe para evitar erros)
        if ($this->foto) {
            $this->state['foto'] = $this->foto->store('captacao_temp', 'public_disk');
        }
            // CORREÇÃO: Usando 'public_disk' para salvar diretamente em public/captacao_temp
            if ($this->foto)
                $this->state['foto'] = $this->foto->store('captacao_temp', 'public_disk');
            if ($this->foto_esposa)
                $this->state['foto_esposa'] = $this->foto_esposa->store('captacao_temp', 'public_disk');

            if ($this->possuiCarro === 'sim') {
                if ($this->foto_frente)
                    $this->state['foto_frente'] = $this->foto_frente->store('captacao_temp', 'public_disk');
                if ($this->foto_tras)
                    $this->state['foto_tras'] = $this->foto_tras->store('captacao_temp', 'public_disk');
                if ($this->foto_direita)
                    $this->state['foto_direita'] = $this->foto_direita->store('captacao_temp', 'public_disk');
                if ($this->foto_esquerda)
                    $this->state['foto_esquerda'] = $this->foto_esquerda->store('captacao_temp', 'public_disk');
                if ($this->foto_dentro)
                    $this->state['foto_dentro'] = $this->foto_dentro->store('captacao_temp', 'public_disk');
                if ($this->foto_cambio)
                    $this->state['foto_cambio'] = $this->foto_cambio->store('captacao_temp', 'public_disk');
            }

            \App\Models\Universal\Captacao::create([
            'nome'   => $this->state['nome'],
            'cpf'    => $this->state['cpf'],
            'status' => 'pendente',
            'payload' => $this->state, // O JSON aceita campos nulos sem quebrar
        ]);

        $this->step = $this->totalSteps;
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Erro: ' . $e->getMessage());
        session()->flash('submission_error', 'Erro técnico ao salvar os dados.');
    }
    }
}