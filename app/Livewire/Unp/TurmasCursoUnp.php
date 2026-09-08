<?php

namespace App\Livewire\Unp;

use App\Models\Unp\CursoUnpTurma;
use App\Models\Unp\Instrutor;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TurmasCursoUnp extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFiltro = '';

    public ?int $turmaId = null;

    public string $nome = '';

    public $data_inicio = null;

    public $data_fim = null;

    public string $dias_horarios = '';

    public string $local = '';

    public $instrutor_id = null;

    public $limite_alunos = null;

    public string $status = 'planejamento';

    public string $link_whatsapp = '';

    public bool $modalAberto = false;

    public ?int $confirmarExclusaoId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFiltro' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFiltro(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:255'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
            'dias_horarios' => ['required', 'string', 'max:255'],
            'local' => ['required', 'string', 'max:255'],
            'instrutor_id' => ['nullable', 'exists:instrutores,id'],
            'limite_alunos' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', Rule::in(['planejamento', 'aberta', 'em_andamento', 'encerrada', 'cancelada'])],
            'link_whatsapp' => ['nullable', 'url:http,https', 'max:500'],
        ];
    }

    public function criar(): void
    {
        $this->limparFormulario();
        $this->modalAberto = true;
    }

    public function editar(int $id): void
    {
        $turma = CursoUnpTurma::query()->findOrFail($id);

        $this->turmaId = $turma->id;
        $this->nome = $turma->nome;
        $this->data_inicio = $turma->data_inicio?->format('Y-m-d');
        $this->data_fim = $turma->data_fim?->format('Y-m-d');
        $this->dias_horarios = $turma->dias_horarios;
        $this->local = $turma->local;
        $this->instrutor_id = $turma->instrutor_id;
        $this->limite_alunos = $turma->limite_alunos;
        $this->status = $turma->status;
        $this->link_whatsapp = (string) $turma->link_whatsapp;
        $this->modalAberto = true;
    }

    public function salvar(): void
    {
        $dados = $this->validate();
        $dados['link_whatsapp'] = filled($dados['link_whatsapp'] ?? null)
            ? trim($dados['link_whatsapp'])
            : null;

        CursoUnpTurma::query()->updateOrCreate(
            ['id' => $this->turmaId],
            $dados
        );

        session()->flash('message', $this->turmaId ? 'Turma atualizada com sucesso.' : 'Turma criada com sucesso.');
        $this->fecharModal();
    }

    public function confirmarExclusao(int $id): void
    {
        $this->confirmarExclusaoId = $id;
    }

    public function excluir(): void
    {
        if (! $this->confirmarExclusaoId) {
            return;
        }

        $turma = CursoUnpTurma::query()->withCount('matriculas')->findOrFail($this->confirmarExclusaoId);

        if ($turma->matriculas_count > 0) {
            session()->flash('error', 'Esta turma possui matrículas e não pode ser excluída. Altere o status para cancelada.');
            $this->confirmarExclusaoId = null;

            return;
        }

        $turma->delete();
        $this->confirmarExclusaoId = null;
        session()->flash('message', 'Turma excluída com sucesso.');
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limparFormulario();
    }

    private function limparFormulario(): void
    {
        $this->reset([
            'turmaId', 'nome', 'data_inicio', 'data_fim', 'dias_horarios',
            'local', 'instrutor_id', 'limite_alunos', 'link_whatsapp',
        ]);
        $this->status = 'planejamento';
        $this->resetErrorBag();
    }

    public function render()
    {
        $turmas = CursoUnpTurma::query()
            ->with('instrutor')
            ->withCount('matriculas')
            ->when($this->search, function ($query): void {
                $termo = '%'.trim($this->search).'%';
                $query->where(function ($subquery) use ($termo): void {
                    $subquery->where('nome', 'like', $termo)
                        ->orWhere('local', 'like', $termo)
                        ->orWhereHas('instrutor', fn ($instrutor) => $instrutor->where('nome', 'like', $termo));
                });
            })
            ->when($this->statusFiltro, fn ($query) => $query->where('status', $this->statusFiltro))
            ->orderByDesc('data_inicio')
            ->paginate(10);

        return view('livewire.unp.turmas-curso-unp', [
            'turmas' => $turmas,
            'instrutores' => Instrutor::query()->orderBy('nome')->get(['id', 'nome']),
        ]);
    }
}
