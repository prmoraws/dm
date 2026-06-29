<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\Unp\Presidio;
use App\Models\Universal\Bloco;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use App\Models\Universal\CaptacaoCredenciado;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CaptacaoCredenciadoWizard extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 7;

    // Propriedades seguindo a tabela SQL
    public $nome, $celular, $telefone, $email, $cpf, $profissao, $aptidoes;
    public $bloco_id, $regiao_id, $igreja_id, $categoria_id, $cargo_id, $grupo_id;
    public $estado_id, $cidade_id, $endereco, $bairro, $cep;
    public $trabalho = [], $batismo = [], $preso = [], $conversao, $obra, $testemunho;

    // Arquivos
    public $foto, $identidade_frente, $identidade_verso;
    public $credenciais = [];

    // Coleções
    public $allBlocos, $allEstados, $allCategorias, $allCargos, $allGrupos, $allPresidios;
    public $regiaos = [], $igrejas = [], $cidades = [];

    public function mount()
    {
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allEstados = Estado::orderBy('nome')->get();
        $this->allCategorias = Categoria::orderBy('nome')->get();
        $this->allCargos = Cargo::orderBy('nome')->get();
        $this->allGrupos = Grupo::orderBy('nome')->get();
        $this->allPresidios = Presidio::orderBy('nome')->get();
        $this->addCredencial();
    }

    public function updatedBlocoId($value)
    {
        $this->regiaos = $value ? Regiao::where('bloco_id', $value)->orderBy('nome')->get() : [];
        $this->reset(['regiao_id', 'igreja_id']);
    }

    public function updatedRegiaoId($value)
    {
        $this->igrejas = $value ? Igreja::where('regiao_id', $value)->orderBy('nome')->get() : [];
        $this->reset('igreja_id');
    }

    public function updatedEstadoId($value)
    {
        $this->cidades = $value ? Cidade::where('estado_id', $value)->orderBy('nome')->get() : [];
        $this->reset('cidade_id');
    }

    public function addCredencial()
    {
        if (count($this->credenciais) < 10) {
            $this->credenciais[] = ['presidio_id' => '', 'foto_frente' => null, 'foto_verso' => null];
        }
    }

    public function removeCredencial($index)
    {
        unset($this->credenciais[$index]);
        $this->credenciais = array_values($this->credenciais);
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) $this->currentStep--;
    }

    public function validateStep()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'bloco_id' => 'required',
                'regiao_id' => 'required',
                'igreja_id' => 'required',
                'categoria_id' => 'required',
                'cargo_id' => 'required',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate(['nome' => 'required|min:3', 'celular' => 'required', 'cpf' => 'required|unique:captacao_credenciados,cpf']);
        } elseif ($this->currentStep == 3) {
            $this->validate(['estado_id' => 'required', 'cidade_id' => 'required', 'endereco' => 'required', 'bairro' => 'required']);
        }
    }

    public function submit()
    {
        $this->validate([
            'foto' => 'required|image|max:10240',
            'identidade_frente' => 'required|image|max:10240',
            'identidade_verso' => 'required|image|max:10240',
        ]);

        try {
            $credenciaisFinal = [];
            foreach ($this->credenciais as $cred) {
                if (!empty($cred['presidio_id'])) {
                    $credenciaisFinal[] = [
                        'presidio_id' => $cred['presidio_id'],
                        'foto_frente' => $cred['foto_frente'] ? $cred['foto_frente']->store('credenciados/captura', 'public_disk') : null,
                        'foto_verso' => $cred['foto_verso'] ? $cred['foto_verso']->store('credenciados/captura', 'public_disk') : null,
                    ];
                }
            }

            CaptacaoCredenciado::create([
                'nome' => $this->nome,
                'celular' => $this->celular,
                'telefone' => $this->telefone,
                'email' => $this->email,
                'cpf' => $this->cpf,
                'bloco_id' => $this->bloco_id,
                'regiao_id' => $this->regiao_id,
                'igreja_id' => $this->igreja_id,
                'categoria_id' => $this->categoria_id,
                'cargo_id' => $this->cargo_id,
                'grupo_id' => $this->grupo_id ?: null,
                'estado_id' => $this->estado_id,
                'cidade_id' => $this->cidade_id,
                'endereco' => $this->endereco,
                'bairro' => $this->bairro,
                'cep' => $this->cep,
                'profissao' => $this->profissao,
                'aptidoes' => $this->aptidoes,
                'conversao' => $this->conversao,
                'obra' => $this->obra,
                'testemunho' => $this->testemunho,
                'trabalho' => $this->trabalho,
                'batismo' => $this->batismo,
                'preso' => $this->preso,
                'foto' => $this->foto->store('credenciados/captura', 'public_disk'),
                'identidade_frente' => $this->identidade_frente->store('credenciados/captura', 'public_disk'),
                'identidade_verso' => $this->identidade_verso->store('credenciados/captura', 'public_disk'),
                'credenciais_payload' => $credenciaisFinal,
                'status' => 'pendente'
            ]);

            return redirect()->route('captacao.sucesso');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.universal.captacao-credenciado-wizard')->layout('layouts.guest');
    }
}
