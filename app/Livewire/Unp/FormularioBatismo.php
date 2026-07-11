<?php

namespace App\Livewire\Unp;

use App\Models\Universal\Bloco;
use App\Models\Unp\Presidio;
use App\Models\Unp\Batismo;
use Livewire\Component;

class FormularioBatismo extends Component
{
    public $nome;
    public $bloco_id = '';
    public $quantidade = 1;
    public $presidio_id = '';
    public $data_batismo;

    // Lista de presídios filtrados reativamente
    public $presidiosDisponiveis = [];

    protected $rules = [
        'nome' => 'required|string|min:3|max:255',
        'bloco_id' => 'required|exists:blocos,id',
        'quantidade' => 'required|integer|min:1|max:999',
        'presidio_id' => 'required|exists:presidios,id',
        'data_batismo' => 'required|date|before_or_equal:today',
    ];

    protected $messages = [
        'nome.required' => 'O nome completo é obrigatório.',
        'bloco_id.required' => 'Selecione o bloco.',
        'presidio_id.required' => 'Selecione o presídio.',
        'data_batismo.required' => 'A data do batismo é obrigatória.',
        'data_batismo.before_or_equal' => 'A data do batismo não pode ser uma data futura.',
        'quantidade.required' => 'A quantidade é obrigatória.',
        'quantidade.integer' => 'Digite um número válido.',
        'quantidade.min' => 'A quantidade mínima é 1.',
    ];

    // Lifecycle hook: roda sempre que a propriedade $bloco_id muda
    public function updatedBlocoId($value)
    {
        $this->presidio_id = ''; // Reseta o presídio selecionado

        if (!empty($value)) {
            // Busca apenas os presídios associados ao bloco selecionado
            $bloco = Bloco::with('presidios')->find($value);
            $this->presidiosDisponiveis = $bloco ? $bloco->presidios : [];
        } else {
            $this->presidiosDisponiveis = [];
        }
    }

    public function salvar()
    {
        $this->validate();

        try {
            Batismo::create([
                'nome' => $this->nome,
                'bloco_id' => $this->bloco_id,
                'presidio_id' => $this->presidio_id,
                'quantidade' => $this->quantidade,
                'data_batismo' => $this->data_batismo,
            ]);

            session()->flash('message', 'Cadastro de batismo realizado com sucesso!');
            $this->resetCampos();
        } catch (\Exception $e) {
            session()->flash('error', 'Ocorreu um erro ao salvar: ' . $e->getMessage());
        }
    }

    private function resetCampos()
    {
        $this->nome = '';
        $this->bloco_id = '';
        $this->presidio_id = '';
        $this->quantidade = 1;
        $this->data_batismo = '';
        $this->presidiosDisponiveis = [];
    }

    public function render()
    {
        // Carrega todos os blocos para o primeiro select
        $blocos = Bloco::orderBy('nome', 'asc')->get();

        // Define o layout como 'guest' para que seja uma página pública e limpa
        return view('livewire.unp.formulario-batismo', [
            'blocos' => $blocos
        ])->layout('layouts.guest');
    }
}
