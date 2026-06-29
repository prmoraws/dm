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
        $this->validateStep($this->step);

        if ($this->step === 4 && $this->possuiCarro === 'nao') {
            $this->step = 6;
        } elseif ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step === 6 && $this->possuiCarro === 'nao') {
            $this->step = 4;
        } elseif ($this->step > 1) {
            $this->step--;
        }
    }

    public function goToStep($stepNumber)
    {
        if ($stepNumber < $this->step) {
            $this->step = $stepNumber;
        }
    }

    public function validateStep(int $step)
    {
        $rules = [];
        switch ($step) {
            case 1:
                $rules = ['state.nome' => 'required|string|max:255', 'state.cpf' => 'required|string|max:20|unique:captacoes,cpf'];
                break;
            case 2:
                $rules = [
                    'state.bloco_id' => 'required|exists:blocos,id',
                    'state.regiao_id' => 'required|exists:regiaos,id',
                    'state.cargo' => 'required|in:responsavel,auxiliar',
                    'state.whatsapp' => 'required|string|max:20',
                    'state.preso' => 'required|boolean',
                    'foto' => 'nullable|image|max:2048',
                ];
                break;
            case 3:
                $rules = ['foto_esposa' => 'nullable|image|max:2048'];
                break;
            case 4:
                $rules = ['possuiCarro' => 'required|in:sim,nao'];
                break;
            case 5:
                $rules = [
                    'state.modelo' => 'required_if:possuiCarro,sim|string|max:255',
                    'state.ano' => 'required_if:possuiCarro,sim|string|max:255',
                    'state.placa' => 'required_if:possuiCarro,sim|string|max:10',
                    'foto_frente' => 'required_if:possuiCarro,sim|image|max:2048',
                    'foto_tras' => 'required_if:possuiCarro,sim|image|max:2048',
                ];
                break;
        }

        if (!empty($rules)) {
            $this->validate($rules);
        }
    }

    public function submit()
    {
        try {
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

            Captacao::create([
                'nome' => $this->state['nome'],
                'cpf' => $this->state['cpf'],
                'status' => 'pendente',
                'payload' => $this->state,
            ]);

            $this->step = $this->totalSteps;
        } catch (\Exception $e) {
            Log::error('Erro ao salvar captação: ' . $e->getMessage());
            session()->flash('submission_error', 'Ocorreu um erro ao enviar seus dados. Por favor, tente novamente.');
        }
    }
}