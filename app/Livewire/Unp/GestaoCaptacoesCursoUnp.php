<?php

namespace App\Livewire\Unp;

use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class GestaoCaptacoesCursoUnp extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'pendente';
    public $selecionado = null;
    public bool $modalVisualizar = false;
    public bool $modalAprovar = false;
    public bool $modalRejeitar = false;
    public ?int $acaoId = null;
    public $turma_id = null;
    public string $motivo_rejeicao = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pendente'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function visualizar(int $id): void
    {
        $this->selecionado = CursoUnpCaptacao::query()
            ->with(['bloco', 'regiao', 'igreja', 'matriculas.turma'])
            ->findOrFail($id);
        $this->modalVisualizar = true;
    }

    public function abrirAprovacao(int $id): void
    {
        $captacao = CursoUnpCaptacao::query()->where('status', 'pendente')->findOrFail($id);
        $this->acaoId = $captacao->id;
        $this->turma_id = null;
        $this->selecionado = $captacao;
        $this->modalAprovar = true;
        $this->resetErrorBag();
    }

    public function aprovar(): void
    {
        $this->validate([
            'acaoId' => ['required', 'exists:curso_unp_captacoes,id'],
            'turma_id' => ['required', 'exists:curso_unp_turmas,id'],
        ]);

        try {
            DB::transaction(function (): void {
                $captacao = CursoUnpCaptacao::query()
                    ->lockForUpdate()
                    ->findOrFail($this->acaoId);

                if ($captacao->status !== 'pendente') {
                    throw new \DomainException('Esta inscrição já foi analisada.');
                }

                $turma = CursoUnpTurma::query()
                    ->lockForUpdate()
                    ->findOrFail($this->turma_id);

                if (! in_array($turma->status, ['aberta', 'em_andamento'], true)) {
                    throw new \DomainException('Selecione uma turma aberta ou em andamento.');
                }

                if ($turma->limite_alunos !== null
                    && $turma->matriculas()->count() >= $turma->limite_alunos) {
                    throw new \DomainException('A turma selecionada atingiu o limite de alunos.');
                }

                CursoUnpMatricula::query()->create([
                    'captacao_id' => $captacao->id,
                    'turma_id' => $turma->id,
                    'situacao' => 'matriculado',
                    'matriculado_em' => now(),
                    'matriculado_por' => auth()->id(),
                ]);

                $captacao->update([
                    'status' => 'aprovado',
                    'motivo_rejeicao' => null,
                    'revisado_por' => auth()->id(),
                    'revisado_em' => now(),
                ]);
            });

            $this->fecharModais();
            session()->flash('message', 'Inscrição aprovada e aluno matriculado na turma.');
        } catch (\DomainException $e) {
            $this->addError('turma_id', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Falha ao aprovar captação do Curso UNP', [
                'tipo' => $e::class,
                'mensagem' => $e->getMessage(),
                'captacao_id' => $this->acaoId,
            ]);
            session()->flash('error', 'Não foi possível concluir a aprovação.');
        }
    }

    public function abrirRejeicao(int $id): void
    {
        $captacao = CursoUnpCaptacao::query()->where('status', 'pendente')->findOrFail($id);
        $this->acaoId = $captacao->id;
        $this->motivo_rejeicao = '';
        $this->selecionado = $captacao;
        $this->modalRejeitar = true;
        $this->resetErrorBag();
    }

    public function rejeitar(): void
    {
        $this->validate([
            'acaoId' => ['required', 'exists:curso_unp_captacoes,id'],
            'motivo_rejeicao' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $alterados = CursoUnpCaptacao::query()
            ->whereKey($this->acaoId)
            ->where('status', 'pendente')
            ->update([
                'status' => 'rejeitado',
                'motivo_rejeicao' => trim($this->motivo_rejeicao),
                'revisado_por' => auth()->id(),
                'revisado_em' => now(),
            ]);

        if ($alterados !== 1) {
            $this->addError('motivo_rejeicao', 'Esta inscrição já foi analisada.');
            return;
        }

        $this->fecharModais();
        session()->flash('message', 'Inscrição rejeitada.');
    }

    public function excluir(int $id): void
    {
        $captacao = CursoUnpCaptacao::query()
            ->withCount('matriculas')
            ->findOrFail($id);

        if ($captacao->matriculas_count > 0 || $captacao->status === 'aprovado') {
            session()->flash('error', 'Inscrições aprovadas ou com matrícula não podem ser excluídas.');
            return;
        }

        $foto = $captacao->foto;
        $captacao->delete();

        if ($foto) {
            Storage::disk('public_disk')->delete($foto);
        }

        session()->flash('message', 'Inscrição excluída.');
    }

    public function fecharModais(): void
    {
        $this->reset([
            'selecionado', 'modalVisualizar', 'modalAprovar', 'modalRejeitar',
            'acaoId', 'turma_id', 'motivo_rejeicao',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $captacoes = CursoUnpCaptacao::query()
            ->with(['bloco', 'igreja', 'matriculas.turma'])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query): void {
                $termo = '%'.trim($this->search).'%';
                $query->where(function ($subquery) use ($termo): void {
                    $subquery->where('nome', 'like', $termo)
                        ->orWhere('celular', 'like', $termo)
                        ->orWhere('protocolo', 'like', $termo)
                        ->orWhereHas('igreja', fn ($igreja) => $igreja->where('nome', 'like', $termo));
                });
            })
            ->latest()
            ->paginate(10);

        $turmas = CursoUnpTurma::query()
            ->whereIn('status', ['aberta', 'em_andamento'])
            ->withCount('matriculas')
            ->orderBy('data_inicio')
            ->get();

        return view('livewire.unp.gestao-captacoes-curso-unp', compact('captacoes', 'turmas'));
    }
}
