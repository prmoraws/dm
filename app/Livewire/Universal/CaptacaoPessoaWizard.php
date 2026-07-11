<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\Universal\Bloco;
use App\Models\Universal\CaptacaoPessoa;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // ADICIONADO: Necessário para manipular arquivos.
use Illuminate\Support\Str;             // ADICIONADO: Necessário para gerar a string aleatória do nome do arquivo.
use Livewire\Component;
use Livewire\WithFileUploads;

class CaptacaoPessoaWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 8;

    // Propriedades do formulário
    public $foto, $nome, $celular, $telefone, $email;
    public $cep, $endereco, $bairro, $estado_id, $cidade_id;
    public $profissao, $aptidoes;
    public $conversao, $obra, $testemunho;
    public array $trabalho = [], $batismo = [], $preso = [];
    public $bloco_id, $regiao_id, $igreja_id;
    public $categoria_id, $cargo_id, $grupo_id;
    public $assinatura; // ADICIONADO: A propriedade para a assinatura precisa ser declarada.

    // Propriedades de suporte
    public $allEstados, $cidades = [];
    public $allBlocos, $regiaos = [], $igrejas = [];
    public $allCategorias, $allCargos, $allGrupos;
    public ?string $selectedBlocoName = '';

    public function mount()
    {
        $this->allEstados = Estado::orderBy('nome')->get();
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allCategorias = Categoria::orderBy('nome')->get();
        $this->allCargos = Cargo::orderBy('nome')->get();
        $this->allGrupos = Grupo::orderBy('nome')->get();
        
        // Preenche com BA > Salvador por padrão para melhor UX
        $bahia = $this->allEstados->firstWhere('uf', 'BA');
        if ($bahia) {
            $this->estado_id = $bahia->id;
            $this->cidades = Cidade::where('estado_id', $bahia->id)->orderBy('nome')->get();
            $salvador = $this->cidades->firstWhere('nome', 'Salvador');
            if ($salvador) {
                $this->cidade_id = $salvador->id;
            }
        }
    }

    protected function rules(): array
    {
        // Regras de validação por etapa
        return match ($this->step) {
            2 => [
                'bloco_id' => 'required|exists:blocos,id',
                'regiao_id' => 'required|exists:regiaos,id',
                'igreja_id' => 'required|exists:igrejas,id',
                'categoria_id' => 'required|exists:categorias,id',
                'cargo_id' => 'required|exists:cargos,id',
                'grupo_id' => $this->selectedBlocoName === 'Catedral' ? 'required|exists:grupos,id' : 'nullable|exists:grupos,id',
            ],
            3 => [
                'nome' => 'required|string|min:3|max:255',
                'celular' => 'required|string|max:20',
                'email' => 'nullable|email|max:255|unique:captacao_pessoas,email',
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            ],
            4 => [
                'estado_id' => 'required|exists:estados,id',
                'cidade_id' => 'required|exists:cidades,id',
                'endereco' => 'required|string|max:255',
                'bairro' => 'required|string|max:255',
            ],
            5 => [
                'profissao' => 'nullable|string|max:255',
                'aptidoes' => 'nullable|string',
                'conversao' => 'nullable|date',
                'obra' => 'nullable|date',
                'trabalho' => 'nullable|array',
                'batismo' => 'nullable|array',
                'preso' => 'nullable|array',
            ],
            6 => [
                'testemunho' => 'nullable|string|min:10',
            ],
            7 => [
                'assinatura' => 'required',
            ],
            default => [],
        };
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Funções para dropdowns dependentes
    public function updatedBlocoId($value)
    {
        $this->regiaos = $value ? Regiao::where('bloco_id', $value)->orderBy('nome')->get() : collect();
        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
        
        $blocoSelecionado = $this->allBlocos->find($value);
        $this->selectedBlocoName = $blocoSelecionado ? $blocoSelecionado->nome : '';
    }

    public function updatedRegiaoId($value)
    {
        $this->igrejas = $value ? Igreja::where('regiao_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('igreja_id');
    }

    public function updatedEstadoId($value)
    {
        $this->cidades = $value ? Cidade::where('estado_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('cidade_id');
    }

    // Navegação do formulário
    public function nextStep()
    {
        if ($this->step > 1) {
            $this->validate();
        }
        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }
    
    // CORRIGIDO: Método para salvar assinatura vindo do JavaScript (Livewire v3)
    public function saveSignature($signatureData)
    {
        $this->assinatura = $signatureData;
    }

   
    public function submit()
    {
        // A validação completa já está correta, não precisa mexer aqui.
        $this->validate([
            'bloco_id' => 'required|exists:blocos,id',
            'regiao_id' => 'required|exists:regiaos,id',
            'igreja_id' => 'required|exists:igrejas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'cargo_id' => 'required|exists:cargos,id',
            'grupo_id' => $this->selectedBlocoName === 'Catedral' ? 'required|exists:grupos,id' : 'nullable|exists:grupos,id',
            'nome' => 'required|string|min:3|max:255',
            'celular' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:captacao_pessoas,email',
            'estado_id' => 'required|exists:estados,id',
            'cidade_id' => 'required|exists:cidades,id',
            'endereco' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'assinatura' => 'required',
        ]);
        
        try {
            // ✅ CORRIGIDO: Array $dataToSave agora inclui TODOS os campos do formulário.
            $dataToSave = [
                // Etapa 2: Igreja e Função
                'bloco_id' => $this->bloco_id,
                'regiao_id' => $this->regiao_id,
                'igreja_id' => $this->igreja_id,
                'categoria_id' => $this->categoria_id,
                'cargo_id' => $this->cargo_id,
                'grupo_id' => $this->grupo_id,

                // Etapa 3: Dados Pessoais
                'nome' => $this->nome,
                'celular' => $this->celular,
                'telefone' => $this->telefone,
                'email' => $this->email,

                // Etapa 4: Endereço
                'cep' => $this->cep,
                'endereco' => $this->endereco,
                'bairro' => $this->bairro,
                'estado_id' => $this->estado_id,
                'cidade_id' => $this->cidade_id,

                // Etapa 5: Informações Adicionais
                'profissao' => $this->profissao,
                'aptidoes' => $this->aptidoes,
                'conversao' => $this->conversao,
                'obra' => $this->obra,
                'trabalho' => $this->trabalho,
                'batismo' => $this->batismo,
                'preso' => $this->preso,

                // Etapa 6: Testemunho
                'testemunho' => $this->testemunho,

                // Outros
                'status' => 'pendente',
            ];

            if ($this->foto) {
                $dataToSave['foto'] = $this->foto->store('captacao_temp', 'public_disk');
            }

            if (preg_match('/^data:image\/(\w+);base64,/', $this->assinatura)) {
                $data = substr($this->assinatura, strpos($this->assinatura, ',') + 1);
                $data = base64_decode($data);
                $fileName = 'captacao_temp/signatures/' . Str::random(40) . '.png';
                Storage::disk('public_disk')->put($fileName, $data);
                $dataToSave['assinatura'] = $fileName;
            }

            CaptacaoPessoa::create($dataToSave);

            session()->flash('success', 'Cadastro realizado com sucesso!');
            
            $this->step++;

        } catch (\Exception $e) {
            Log::error('Erro ao salvar captação de pessoa: ' . $e->getMessage());
            session()->flash('error', 'Ocorreu um erro inesperado ao enviar seu cadastro. Tente novamente.');
        }
    }

    public function render()
    {
        
        return view('livewire.universal.captacao-pessoa-wizard')->layout('layouts.guest');
    }
}