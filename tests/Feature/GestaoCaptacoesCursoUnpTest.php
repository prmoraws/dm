<?php

namespace Tests\Feature;

use App\Livewire\Unp\GestaoCaptacoesCursoUnp;
use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestaoCaptacoesCursoUnpTest extends TestCase
{
    use RefreshDatabase;

    private function captacao(array $dados = []): CursoUnpCaptacao
    {
        return CursoUnpCaptacao::query()->create(array_merge([
            'protocolo' => fake()->uuid(),
            'foto' => 'curso-unp/captacoes/teste.jpg',
            'nome' => 'Aluno Teste',
            'celular' => '71999999999',
            'batizado_aguas' => false,
            'batizado_espirito_santo' => false,
            'estado_civil' => 'solteiro',
            'casado_civil' => false,
            'casado_igreja' => false,
            'endereco_completo' => 'Rua de Teste, 10, Salvador - BA',
            'mes_ingresso_igreja' => 1,
            'ano_ingresso_igreja' => 2020,
            'status' => 'pendente',
            'lgpd_aceito_em' => now(),
            'ip_hash' => str_repeat('a', 64),
        ], $dados));
    }

    private function turma(array $dados = []): CursoUnpTurma
    {
        return CursoUnpTurma::query()->create(array_merge([
            'nome' => 'Turma 01',
            'data_inicio' => '2026-10-01',
            'data_fim' => '2026-12-01',
            'dias_horarios' => 'Sábados',
            'local' => 'Salvador',
            'status' => 'aberta',
        ], $dados));
    }

    #[Test]
    public function aprovacao_cria_matricula_e_atualiza_captacao(): void
    {
        $captacao = $this->captacao();
        $turma = $this->turma();

        Livewire::test(GestaoCaptacoesCursoUnp::class)
            ->call('abrirAprovacao', $captacao->id)
            ->set('turma_id', $turma->id)
            ->call('aprovar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_captacoes', ['id' => $captacao->id, 'status' => 'aprovado']);
        $this->assertDatabaseHas('curso_unp_matriculas', [
            'captacao_id' => $captacao->id,
            'turma_id' => $turma->id,
            'situacao' => 'matriculado',
        ]);
    }

    #[Test]
    public function turma_lotada_nao_recebe_nova_matricula(): void
    {
        $primeira = $this->captacao(['status' => 'aprovado']);
        $segunda = $this->captacao(['protocolo' => fake()->uuid(), 'celular' => '71888888888']);
        $turma = $this->turma(['limite_alunos' => 1]);
        $turma->matriculas()->create([
            'captacao_id' => $primeira->id,
            'situacao' => 'matriculado',
            'matriculado_em' => now(),
        ]);

        Livewire::test(GestaoCaptacoesCursoUnp::class)
            ->call('abrirAprovacao', $segunda->id)
            ->set('turma_id', $turma->id)
            ->call('aprovar')
            ->assertHasErrors(['turma_id']);

        $this->assertDatabaseHas('curso_unp_captacoes', ['id' => $segunda->id, 'status' => 'pendente']);
    }

    #[Test]
    public function rejeicao_exige_motivo_e_preserva_historico(): void
    {
        $captacao = $this->captacao();

        Livewire::test(GestaoCaptacoesCursoUnp::class)
            ->call('abrirRejeicao', $captacao->id)
            ->set('motivo_rejeicao', 'Não atende aos critérios desta turma.')
            ->call('rejeitar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_captacoes', [
            'id' => $captacao->id,
            'status' => 'rejeitado',
            'motivo_rejeicao' => 'Não atende aos critérios desta turma.',
        ]);
    }
}
