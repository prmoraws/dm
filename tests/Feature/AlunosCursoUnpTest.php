<?php

namespace Tests\Feature;

use App\Livewire\Unp\AlunosCursoUnp;
use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpPresenca;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlunosCursoUnpTest extends TestCase
{
    use RefreshDatabase;

    private function matricula(): CursoUnpMatricula
    {
        $captacao = CursoUnpCaptacao::query()->create([
            'protocolo' => fake()->uuid(),
            'foto' => 'curso-unp/captacoes/teste.jpg',
            'nome' => 'Maria da Silva',
            'celular' => '71999999999',
            'batizado_aguas' => false,
            'batizado_espirito_santo' => false,
            'estado_civil' => 'solteiro',
            'casado_civil' => false,
            'casado_igreja' => false,
            'endereco_completo' => 'Rua de Teste, Salvador - BA',
            'mes_ingresso_igreja' => 1,
            'ano_ingresso_igreja' => 2020,
            'status' => 'aprovado',
            'lgpd_aceito_em' => now(),
            'ip_hash' => str_repeat('a', 64),
        ]);

        $turma = CursoUnpTurma::query()->create([
            'nome' => 'Turma Salvador',
            'data_inicio' => '2026-10-01',
            'data_fim' => '2026-12-01',
            'dias_horarios' => 'Sábados',
            'local' => 'Salvador',
            'status' => 'em_andamento',
        ]);

        return CursoUnpMatricula::query()->create([
            'captacao_id' => $captacao->id,
            'turma_id' => $turma->id,
            'situacao' => 'cursando',
            'matriculado_em' => now(),
        ]);
    }

    #[Test]
    public function lista_exibe_aluno_turma_e_frequencia(): void
    {
        $matricula = $this->matricula();
        CursoUnpPresenca::query()->create([
            'matricula_id' => $matricula->id,
            'data_aula' => '2026-10-10',
            'situacao' => 'presente',
        ]);
        CursoUnpPresenca::query()->create([
            'matricula_id' => $matricula->id,
            'data_aula' => '2026-10-17',
            'situacao' => 'ausente',
        ]);

        Livewire::test(AlunosCursoUnp::class)
            ->assertSee('Maria da Silva')
            ->assertSee('Turma Salvador')
            ->assertSee('50%');
    }

    #[Test]
    public function detalhe_exibe_historico_de_presencas(): void
    {
        $matricula = $this->matricula();
        CursoUnpPresenca::query()->create([
            'matricula_id' => $matricula->id,
            'data_aula' => '2026-10-10',
            'situacao' => 'justificada',
            'observacao' => 'Apresentou justificativa.',
        ]);

        Livewire::test(AlunosCursoUnp::class)
            ->call('visualizar', $matricula->id)
            ->assertSet('modalVisualizar', true)
            ->assertSee('Histórico de presenças')
            ->assertSee('Apresentou justificativa.');
    }

    #[Test]
    public function gestor_edita_dados_sem_alterar_matricula_ou_presencas(): void
    {
        $matricula = $this->matricula();
        CursoUnpPresenca::query()->create([
            'matricula_id' => $matricula->id,
            'data_aula' => '2026-10-10',
            'situacao' => 'presente',
        ]);

        Livewire::test(AlunosCursoUnp::class)
            ->call('editar', $matricula->id)
            ->set('nome', 'Maria Souza')
            ->set('celular', '71988887777')
            ->call('salvar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_captacoes', [
            'id' => $matricula->captacao_id,
            'nome' => 'Maria Souza',
            'celular' => '71988887777',
        ]);
        $this->assertDatabaseHas('curso_unp_matriculas', ['id' => $matricula->id]);
        $this->assertDatabaseHas('curso_unp_presencas', ['matricula_id' => $matricula->id]);
    }

    #[Test]
    public function filtros_localizam_aluno_por_turma_e_situacao(): void
    {
        $matricula = $this->matricula();

        Livewire::test(AlunosCursoUnp::class)
            ->set('turmaFiltro', $matricula->turma_id)
            ->set('situacaoFiltro', 'cursando')
            ->set('search', 'Maria')
            ->assertSee('Maria da Silva');
    }
}
