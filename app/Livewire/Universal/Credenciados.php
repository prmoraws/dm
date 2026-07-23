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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Credenciados extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    public $credenciado_id, $nome, $celular, $telefone, $email, $endereco, $bairro, $cep, $cidade_id, $estado_id, $profissao, $aptidoes, $conversao, $obra, $testemunho, $bloco_id, $regiao_id, $igreja_id, $categoria_id, $cargo_id, $grupo_id, $foto, $fotoAtual;

    public $identidade_frente, $identidade_verso, $idFrenteAtual, $idVersoAtual;
    public $credenciais = [];

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
        $this->allPresidios = Presidio::orderBy('nome')->get();
    }

    public function addCredencial()
    {
        if (count($this->credenciais) < 10) {
            $this->credenciais[] = [
                'presidio_id' => '',
                'foto_frente' => null,
                'foto_verso' => null,
                'foto_frente_atual' => null,
                'foto_verso_atual' => null,
                'unidade_nao_faz' => false,
                'data_vencimento' => null,
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

        $user = Auth::user();

        // Se o usuário não for do bloco 21 (Admin), forçamos o bloco_id dele a ser o único permitido ao salvar
        if ($user->bloco_id != 21) {
            $this->bloco_id = $user->bloco_id;
        }

        $dataToSave = $this->validate();

        // Se for edição, verificamos explicitamente se o registro pertence ao bloco do usuário
        if ($this->credenciado_id) {
            $existing = Credenciado::findOrFail($this->credenciado_id);
            if ($user->bloco_id != 21 && $existing->bloco_id != $user->bloco_id) {
                abort(403, 'Ação não autorizada.');
            }
        }

        unset($dataToSave['foto'], $dataToSave['identidade_frente'], $dataToSave['identidade_verso']);

        try {
            if ($this->foto && is_object($this->foto)) {
                if ($this->credenciado_id && $this->fotoAtual) {
                    Storage::disk('public_disk')->delete($this->fotoAtual);
                }
                $dataToSave['foto'] = $this->foto->store('credenciados/foto', 'public_disk');
            }

            if ($this->identidade_frente && is_object($this->identidade_frente)) {
                if ($this->idFrenteAtual) {
                    Storage::disk('public_disk')->delete($this->idFrenteAtual);
                }
                $dataToSave['identidade_frente'] = $this->identidade_frente->store('credenciados/documento', 'public_disk');
            }

            if ($this->identidade_verso && is_object($this->identidade_verso)) {
                if ($this->idVersoAtual) {
                    Storage::disk('public_disk')->delete($this->idVersoAtual);
                }
                $dataToSave['identidade_verso'] = $this->identidade_verso->store('credenciados/documento', 'public_disk');
            }

            $dataToSave['trabalho'] = $this->trabalho ?? [];
            $dataToSave['batismo'] = $this->batismo ?? [];
            $dataToSave['preso'] = $this->preso ?? [];

            $credenciado = Credenciado::updateOrCreate(
                ['id' => $this->credenciado_id],
                $dataToSave
            );

            if ($this->credenciado_id) {
                CredencialPresidio::where('credenciado_id', $this->credenciado_id)->delete();
            }

            foreach ($this->credenciais as $index => $cred) {
                if (!empty($cred['presidio_id'])) {
                    $novaCredencial = new CredencialPresidio();
                    $novaCredencial->credenciado_id = $credenciado->id;
                    $novaCredencial->presidio_id = $cred['presidio_id'];
                    $novaCredencial->unidade_nao_faz = $cred['unidade_nao_faz'] ?? false;
                    $novaCredencial->data_vencimento = $cred['data_vencimento'] ?? null;

                    if (!($cred['unidade_nao_faz'] ?? false)) {
                        if (isset($cred['foto_frente']) && is_object($cred['foto_frente'])) {
                            $novaCredencial->foto_frente = $cred['foto_frente']->store('credenciados/credencial', 'public_disk');
                        } else {
                            $novaCredencial->foto_frente = $cred['foto_frente_atual'] ?? null;
                        }

                        if (isset($cred['foto_verso']) && is_object($cred['foto_verso'])) {
                            $novaCredencial->foto_verso = $cred['foto_verso']->store('credenciados/credencial', 'public_disk');
                        } else {
                            $novaCredencial->foto_verso = $cred['foto_verso_atual'] ?? null;
                        }
                    } else {
                        // Se a unidade não faz, limpamos as fotos salvas anteriormente se houver
                        $novaCredencial->foto_frente = null;
                        $novaCredencial->foto_verso = null;
                    }

                    $novaCredencial->save();
                }
            }

            session()->flash('message', $this->credenciado_id ? 'Credenciado atualizado com sucesso.' : 'Credenciado cadastrado com sucesso.');
            $this->closeModal();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                $this->errorMessage = 'Este e-mail já está em uso por outro registro.';
            } else {
                $this->errorMessage = 'Erro de banco de dados ao salvar.';
            }
            Log::error('Erro Query Credenciados: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->errorMessage = 'Ocorreu um erro inesperado: ' . $e->getMessage();
            Log::error('Erro Geral Credenciados: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $credenciado = Credenciado::with('credencialPresidios')->findOrFail($id);

            // Valida a Policy de segurança
            $this->authorize('update', $credenciado);

            $this->credenciado_id = $id;
            $this->fill($credenciado->toArray());

            $this->regiaos = $credenciado->bloco_id ? Regiao::where('bloco_id', $credenciado->bloco_id)->orderBy('nome')->get() : collect();
            $this->igrejas = $credenciado->regiao_id ? Igreja::where('regiao_id', $credenciado->regiao_id)->orderBy('nome')->get() : collect();
            $this->cidades = $credenciado->estado_id ? Cidade::where('estado_id', $credenciado->estado_id)->orderBy('nome')->get() : collect();

            $this->trabalho = $this->sanitizeJsonAttribute($credenciado->trabalho);
            $this->batismo = $this->sanitizeJsonAttribute($credenciado->batismo);
            $this->preso = $this->sanitizeJsonAttribute($credenciado->preso);

            $this->conversao = $credenciado->conversao ? $credenciado->conversao->format('Y-m-d') : null;
            $this->obra = $credenciado->obra ? $credenciado->obra->format('Y-m-d') : null;

            $this->fotoAtual = $credenciado->foto;
            $this->idFrenteAtual = $credenciado->identidade_frente;
            $this->idVersoAtual = $credenciado->identidade_verso;

            $this->foto = null;
            $this->identidade_frente = null;
            $this->identidade_verso = null;

            $this->credenciais = [];
            foreach ($credenciado->credencialPresidios as $cp) {
                $this->credenciais[] = [
                    'presidio_id' => $cp->presidio_id,
                    'foto_frente' => null,
                    'foto_verso' => null,
                    'foto_frente_atual' => $cp->foto_frente,
                    'foto_verso_atual' => $cp->foto_verso,
                    'unidade_nao_faz' => (bool) $cp->unidade_nao_faz,
                    'data_vencimento' => $cp->data_vencimento ? $cp->data_vencimento->format('Y-m-d') : null,
                ];
            }

            $this->isOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar dados.');
            Log::error('Erro ao editar: ' . $e->getMessage());
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
        $user = Auth::user();

        $query = Credenciado::with(['igreja', 'credencialPresidios.presidio'])
            // Restringe rigorosamente para o bloco do usuário logado se ele NÃO for do bloco 21
            ->when($user->bloco_id != 21, function ($q) use ($user) {
                $q->where('bloco_id', $user->bloco_id);
            })
            ->when($this->search, function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%');
            })
            ->latest();

        return view('livewire.universal.credenciados', [
            'results' => $query->paginate(10),
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
        $user = Auth::user();

        $rules = [
            'nome' => 'required|string|min:3|max:250',
            'celular' => 'required|string|max:20',
            'email' => 'nullable|email|max:250|unique:credenciados,email,' . $this->credenciado_id,
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

        // Se for admin (bloco 21), ele pode escolher o bloco no select. Senão, o bloco_id é obrigatório e fixo.
        if ($user->bloco_id == 21) {
            $rules['bloco_id'] = 'required|exists:blocos,id';
        } else {
            $rules['bloco_id'] = 'nullable';
        }

        return $rules;
    }

    public function create()
    {
        $this->resetInputFields();
        // Se não for admin, já fixa o bloco_id do usuário atual ao abrir o modal de criação
        if (Auth::user()->bloco_id != 21) {
            $this->bloco_id = Auth::user()->bloco_id;
        }
        $this->isOpen = true;
    }

    private function resetInputFields()
    {
        $this->reset();
        $this->mount();
        $this->resetErrorBag();
        $this->credenciais = [];
    }

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

    public function confirmDelete($id)
    {
        $credenciado = Credenciado::findOrFail($id);
        $this->authorize('delete', $credenciado);
        $this->confirmDeleteId = $id;
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $cred = Credenciado::find($this->confirmDeleteId);
            if ($cred) {
                $this->authorize('delete', $cred);
                $cred->delete();
                session()->flash('message', 'Registro removido.');
            }
            $this->confirmDeleteId = null;
        }
    }

    public function view($id)
    {
        try {
            $credenciado = Credenciado::with([
                'bloco',
                'regiao',
                'igreja',
                'cidade.estado',
                'cargo',
                'categoria',
                'grupo',
                'credencialPresidios.presidio'
            ])->findOrFail($id);

            $this->authorize('view', $credenciado);

            $this->selectedCredenciado = $credenciado;
            $this->isViewOpen = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar os detalhes.');
            Log::error('Erro ao visualizar credenciado: ' . $e->getMessage());
        }
    }

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
