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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
    public $credencial_status = '', $inicio = '', $fim = '', $filtro_bloco = '', $filtro_regiao = '', $filtro_igreja = '';
    public $filtro_cargo = '', $filtro_categoria = '', $filtro_presidio = '';
    public $perPage = 10, $sortField = 'created_at', $sortDirection = 'desc', $filtrosAbertos = false;
    public $cidades = [], $regiaos = [], $igrejas = [];
    public $allBlocos, $allEstados, $allCategorias, $allCargos, $allGrupos, $allPresidios;
    public $trabalho = [], $batismo = [], $preso = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'credencial_status' => ['except' => ''],
        'inicio' => ['except' => ''],
        'fim' => ['except' => ''],
        'filtro_bloco' => ['as' => 'bloco', 'except' => ''],
        'filtro_regiao' => ['as' => 'regiao', 'except' => ''],
        'filtro_igreja' => ['as' => 'igreja', 'except' => ''],
        'filtro_cargo' => ['as' => 'cargo', 'except' => ''],
        'filtro_categoria' => ['as' => 'categoria', 'except' => ''],
        'filtro_presidio' => ['as' => 'presidio', 'except' => ''],
        'perPage' => ['as' => 'por_pagina', 'except' => 10],
        'sortField' => ['as' => 'ordenar', 'except' => 'created_at'],
        'sortDirection' => ['as' => 'direcao', 'except' => 'desc'],
    ];

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
                'data_primeira_credencial' => null,
                'data_renovacao' => null,
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

        if ($user->bloco_id != 21) {
            $this->bloco_id = $user->bloco_id;
        }

        $this->celular = preg_replace('/\D+/', '', (string) $this->celular);
        $this->telefone = $this->telefone ? preg_replace('/\D+/', '', (string) $this->telefone) : null;
        $this->cep = $this->cep ? preg_replace('/\D+/', '', (string) $this->cep) : null;
        $this->email = $this->email ? mb_strtolower(trim((string) $this->email)) : null;
        $this->nome = trim((string) $this->nome);

        $existente = $this->credenciado_id
            ? Credenciado::with('credencialPresidios')->findOrFail($this->credenciado_id)
            : null;

        $this->authorize($existente ? 'update' : 'create', $existente ?: Credenciado::class);
        $dataToSave = $this->validate();
        $novosArquivos = [];
        $arquivosParaExcluir = [];

        try {
            if ($this->foto && is_object($this->foto)) {
                $dataToSave['foto'] = $this->foto->store('credenciados/foto', 'public_disk');
                $novosArquivos[] = $dataToSave['foto'];
                if ($this->fotoAtual) $arquivosParaExcluir[] = $this->fotoAtual;
            }

            if ($this->identidade_frente && is_object($this->identidade_frente)) {
                $dataToSave['identidade_frente'] = $this->identidade_frente->store('credenciados/documento', 'public_disk');
                $novosArquivos[] = $dataToSave['identidade_frente'];
                if ($this->idFrenteAtual) $arquivosParaExcluir[] = $this->idFrenteAtual;
            }

            if ($this->identidade_verso && is_object($this->identidade_verso)) {
                $dataToSave['identidade_verso'] = $this->identidade_verso->store('credenciados/documento', 'public_disk');
                $novosArquivos[] = $dataToSave['identidade_verso'];
                if ($this->idVersoAtual) $arquivosParaExcluir[] = $this->idVersoAtual;
            }

            foreach (['foto', 'identidade_frente', 'identidade_verso'] as $campoArquivo) {
                if (! isset($dataToSave[$campoArquivo]) || ! is_string($dataToSave[$campoArquivo])) {
                    unset($dataToSave[$campoArquivo]);
                }
            }
            unset($dataToSave['credenciais']);

            $dataToSave['trabalho'] = $this->trabalho ?? [];
            $dataToSave['batismo'] = $this->batismo ?? [];
            $dataToSave['preso'] = $this->preso ?? [];

            $credenciado = DB::transaction(function () use ($existente, $dataToSave, &$novosArquivos, &$arquivosParaExcluir) {
                $credenciado = $existente ?: new Credenciado();
                $credenciado->fill($dataToSave);

                foreach (['foto', 'identidade_frente', 'identidade_verso'] as $campo) {
                    if (isset($dataToSave[$campo])) $credenciado->{$campo} = $dataToSave[$campo];
                }

                $credenciado->save();
                $idsMantidos = [];

                foreach ($this->credenciais as $cred) {
                    if (empty($cred['presidio_id'])) continue;

                    $registro = !empty($cred['id'])
                        ? $credenciado->credencialPresidios()->whereKey($cred['id'])->firstOrFail()
                        : new CredencialPresidio(['credenciado_id' => $credenciado->id]);

                    $registro->fill([
                        'presidio_id' => $cred['presidio_id'],
                        'unidade_nao_faz' => (bool) ($cred['unidade_nao_faz'] ?? false),
                        'data_primeira_credencial' => $cred['data_primeira_credencial'] ?? null,
                        'data_renovacao' => $cred['data_renovacao'] ?? null,
                        'data_vencimento' => $cred['data_vencimento'] ?? null,
                    ]);

                    foreach (['foto_frente', 'foto_verso'] as $campoFoto) {
                        $fotoAtual = $registro->{$campoFoto};
                        if ($registro->unidade_nao_faz) {
                            $registro->{$campoFoto} = null;
                            if ($fotoAtual) $arquivosParaExcluir[] = $fotoAtual;
                        } elseif (isset($cred[$campoFoto]) && is_object($cred[$campoFoto])) {
                            $novo = $cred[$campoFoto]->store('credenciados/credencial', 'public_disk');
                            $registro->{$campoFoto} = $novo;
                            $novosArquivos[] = $novo;
                            if ($fotoAtual) $arquivosParaExcluir[] = $fotoAtual;
                        }
                    }

                    $registro->save();
                    $idsMantidos[] = $registro->id;
                }

                $removidas = $credenciado->credencialPresidios()
                    ->when($idsMantidos !== [], fn ($query) => $query->whereNotIn('id', $idsMantidos))
                    ->get();
                foreach ($removidas as $removida) {
                    if ($removida->foto_frente) $arquivosParaExcluir[] = $removida->foto_frente;
                    if ($removida->foto_verso) $arquivosParaExcluir[] = $removida->foto_verso;
                    $removida->delete();
                }

                return $credenciado;
            });

            try {
                Storage::disk('public_disk')->delete(array_values(array_unique(array_filter($arquivosParaExcluir))));
            } catch (\Throwable $storageException) {
                Log::warning('Credenciado salvo, mas houve falha ao limpar arquivos substituídos.', [
                    'credenciado_id' => $credenciado->id,
                    'exception' => $storageException,
                ]);
            }

            session()->flash('message', $this->credenciado_id ? 'Credenciado atualizado com sucesso.' : 'Credenciado cadastrado com sucesso.');
            $this->closeModal();
        } catch (\Throwable $e) {
            Storage::disk('public_disk')->delete(array_values(array_unique(array_filter($novosArquivos))));
            $this->errorMessage = 'Não foi possível salvar o credenciado. Revise os dados e tente novamente.';
            Log::error('Falha ao salvar credenciado.', ['exception' => $e, 'credenciado_id' => $this->credenciado_id]);
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
                    'id' => $cp->id,
                    'presidio_id' => $cp->presidio_id,
                    'foto_frente' => null,
                    'foto_verso' => null,
                    'foto_frente_atual' => $cp->foto_frente,
                    'foto_verso_atual' => $cp->foto_verso,
                    'unidade_nao_faz' => (bool) $cp->unidade_nao_faz,
                    'data_primeira_credencial' => $cp->data_primeira_credencial?->format('Y-m-d'),
                    'data_renovacao' => $cp->data_renovacao?->format('Y-m-d'),
                    'data_vencimento' => $cp->data_vencimento ? $cp->data_vencimento->format('Y-m-d') : null,
                ];
            }

            $this->isOpen = true;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
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
        $hoje = now()->startOfDay();
        $limite = now()->startOfDay()->addDays(30);

        $query = Credenciado::with(['bloco', 'regiao', 'igreja', 'cargo', 'categoria', 'credencialPresidios.presidio'])
            // Restringe rigorosamente para o bloco do usuário logado se ele NÃO for do bloco 21
            ->when($user->bloco_id != 21, function ($q) use ($user) {
                $q->where('bloco_id', $user->bloco_id);
            })
            ->when($this->search, function ($q) {
                $termo = '%' . trim($this->search) . '%';
                $q->where(function ($subquery) use ($termo) {
                    $subquery->where('nome', 'like', $termo)
                        ->orWhere('celular', 'like', $termo)
                        ->orWhere('telefone', 'like', $termo)
                        ->orWhere('email', 'like', $termo)
                        ->orWhereHas('igreja', fn ($igreja) => $igreja->where('nome', 'like', $termo))
                        ->orWhereHas('bloco', fn ($bloco) => $bloco->where('nome', 'like', $termo))
                        ->orWhereHas('regiao', fn ($regiao) => $regiao->where('nome', 'like', $termo))
                        ->orWhereHas('cargo', fn ($cargo) => $cargo->where('nome', 'like', $termo));
                });
            })
            ->when($this->inicio, fn ($q) => $q->whereDate('created_at', '>=', $this->inicio))
            ->when($this->fim, fn ($q) => $q->whereDate('created_at', '<=', $this->fim))
            ->when($user->bloco_id == 21 && $this->filtro_bloco, fn ($q) => $q->where('bloco_id', $this->filtro_bloco))
            ->when($this->filtro_regiao, fn ($q) => $q->where('regiao_id', $this->filtro_regiao))
            ->when($this->filtro_igreja, fn ($q) => $q->where('igreja_id', $this->filtro_igreja))
            ->when($this->filtro_cargo, fn ($q) => $q->where('cargo_id', $this->filtro_cargo))
            ->when($this->filtro_categoria, fn ($q) => $q->where('categoria_id', $this->filtro_categoria))
            ->when($this->filtro_presidio, fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('presidio_id', $this->filtro_presidio)))
            ->when($this->credencial_status === 'com_credencial', fn ($q) => $q->whereHas('credencialPresidios'))
            ->when($this->credencial_status === 'sem_credencial', fn ($q) => $q->whereDoesntHave('credencialPresidios'))
            ->when($this->credencial_status === 'validas', fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('unidade_nao_faz', false)->whereDate('data_vencimento', '>=', $hoje)))
            ->when($this->credencial_status === 'vencendo', fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('unidade_nao_faz', false)->whereBetween('data_vencimento', [$hoje, $limite])))
            ->when($this->credencial_status === 'vencidas', fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('unidade_nao_faz', false)->whereDate('data_vencimento', '<', $hoje)))
            ->when($this->credencial_status === 'sem_validade', fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('unidade_nao_faz', false)->whereNull('data_vencimento')))
            ->when($this->credencial_status === 'unidade_nao_faz', fn ($q) => $q->whereHas('credencialPresidios', fn ($credencial) => $credencial
                ->where('unidade_nao_faz', true)));

        $sortField = in_array($this->sortField, ['nome', 'created_at', 'updated_at'], true)
            ? $this->sortField
            : 'created_at';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $perPage = in_array((int) $this->perPage, [10, 25, 50, 100], true) ? (int) $this->perPage : 10;

        $regioesFiltro = Regiao::query()
            ->when($user->bloco_id != 21, fn ($q) => $q->where('bloco_id', $user->bloco_id))
            ->when($user->bloco_id == 21 && $this->filtro_bloco, fn ($q) => $q->where('bloco_id', $this->filtro_bloco))
            ->orderBy('nome')->get(['id', 'nome']);
        $igrejasFiltro = Igreja::query()
            ->when($user->bloco_id != 21, fn ($q) => $q->where('bloco_id', $user->bloco_id))
            ->when($user->bloco_id == 21 && $this->filtro_bloco, fn ($q) => $q->where('bloco_id', $this->filtro_bloco))
            ->when($this->filtro_regiao, fn ($q) => $q->where('regiao_id', $this->filtro_regiao))
            ->orderBy('nome')->get(['id', 'nome']);

        return view('livewire.universal.credenciados', [
            'results' => $query->orderBy($sortField, $sortDirection)->paginate($perPage),
            'regioesFiltro' => $regioesFiltro,
            'igrejasFiltro' => $igrejasFiltro,
        ]);
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->isOpen = false;
    }

    protected function rules()
    {
        $user = Auth::user();

        $rules = [
            'nome' => 'required|string|min:3|max:250',
            'celular' => ['required', 'digits_between:10,13'],
            'email' => 'nullable|email|max:250|unique:credenciados,email,' . $this->credenciado_id,
            'regiao_id' => ['required', Rule::exists('regiaos', 'id')->where(fn ($query) => $query->where('bloco_id', $this->bloco_id))],
            'igreja_id' => ['required', Rule::exists('igrejas', 'id')->where(fn ($query) => $query
                ->where('bloco_id', $this->bloco_id)->where('regiao_id', $this->regiao_id))],
            'estado_id' => 'required|exists:estados,id',
            'cidade_id' => ['required', Rule::exists('cidades', 'id')->where(fn ($query) => $query->where('estado_id', $this->estado_id))],
            'categoria_id' => 'required|exists:categorias,id',
            'cargo_id' => 'required|exists:cargos,id',
            'grupo_id' => 'required|exists:grupos,id',
            'endereco' => 'required|string|max:250',
            'bairro' => 'required|string|max:250',
            'profissao' => 'nullable|string|max:250',
            'cep' => 'nullable|digits:8',
            'telefone' => 'nullable|digits_between:10,13',
            'aptidoes' => 'nullable|string',
            'conversao' => 'nullable|date',
            'obra' => 'nullable|date',
            'testemunho' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identidade_frente' => 'nullable|image|max:2048',
            'identidade_verso' => 'nullable|image|max:2048',
            'credenciais' => 'array|max:10',
            'credenciais.*.id' => 'nullable|integer',
            'credenciais.*.presidio_id' => 'nullable|distinct|exists:presidios,id',
            'credenciais.*.unidade_nao_faz' => 'boolean',
            'credenciais.*.data_primeira_credencial' => 'nullable|date',
            'credenciais.*.data_renovacao' => 'nullable|date|after_or_equal:credenciais.*.data_primeira_credencial',
            'credenciais.*.data_vencimento' => 'nullable|date|after_or_equal:credenciais.*.data_primeira_credencial',
            'credenciais.*.foto_frente' => 'nullable|image|max:2048',
            'credenciais.*.foto_verso' => 'nullable|image|max:2048',
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
        $this->authorize('create', Credenciado::class);
        $this->resetInputFields();
        // Se não for admin, já fixa o bloco_id do usuário atual ao abrir o modal de criação
        if (Auth::user()->bloco_id != 21) {
            $this->bloco_id = Auth::user()->bloco_id;
        }
        $this->isOpen = true;
    }

    private function resetInputFields()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->credenciais = [];
    }

    private function resetForm(): void
    {
        $this->reset([
            'credenciado_id', 'nome', 'celular', 'telefone', 'email', 'endereco', 'bairro', 'cep',
            'cidade_id', 'estado_id', 'profissao', 'aptidoes', 'conversao', 'obra', 'testemunho',
            'bloco_id', 'regiao_id', 'igreja_id', 'categoria_id', 'cargo_id', 'grupo_id', 'foto',
            'fotoAtual', 'identidade_frente', 'identidade_verso', 'idFrenteAtual', 'idVersoAtual',
            'credenciais', 'trabalho', 'batismo', 'preso', 'cidades', 'regiaos', 'igrejas', 'errorMessage',
        ]);
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

    public function updated($property): void
    {
        if (str_starts_with($property, 'filtro_') || in_array($property, ['credencial_status', 'inicio', 'fim', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function updatedFiltroBloco(): void
    {
        $this->reset('filtro_regiao', 'filtro_igreja');
        $this->resetPage();
    }

    public function updatedFiltroRegiao(): void
    {
        $this->reset('filtro_igreja');
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['nome', 'created_at', 'updated_at'], true)) return;
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function limparFiltroDashboard(): void
    {
        $this->reset(
            'credencial_status', 'inicio', 'fim', 'filtro_bloco', 'filtro_regiao', 'filtro_igreja',
            'filtro_cargo', 'filtro_categoria', 'filtro_presidio'
        );
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
            $cred = Credenciado::with('credencialPresidios')->find($this->confirmDeleteId);
            if ($cred) {
                $this->authorize('delete', $cred);
                $arquivos = collect([$cred->foto, $cred->identidade_frente, $cred->identidade_verso])
                    ->merge($cred->credencialPresidios->pluck('foto_frente'))
                    ->merge($cred->credencialPresidios->pluck('foto_verso'))
                    ->filter()->unique()->values()->all();
                DB::transaction(fn () => $cred->delete());
                try {
                    Storage::disk('public_disk')->delete($arquivos);
                } catch (\Throwable $storageException) {
                    Log::warning('Credenciado removido, mas houve falha ao limpar arquivos.', [
                        'credenciado_id' => $cred->id,
                        'exception' => $storageException,
                    ]);
                }
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
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
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
