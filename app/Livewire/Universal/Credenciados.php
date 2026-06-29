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
use App\Models\Universal\Credenciado;
use App\Models\Universal\CredencialPresidio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Credenciados extends Component
{
    use WithPagination, WithFileUploads;

    // Propriedades Básicas (Idênticas ao Pessoas)
    public $credenciado_id, $nome, $celular, $telefone, $email, $endereco, $bairro, $cep, $cidade_id, $estado_id, $profissao, $aptidoes, $conversao, $obra, $testemunho, $bloco_id, $regiao_id, $igreja_id, $categoria_id, $cargo_id, $grupo_id, $foto, $fotoAtual;

    // Documentos de Identidade
    public $identidade_frente, $identidade_verso, $idFrenteAtual, $idVersoAtual;

    // Controle de Credenciais Dinâmicas
    public $credenciais = []; // Array para armazenar as credenciais temporárias

    // UI e Busca
    public $isOpen = false, $isViewOpen = false, $confirmDeleteId = null, $selectedCredenciado, $search = '', $errorMessage = '';
    public $cidades = [], $regiaos = [], $igrejas = [];
    public $allBlocos, $allEstados, $allCategorias, $allCargos, $allGrupos, $allPresidios;
    public $trabalho = [], $batismo = [], $preso = [];

    protected $queryString = ['search' => ['except' => '']];

    public function mount()
    {
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allEstados = Estado::orderBy('nome')->get();
        $this->allCategorias = Categoria::orderBy('nome')->get();
        $this->allCargos = Cargo::orderBy('nome')->get();
        $this->allGrupos = Grupo::orderBy('nome')->get();
        $this->allPresidios = Presidio::orderBy('nome')->get(); //
    }

    // Adiciona um novo bloco de credencial (máximo 10)
    public function addCredencial()
    {
        if (count($this->credenciais) < 10) {
            $this->credenciais[] = [
                'presidio_id' => '',
                'foto_frente' => null,
                'foto_verso' => null,
                'foto_frente_atual' => null,
                'foto_verso_atual' => null
            ];
        } else {
            $this->errorMessage = 'Limite de 10 credenciais atingido.';
        }
    }

    public function removeCredencial($index)
    {
        unset($this->credenciais[$index]);
        $this->credenciais = array_values($this->credenciais);
    }

    public function store()
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        // 1. Validação dos dados baseada no rules()
        $dataToSave = $this->validate();

        // 2. IMPORTANTE: Removemos os campos de arquivo do array principal para 
        // evitar que o Eloquent salve "null" sobre os caminhos que já existem no banco.
        unset($dataToSave['foto'], $dataToSave['identidade_frente'], $dataToSave['identidade_verso']);

        try {
            // 3. Tratamento da Foto de Perfil
            if ($this->foto && is_object($this->foto)) {
                // Se já existe uma foto, apaga o arquivo físico antes de salvar o novo
                if ($this->credenciado_id && $this->fotoAtual) {
                    \Illuminate\Support\Facades\Storage::disk('public_disk')->delete($this->fotoAtual);
                }
                $dataToSave['foto'] = $this->foto->store('credenciados/foto', 'public_disk');
            }

            // 4. Tratamento dos Documentos de Identidade (RG/CNH)
            if ($this->identidade_frente && is_object($this->identidade_frente)) {
                if ($this->idFrenteAtual) {
                    \Illuminate\Support\Facades\Storage::disk('public_disk')->delete($this->idFrenteAtual);
                }
                $dataToSave['identidade_frente'] = $this->identidade_frente->store('credenciados/documento', 'public_disk');
            }

            if ($this->identidade_verso && is_object($this->identidade_verso)) {
                if ($this->idVersoAtual) {
                    \Illuminate\Support\Facades\Storage::disk('public_disk')->delete($this->idVersoAtual);
                }
                $dataToSave['identidade_verso'] = $this->identidade_verso->store('credenciados/documento', 'public_disk');
            }

            // 5. Organização dos campos de Checkbox (Arrays)
            $dataToSave['trabalho'] = $this->trabalho ?? [];
            $dataToSave['batismo'] = $this->batismo ?? [];
            $dataToSave['preso'] = $this->preso ?? [];

            // 6. Persistência do Credenciado no Banco de Dados
            $credenciado = \App\Models\Universal\Credenciado::updateOrCreate(
                ['id' => $this->credenciado_id],
                $dataToSave
            );

            // 7. Processamento das Credenciais dos Presídios (Relacionamento HasMany)
            // Limpamos as associações antigas para reinserir as atuais (evita duplicidade na edição)
            if ($this->credenciado_id) {
                \App\Models\Universal\CredencialPresidio::where('credenciado_id', $this->credenciado_id)->delete();
            }

            foreach ($this->credenciais as $index => $cred) {
                if (!empty($cred['presidio_id'])) {
                    $novaCredencial = new \App\Models\Universal\CredencialPresidio();
                    $novaCredencial->credenciado_id = $credenciado->id;
                    $novaCredencial->presidio_id = $cred['presidio_id'];

                    // Upload da Frente da Credencial
                    if (isset($cred['foto_frente']) && is_object($cred['foto_frente'])) {
                        $novaCredencial->foto_frente = $cred['foto_frente']->store('credenciados/credencial', 'public_disk');
                    } else {
                        // Mantém a foto que já existia se não houver upload novo
                        $novaCredencial->foto_frente = $cred['foto_frente_atual'] ?? null;
                    }

                    // Upload do Verso da Credencial
                    if (isset($cred['foto_verso']) && is_object($cred['foto_verso'])) {
                        $novaCredencial->foto_verso = $cred['foto_verso']->store('credenciados/credencial', 'public_disk');
                    } else {
                        $novaCredencial->foto_verso = $cred['foto_verso_atual'] ?? null;
                    }

                    $novaCredencial->save();
                }
            }

            session()->flash('message', $this->credenciado_id ? 'Credenciado atualizado com sucesso.' : 'Credenciado cadastrado com sucesso.');
            $this->closeModal();
        } catch (\Illuminate\Database\QueryException $e) {
            // Trata erro de e-mail duplicado (Código 1062 no MySQL)
            if ($e->errorInfo[1] == 1062) {
                $this->errorMessage = 'Este e-mail já está em uso por outro registro.';
            } else {
                $this->errorMessage = 'Erro de banco de dados ao salvar.';
            }
            \Illuminate\Support\Facades\Log::error('Erro Query Credenciados: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado: ' . $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Erro Geral Credenciados: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $credenciado = \App\Models\Universal\Credenciado::with('credencialPresidios')->findOrFail($id);
            $this->credenciado_id = $id;

            // Preenche os campos básicos
            $this->fill($credenciado->toArray());

            // CORREÇÃO: Carregar as coleções dependentes para os selects funcionarem
            $this->regiaos = $credenciado->bloco_id ? \App\Models\Universal\Regiao::where('bloco_id', $credenciado->bloco_id)->orderBy('nome')->get() : collect();
            $this->igrejas = $credenciado->regiao_id ? \App\Models\Universal\Igreja::where('regiao_id', $credenciado->regiao_id)->orderBy('nome')->get() : collect();
            $this->cidades = $credenciado->estado_id ? \App\Models\Adm\Cidade::where('estado_id', $credenciado->estado_id)->orderBy('nome')->get() : collect();

            // Sanitização de Arrays (conforme o padrão Pessoas.php)
            $this->trabalho = $this->sanitizeJsonAttribute($credenciado->trabalho);
            $this->batismo = $this->sanitizeJsonAttribute($credenciado->batismo);
            $this->preso = $this->sanitizeJsonAttribute($credenciado->preso);

            // Datas
            $this->conversao = $credenciado->conversao ? $credenciado->conversao->format('Y-m-d') : null;
            $this->obra = $credenciado->obra ? $credenciado->obra->format('Y-m-d') : null;

            // Fotos atuais
            $this->fotoAtual = $credenciado->foto;
            $this->idFrenteAtual = $credenciado->identidade_frente;
            $this->idVersoAtual = $credenciado->identidade_verso;

            // Limpa os inputs de arquivo
            $this->foto = null;
            $this->identidade_frente = null;
            $this->identidade_verso = null;

            // Carrega credenciais existentes
            $this->credenciais = [];
            foreach ($credenciado->credencialPresidios as $cp) {
                $this->credenciais[] = [
                    'presidio_id' => $cp->presidio_id,
                    'foto_frente' => null,
                    'foto_verso' => null,
                    'foto_frente_atual' => $cp->foto_frente,
                    'foto_verso_atual' => $cp->foto_verso
                ];
            }

            $this->isOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar dados.');
            \Illuminate\Support\Facades\Log::error('Erro ao editar: ' . $e->getMessage());
        }
    }

    private function sanitizeJsonAttribute($attribute)
    {
        if (is_array($attribute)) return $attribute;
        $decoded = json_decode($attribute, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function render()
    {
        $results = \App\Models\Universal\Credenciado::with(['igreja', 'credencialPresidios.presidio'])
            ->where('nome', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.universal.credenciados', [
            'results' => $results,
        ]);
    }

    public function closeModal()
    {
        $this->reset();
        $this->mount();
        $this->isOpen = false;
    }

    protected function rules()
    {
        return [
            'nome' => 'required|string|min:3|max:250',
            'celular' => 'required|string|max:20',
            'email' => 'nullable|email|max:250|unique:credenciados,email,' . $this->credenciado_id,
            'bloco_id' => 'required|exists:blocos,id',
            'regiao_id' => 'required|exists:regiaos,id',
            'igreja_id' => 'required|exists:igrejas,id',
            'estado_id' => 'required|exists:estados,id',
            'cidade_id' => 'required|exists:cidades,id',
            'categoria_id' => 'required|exists:categorias,id',
            'cargo_id' => 'required|exists:cargos,id',
            'grupo_id' => 'required|exists:grupos,id',
            'endereco' => 'required|string|max:250',
            'bairro' => 'required|string|max:250',
            'profissao' => 'nullable|string|max:250',
            'cep' => 'nullable|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'aptidoes' => 'nullable|string',
            'conversao' => 'nullable|date',
            'obra' => 'nullable|date',
            'testemunho' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identidade_frente' => 'nullable|image|max:2048',
            'identidade_verso' => 'nullable|image|max:2048',
        ];
    }


    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    // ADICIONE ESTE MÉTODO ABAIXO
    private function resetInputFields()
    {
        // Reseta todas as propriedades para o estado inicial
        $this->reset();

        // Recarrega as coleções iniciais (Blocos, Estados, etc)
        $this->mount();

        // Limpa as mensagens de erro de validação
        $this->resetErrorBag();

        // Garante que o array de credenciais comece vazio ou limpo
        $this->credenciais = [];
    }


    public function updatedBlocoId($value)
    {
        $this->regiaos = $value ? \App\Models\Universal\Regiao::where('bloco_id', $value)->orderBy('nome')->get() : collect();
        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
    }

    public function updatedRegiaoId($value)
    {
        $this->igrejas = $value ? \App\Models\Universal\Igreja::where('regiao_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('igreja_id');
    }

    public function updatedEstadoId($value)
    {
        $this->cidades = $value ? \App\Models\Adm\Cidade::where('estado_id', $value)->orderBy('nome')->get() : collect();
        $this->reset('cidade_id');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Método para deletar (como no Pessoas)
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $cred = \App\Models\Universal\Credenciado::find($this->confirmDeleteId);
            // Lógica de apagar arquivos antes de deletar o registro...
            $cred->delete();
            session()->flash('message', 'Registro removido.');
            $this->confirmDeleteId = null;
        }
    }

    /**
     * Carrega os dados para visualizar os detalhes do credenciado
     */
    public function view($id)
    {
        try {
            // Carregamos o credenciado com todas as relações, incluindo as credenciais e presídios
            $this->selectedCredenciado = \App\Models\Universal\Credenciado::with([
                'bloco',
                'regiao',
                'igreja',
                'cidade.estado',
                'cargo',
                'categoria',
                'grupo',
                'credencialPresidios.presidio' // Importante para ver o nome do presídio na lista
            ])->findOrFail($id);

            $this->isViewOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar os detalhes.');
            \Illuminate\Support\Facades\Log::error('Erro ao visualizar credenciado: ' . $e->getMessage());
        }
    }

    /**
     * Fecha o modal de visualização
     */
    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedCredenciado = null;
    }

    protected function messages()
    {
        return [
            'email.unique' => 'Este endereço de e-mail já está cadastrado.',
            'nome.required' => 'O campo Nome Completo é obrigatório.',
            'celular.required' => 'O campo Celular é obrigatório.',
            'bloco_id.required' => 'Selecione um Bloco.',
            'regiao_id.required' => 'Selecione uma Região.',
            'igreja_id.required' => 'Selecione uma Igreja.',
            'estado_id.required' => 'Selecione um Estado.',
            'cidade_id.required' => 'Selecione uma Cidade.',
        ];
    }
}
