<?php

namespace App\Livewire\Unp;

use App\Models\Universal\Bloco;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AlunosCursoUnp extends Component
{
    use WithPagination;

    public string $search = '';

    public string $situacaoFiltro = '';

    public $turmaFiltro = null;

    public ?int $matriculaId = null;

    public ?int $captacaoId = null;

    public $aluno = null;

    public bool $modalVisualizar = false;

    public bool $modalEditar = false;

    public string $nome = '';

    public string $celular = '';

    public $bloco_id = null;

    public $regiao_id = null;

    public $igreja_id = null;

    public bool $batizado_aguas = false;

    public $data_batismo_aguas = null;

    public bool $batizado_espirito_santo = false;

    public $data_batismo_espirito_santo = null;

    public string $estado_civil = '';

    public bool $casado_civil = false;

    public bool $casado_igreja = false;

    public string $endereco_completo = '';

    public $mes_ingresso_igreja = null;

    public $ano_ingresso_igreja = null;

    public $blocos;

    public $regioes;

    public $igrejas;

    protected $queryString = [
        'search' => ['except' => ''],
        'situacaoFiltro' => ['except' => ''],
        'turmaFiltro' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->blocos = Bloco::query()->orderBy('nome')->get(['id', 'nome']);
        $this->regioes = collect();
        $this->igrejas = collect();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSituacaoFiltro(): void
    {
        $this->resetPage();
    }

    public function updatedTurmaFiltro(): void
    {
        $this->resetPage();
    }

    public function updatedBlocoId($value): void
    {
        $this->regioes = $value
            ? Regiao::query()->where('bloco_id', $value)->orderBy('nome')->get(['id', 'nome'])
            : collect();
        $this->reset(['regiao_id', 'igreja_id']);
        $this->igrejas = collect();
    }

    public function updatedRegiaoId($value): void
    {
        $this->igrejas = $value
            ? Igreja::query()->where('regiao_id', $value)->orderBy('nome')->get(['id', 'nome'])
            : collect();
        $this->reset('igreja_id');
    }

    public function updatedBatizadoAguas($value): void
    {
        if (! $value) {
            $this->data_batismo_aguas = null;
        }
    }

    public function updatedBatizadoEspiritoSanto($value): void
    {
        if (! $value) {
            $this->data_batismo_espirito_santo = null;
        }
    }

    public function updatedEstadoCivil($value): void
    {
        if ($value !== 'casado') {
            $this->casado_civil = false;
            $this->casado_igreja = false;
        }
    }

    public function visualizar(int $matriculaId): void
    {
        $this->aluno = $this->buscarMatricula($matriculaId);
        $this->matriculaId = $this->aluno->id;
        $this->modalVisualizar = true;
    }

    public function editar(int $matriculaId): void
    {
        $matricula = $this->buscarMatricula($matriculaId);
        $captacao = $matricula->captacao;

        $this->matriculaId = $matricula->id;
        $this->captacaoId = $captacao->id;
        $this->nome = $captacao->nome;
        $this->celular = $captacao->celular;
        $this->bloco_id = $captacao->bloco_id;
        $this->regioes = $this->bloco_id
            ? Regiao::query()->where('bloco_id', $this->bloco_id)->orderBy('nome')->get(['id', 'nome'])
            : collect();
        $this->regiao_id = $captacao->regiao_id;
        $this->igrejas = $this->regiao_id
            ? Igreja::query()->where('regiao_id', $this->regiao_id)->orderBy('nome')->get(['id', 'nome'])
            : collect();
        $this->igreja_id = $captacao->igreja_id;
        $this->batizado_aguas = (bool) $captacao->batizado_aguas;
        $this->data_batismo_aguas = $captacao->data_batismo_aguas?->format('Y-m-d');
        $this->batizado_espirito_santo = (bool) $captacao->batizado_espirito_santo;
        $this->data_batismo_espirito_santo = $captacao->data_batismo_espirito_santo?->format('Y-m-d');
        $this->estado_civil = $captacao->estado_civil;
        $this->casado_civil = (bool) $captacao->casado_civil;
        $this->casado_igreja = (bool) $captacao->casado_igreja;
        $this->endereco_completo = $captacao->endereco_completo;
        $this->mes_ingresso_igreja = $captacao->mes_ingresso_igreja;
        $this->ano_ingresso_igreja = $captacao->ano_ingresso_igreja;
        $this->modalVisualizar = false;
        $this->modalEditar = true;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $dados = $this->validate($this->rules());
        $captacao = CursoUnpCaptacao::query()->findOrFail($this->captacaoId);
        unset($dados['captacaoId']);
        $dados['nome'] = str($dados['nome'])->trim()->title()->toString();
        $dados['celular'] = preg_replace('/\D+/', '', $dados['celular']);
        $dados['data_batismo_aguas'] = $dados['batizado_aguas'] ? $dados['data_batismo_aguas'] : null;
        $dados['data_batismo_espirito_santo'] = $dados['batizado_espirito_santo']
            ? $dados['data_batismo_espirito_santo']
            : null;

        if ($dados['estado_civil'] !== 'casado') {
            $dados['casado_civil'] = false;
            $dados['casado_igreja'] = false;
        }

        $captacao->update($dados);
        $this->fecharModais();
        session()->flash('message', 'Dados do aluno atualizados com sucesso.');
    }

    public function fecharModais(): void
    {
        $this->reset([
            'matriculaId', 'captacaoId', 'aluno', 'modalVisualizar', 'modalEditar',
            'nome', 'celular', 'bloco_id', 'regiao_id', 'igreja_id',
            'batizado_aguas', 'data_batismo_aguas', 'batizado_espirito_santo',
            'data_batismo_espirito_santo', 'estado_civil', 'casado_civil',
            'casado_igreja', 'endereco_completo', 'mes_ingresso_igreja',
            'ano_ingresso_igreja',
        ]);
        $this->regioes = collect();
        $this->igrejas = collect();
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'captacaoId' => ['required', 'exists:curso_unp_captacoes,id'],
            'nome' => ['required', 'string', 'min:3', 'max:255'],
            'celular' => [
                'required', 'string', 'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $numero = preg_replace('/\D+/', '', (string) $value);
                    if (! in_array(strlen($numero), [10, 11], true)) {
                        $fail('Informe um celular com DDD válido.');
                    }
                    if (CursoUnpCaptacao::query()
                        ->where('celular', $numero)
                        ->where('id', '!=', $this->captacaoId)
                        ->exists()) {
                        $fail('Já existe outro cadastro com este celular.');
                    }
                },
            ],
            'bloco_id' => ['nullable', 'exists:blocos,id'],
            'regiao_id' => [
                'nullable',
                Rule::exists('regiaos', 'id')->where(
                    fn ($query) => $query->where('bloco_id', $this->bloco_id)
                ),
            ],
            'igreja_id' => [
                'nullable',
                Rule::exists('igrejas', 'id')->where(
                    fn ($query) => $query->where('regiao_id', $this->regiao_id)
                ),
            ],
            'batizado_aguas' => ['required', 'boolean'],
            'data_batismo_aguas' => ['nullable', Rule::requiredIf(fn () => $this->batizado_aguas), 'date', 'before_or_equal:today'],
            'batizado_espirito_santo' => ['required', 'boolean'],
            'data_batismo_espirito_santo' => ['nullable', Rule::requiredIf(fn () => $this->batizado_espirito_santo), 'date', 'before_or_equal:today'],
            'estado_civil' => ['required', Rule::in(['solteiro', 'casado', 'viuvo', 'divorciado', 'namorando'])],
            'casado_civil' => ['required', 'boolean'],
            'casado_igreja' => ['required', 'boolean'],
            'endereco_completo' => ['required', 'string', 'min:10', 'max:1000'],
            'mes_ingresso_igreja' => ['required', 'integer', 'between:1,12'],
            'ano_ingresso_igreja' => ['required', 'integer', 'min:1900', 'max:'.now()->year],
        ];
    }

    private function buscarMatricula(int $matriculaId): CursoUnpMatricula
    {
        return CursoUnpMatricula::query()
            ->with([
                'captacao.bloco', 'captacao.regiao', 'captacao.igreja',
                'turma.instrutor', 'presencas' => fn ($query) => $query->orderByDesc('data_aula'),
            ])
            ->findOrFail($matriculaId);
    }

    public function render()
    {
        $alunos = CursoUnpMatricula::query()
            ->join('curso_unp_captacoes', 'curso_unp_captacoes.id', '=', 'curso_unp_matriculas.captacao_id')
            ->select('curso_unp_matriculas.*')
            ->with(['captacao.bloco', 'captacao.igreja', 'turma'])
            ->withCount([
                'presencas',
                'presencas as presentes_count' => fn ($query) => $query->where('situacao', 'presente'),
                'presencas as ausentes_count' => fn ($query) => $query->where('situacao', 'ausente'),
                'presencas as justificadas_count' => fn ($query) => $query->where('situacao', 'justificada'),
            ])
            ->when($this->search, function ($query): void {
                $termo = '%'.trim($this->search).'%';
                $query->whereHas('captacao', function ($captacao) use ($termo): void {
                    $captacao->where(function ($subquery) use ($termo): void {
                        $subquery->where('nome', 'like', $termo)
                            ->orWhere('celular', 'like', $termo)
                            ->orWhereHas('igreja', fn ($igreja) => $igreja->where('nome', 'like', $termo));
                    });
                });
            })
            ->when($this->turmaFiltro, fn ($query) => $query->where('turma_id', $this->turmaFiltro))
            ->when($this->situacaoFiltro, fn ($query) => $query->where('situacao', $this->situacaoFiltro))
            ->orderBy('curso_unp_captacoes.nome')
            ->paginate(12);

        return view('livewire.unp.alunos-curso-unp', [
            'alunos' => $alunos,
            'turmas' => CursoUnpTurma::query()->orderByDesc('data_inicio')->get(['id', 'nome']),
        ]);
    }
}
