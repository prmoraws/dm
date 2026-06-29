<?php

namespace App\Livewire\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\Universal\Bloco;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use App\Models\Universal\Pessoa;
use App\Models\Universal\Regiao;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Pessoas extends Component
{
    use WithPagination, WithFileUploads;

    // Propriedades do modelo
    public $pessoa_id, $nome, $celular, $telefone, $email, $endereco, $bairro, $cep, $cidade_id, $estado_id, $profissao, $aptidoes, $conversao, $obra, $testemunho, $bloco_id, $regiao_id, $igreja_id, $categoria_id, $cargo_id, $grupo_id, $foto, $fotoAtual;

    // Propriedades de controle da UI
    public $isOpen = false;
    public $isViewOpen = false;
    public $confirmDeleteId = null;
    public $selectedPessoa;
    public $search = '';
    public $errorMessage = '';

    // Propriedades para selects dinâmicos e estáticos
    public $cidades = [], $regiaos = [], $igrejas = [];
    public $allBlocos, $allEstados, $allCategorias, $allCargos, $allGrupos;

    // Propriedades para checkboxes
    public $trabalho = [], $batismo = [], $preso = [];

    protected $queryString = ['search' => ['except' => '']];

    // CORREÇÃO: Mensagens de validação personalizadas
    protected function messages()
    {
        return [
            'email.unique' => 'Este endereço de e-mail já está cadastrado. Por favor, utilize outro.',
            'nome.required' => 'O campo Nome Completo é obrigatório.',
            'celular.required' => 'O campo Celular é obrigatório.',
            'endereco.required' => 'O campo Endereço é obrigatório.',
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'bloco_id.required' => 'É necessário selecionar um Bloco.',
            'regiao_id.required' => 'É necessário selecionar uma Região.',
            'igreja_id.required' => 'É necessário selecionar uma Igreja.',
            'estado_id.required' => 'É necessário selecionar um Estado.',
            'cidade_id.required' => 'É necessário selecionar uma Cidade.',
            'categoria_id.required' => 'É necessário selecionar uma Categoria.',
            'cargo_id.required' => 'É necessário selecionar um Cargo.',
            'grupo_id.required' => 'É necessário selecionar um Grupo.',
        ];
    }

    protected function rules()
    {
        return [
            'nome' => 'required|string|min:3|max:250',
            'celular' => 'required|string|max:20',
            // CORREÇÃO: Adicionada regra 'unique' para o e-mail
            // A regra ignora o ID da pessoa atual ao editar, evitando falsos positivos.
            'email' => 'nullable|email|max:250|unique:pessoas,email,' . $this->pessoa_id,
            'endereco' => 'required|string|max:250',
            'bairro' => 'required|string|max:250',
            'profissao' => 'nullable|string|max:250',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bloco_id' => 'required|exists:blocos,id',
            'regiao_id' => 'required|exists:regiaos,id',
            'igreja_id' => 'required|exists:igrejas,id',
            'estado_id' => 'required|exists:estados,id',
            'cidade_id' => 'required|exists:cidades,id',
            'categoria_id' => 'required|exists:categorias,id',
            'cargo_id' => 'required|exists:cargos,id',
            'grupo_id' => 'required|exists:grupos,id',
            'cep' => 'nullable|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'aptidoes' => 'nullable|string',
            'conversao' => 'nullable|date',
            'obra' => 'nullable|date',
            'testemunho' => 'nullable|string',
            'trabalho' => 'nullable|array',
            'batismo' => 'nullable|array',
            'preso' => 'nullable|array',
        ];
    }

    public function mount()
    {
        $this->allBlocos = Bloco::orderBy('nome')->get();
        $this->allEstados = Estado::orderBy('nome')->get();
        $this->allCategorias = Categoria::orderBy('nome')->get();
        $this->allCargos = Cargo::orderBy('nome')->get();
        $this->allGrupos = Grupo::orderBy('nome')->get();
    }

    // ... métodos updated* ...
    public function updatedBlocoId($value)
    {
        $this->regiaos = $value ? Regiao::where('bloco_id', $value)->orderBy('nome')->get() : collect();
        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
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
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * AJUSTE (redução de carga no banco):
     * 1) O 'orWhereHas('bloco', ...)' foi removido. Esse padrão gera uma
     *    subquery correlacionada (EXISTS ... WHERE ... = pessoas.bloco_id)
     *    executada virtualmente por linha candidata. Como a query já faz
     *    JOIN com 'blocos', a busca pelo nome do bloco agora usa
     *    'blocos.nome' diretamente, sem subquery extra.
     * 2) As condições de busca foram agrupadas dentro de um único
     *    where(function ($q) {...}) para garantir a precedência correta
     *    do SQL (evita que o OR "escape" e quebre outros filtros que
     *    venham a ser adicionados no futuro).
     * 3) Nenhuma mudança aqui resolve sozinha o problema do debounce —
     *    a velocidade de digitação ainda dispara uma query por busca.
     *    Isso é tratado no lado do Blade (wire:model.live.debounce.500ms).
     */
    public function render()
    {
        $query = Pessoa::query()
            ->select('pessoas.*')
            ->with(['bloco', 'cargo'])
            ->join('blocos', 'pessoas.bloco_id', '=', 'blocos.id')
            ->when($this->search, function ($query) {
                $term = $this->search;
                $query->where(function ($q) use ($term) {
                    $q->where('pessoas.nome', 'like', "%{$term}%")
                        ->orWhere('pessoas.celular', 'like', "%{$term}%")
                        ->orWhere('blocos.nome', 'like', "%{$term}%");
                });
            })
            ->orderBy('blocos.nome')
            ->orderBy('pessoas.nome');

        return view('livewire.universal.pessoas', [
            'results' => $query->paginate(10),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        $dataToSave = $this->validate();

        try {
            if ($this->foto) {
                if ($this->pessoa_id && $this->fotoAtual) {
                    Storage::disk('public_disk')->delete($this->fotoAtual);
                }

                $dataToSave['foto'] = $this->foto->store('pessoas', 'public_disk');
            } else {
                unset($dataToSave['foto']);
            }

            $dataToSave['trabalho'] = $this->trabalho ?? [];
            $dataToSave['batismo'] = $this->batismo ?? [];
            $dataToSave['preso'] = $this->preso ?? [];

            Pessoa::updateOrCreate(['id' => $this->pessoa_id], $dataToSave);

            session()->flash('message', $this->pessoa_id ? 'Pessoa atualizada com sucesso.' : 'Pessoa criada com sucesso.');
            $this->closeModal();
        } catch (QueryException $e) {
            // CORREÇÃO: Captura de erro do banco de dados para feedback amigável
            if ($e->errorInfo[1] == 1062) { // Código de erro para "Duplicate entry"
                $this->errorMessage = 'Não foi possível salvar. O e-mail informado já está em uso.';
            } else {
                $this->errorMessage = 'Ocorreu um erro de banco de dados ao salvar.';
            }
            Log::error('Erro de Query ao salvar pessoa: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado ao salvar os dados.';
            Log::error('Erro geral ao salvar pessoa: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $pessoa = Pessoa::findOrFail($id);
            $this->pessoa_id = $id;

            // CORREÇÃO 1: Usar ->toArray() para preencher o formulário com segurança.
            // Isso resolve o problema do botão "Atualizar" que não funciona.
            $this->fill($pessoa->toArray());

            // CORREÇÃO 2: Sanitizar os atributos que deveriam ser arrays.
            // Isso resolve o problema dos checkboxes que marcam tudo.
            $this->trabalho = $this->sanitizeJsonAttribute($pessoa->trabalho);
            $this->batismo = $this->sanitizeJsonAttribute($pessoa->batismo);
            $this->preso = $this->sanitizeJsonAttribute($pessoa->preso);

            // Mantemos a formatação correta das datas
            $this->conversao = $pessoa->conversao ? $pessoa->conversao->format('Y-m-d') : null;
            $this->obra = $pessoa->obra ? $pessoa->obra->format('Y-m-d') : null;

            // O resto do método continua como antes
            $this->regiaos = $pessoa->bloco_id ? Regiao::where('bloco_id', $pessoa->bloco_id)->orderBy('nome')->get() : collect();
            $this->igrejas = $pessoa->regiao_id ? Igreja::where('regiao_id', $pessoa->regiao_id)->orderBy('nome')->get() : collect();
            $this->cidades = $pessoa->estado_id ? Cidade::where('estado_id', $pessoa->estado_id)->orderBy('nome')->get() : collect();
            $this->fotoAtual = $pessoa->foto;
            $this->foto = null; // Limpa o input de arquivo
            $this->isOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar dados para edição.');
            Log::error('Erro ao editar pessoa: ' . $e->getMessage());
        }
    }

    private function sanitizeJsonAttribute($attribute)
    {
        if (is_array($attribute)) {
            return $attribute;
        }
        if (!is_string($attribute) || empty($attribute)) {
            return [];
        }
        $decoded = json_decode($attribute, true);
        while (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }
        return is_array($decoded) ? $decoded : [];
    }

    public function view($id)
    {
        try {
            $this->selectedPessoa = Pessoa::with(['bloco', 'regiao', 'igreja', 'cidade.estado', 'cargo', 'categoria', 'grupo'])->findOrFail($id);
            $this->isViewOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar os detalhes da pessoa.');
            Log::error('Erro ao visualizar pessoa: ' . $e->getMessage());
        }
    }
    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }
    public function delete()
    {
        if ($this->confirmDeleteId) {
            try {
                $pessoa = Pessoa::findOrFail($this->confirmDeleteId);
                if ($pessoa->foto) {
                    Storage::disk('public_disk')->delete($pessoa->foto);
                }
                $pessoa->delete();
                session()->flash('message', 'Pessoa deletada com sucesso.');
            } catch (\Exception $e) {
                session()->flash('error', 'Erro ao deletar o registro.');
            } finally {
                $this->confirmDeleteId = null;
            }
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function closeViewModal()
    {
        $this->isViewOpen = false;
        $this->selectedPessoa = null;
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
        $this->resetErrorBag();
    }
}
