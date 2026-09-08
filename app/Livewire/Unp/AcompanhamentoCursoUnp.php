<?php

namespace App\Livewire\Unp;

use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpPresenca;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AcompanhamentoCursoUnp extends Component
{
    public $turma_id = null;

    public string $data_aula = '';

    public array $presencas = [];

    public array $observacoes = [];

    public ?int $matriculaFinalizacaoId = null;

    public string $resultado_final = '';

    public string $observacao_final = '';

    public bool $modalFinalizacao = false;

    public function mount(): void
    {
        $this->data_aula = now()->format('Y-m-d');
    }

    public function updatedTurmaId(): void
    {
        $this->carregarChamada();
    }

    public function updatedDataAula(): void
    {
        $this->carregarChamada();
    }

    public function carregarChamada(): void
    {
        $this->presencas = [];
        $this->observacoes = [];

        if (! $this->turma_id || ! $this->data_aula) {
            return;
        }

        $registros = CursoUnpPresenca::query()
            ->whereDate('data_aula', $this->data_aula)
            ->whereHas('matricula', fn ($query) => $query->where('turma_id', $this->turma_id))
            ->get();

        foreach ($registros as $registro) {
            $this->presencas[$registro->matricula_id] = $registro->situacao;
            $this->observacoes[$registro->matricula_id] = (string) $registro->observacao;
        }
    }

    public function marcarTodos(string $situacao): void
    {
        abort_unless(in_array($situacao, ['presente', 'ausente', 'justificada'], true), 422);

        foreach ($this->matriculasAtivas()->pluck('id') as $matriculaId) {
            $this->presencas[$matriculaId] = $situacao;
        }
    }

    public function salvarChamada(): void
    {
        $this->validate([
            'turma_id' => ['required', 'exists:curso_unp_turmas,id'],
            'data_aula' => ['required', 'date'],
        ]);

        $turma = CursoUnpTurma::query()->findOrFail($this->turma_id);

        if ($this->data_aula < $turma->data_inicio->format('Y-m-d')
            || $this->data_aula > $turma->data_fim->format('Y-m-d')) {
            $this->addError('data_aula', 'A data da aula deve estar dentro do período da turma.');

            return;
        }

        $matriculas = $this->matriculasAtivas()->get();

        if ($matriculas->isEmpty()) {
            $this->addError('turma_id', 'Esta turma não possui alunos ativos.');

            return;
        }

        foreach ($matriculas as $matricula) {
            $this->validate([
                "presencas.{$matricula->id}" => ['required', Rule::in(['presente', 'ausente', 'justificada'])],
                "observacoes.{$matricula->id}" => ['nullable', 'string', 'max:500'],
            ], [
                "presencas.{$matricula->id}.required" => "Marque a presença de {$matricula->captacao->nome}.",
            ]);
        }

        DB::transaction(function () use ($matriculas): void {
            foreach ($matriculas as $matricula) {
                CursoUnpPresenca::query()->updateOrCreate(
                    [
                        'matricula_id' => $matricula->id,
                        'data_aula' => $this->data_aula,
                    ],
                    [
                        'situacao' => $this->presencas[$matricula->id],
                        'observacao' => filled($this->observacoes[$matricula->id] ?? null)
                            ? trim($this->observacoes[$matricula->id])
                            : null,
                        'registrado_por' => auth()->id(),
                    ]
                );

                if ($matricula->situacao === 'matriculado') {
                    $matricula->update(['situacao' => 'cursando']);
                }
            }
        });

        session()->flash('message', 'Chamada salva com sucesso.');
        $this->carregarChamada();
    }

    public function abrirFinalizacao(int $matriculaId): void
    {
        $matricula = CursoUnpMatricula::query()
            ->where('turma_id', $this->turma_id)
            ->with('captacao')
            ->findOrFail($matriculaId);

        $this->matriculaFinalizacaoId = $matricula->id;
        $this->resultado_final = in_array($matricula->situacao, ['aprovado', 'reprovado', 'desistente'], true)
            ? $matricula->situacao
            : '';
        $this->observacao_final = (string) $matricula->observacao_final;
        $this->modalFinalizacao = true;
        $this->resetErrorBag();
    }

    public function finalizarAluno(): void
    {
        $this->validate([
            'matriculaFinalizacaoId' => ['required', 'exists:curso_unp_matriculas,id'],
            'resultado_final' => ['required', Rule::in(['aprovado', 'reprovado', 'desistente'])],
            'observacao_final' => ['nullable', 'string', 'max:1000'],
        ]);

        $matricula = CursoUnpMatricula::query()
            ->where('turma_id', $this->turma_id)
            ->findOrFail($this->matriculaFinalizacaoId);

        $matricula->update([
            'situacao' => $this->resultado_final,
            'observacao_final' => filled($this->observacao_final) ? trim($this->observacao_final) : null,
            'finalizado_em' => now(),
            'finalizado_por' => auth()->id(),
        ]);

        $this->fecharFinalizacao();
        session()->flash('message', 'Resultado final registrado.');
    }

    public function reabrirAluno(int $matriculaId): void
    {
        $matricula = CursoUnpMatricula::query()
            ->where('turma_id', $this->turma_id)
            ->whereIn('situacao', ['aprovado', 'reprovado', 'desistente'])
            ->findOrFail($matriculaId);

        $matricula->update([
            'situacao' => $matricula->presencas()->exists() ? 'cursando' : 'matriculado',
            'observacao_final' => null,
            'finalizado_em' => null,
            'finalizado_por' => null,
        ]);

        session()->flash('message', 'Aluno reaberto para acompanhamento.');
    }

    public function fecharFinalizacao(): void
    {
        $this->reset([
            'matriculaFinalizacaoId', 'resultado_final', 'observacao_final', 'modalFinalizacao',
        ]);
        $this->resetErrorBag();
    }

    private function matriculasAtivas()
    {
        return CursoUnpMatricula::query()
            ->where('turma_id', $this->turma_id)
            ->whereIn('situacao', ['matriculado', 'cursando'])
            ->with('captacao');
    }

    public function render()
    {
        $turmas = CursoUnpTurma::query()
            ->whereNotIn('status', ['cancelada'])
            ->orderByDesc('data_inicio')
            ->get();

        $matriculas = collect();
        if ($this->turma_id) {
            $matriculas = CursoUnpMatricula::query()
                ->where('turma_id', $this->turma_id)
                ->with(['captacao', 'presencas'])
                ->get()
                ->sortBy(fn ($matricula) => $matricula->captacao->nome)
                ->values();
        }

        return view('livewire.unp.acompanhamento-curso-unp', compact('turmas', 'matriculas'));
    }
}
