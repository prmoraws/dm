<?php

namespace Tests\Feature;

use App\Livewire\Unp\AcompanhamentoCursoUnp;
use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AcompanhamentoCursoUnpTest extends TestCase
{
    use RefreshDatabase;

    private function matricula(): CursoUnpMatricula
    {
        $captacao = CursoUnpCaptacao::query()->create([
            'protocolo' => fake()->uuid(),
            'foto' => 'curso-unp/captacoes/teste.jpg',
            'nome' => 'Aluno da Chamada',
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
            'nome' => 'Turma Chamada',
            'data_inicio' => '2026-10-01',
            'data_fim' => '2026-12-01',
            'dias_horarios' => 'Sábados',
            'local' => 'Salvador',
            'status' => 'em_andamento',
        ]);

        return CursoUnpMatricula::query()->create([
            'captacao_id' => $captacao->id,
            'turma_id' => $turma->id,
            'situacao' => 'matriculado',
            'matriculado_em' => now(),
        ]);
    }

    #[Test]
    public function chamada_salva_presenca_e_inicia_curso_do_aluno(): void
    {
        $matricula = $this->matricula();

        Livewire::test(AcompanhamentoCursoUnp::class)
            ->set('turma_id', $matricula->turma_id)
            ->set('data_aula', '2026-10-10')
            ->set("presencas.{$matricula->id}", 'presente')
            ->call('salvarChamada')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_presencas', [
            'matricula_id' => $matricula->id,
            'situacao' => 'presente',
        ]);
        $presenca = $matricula->presencas()->firstOrFail();
        $this->assertSame('2026-10-10', $presenca->data_aula->toDateString());
        $this->assertDatabaseHas('curso_unp_matriculas', ['id' => $matricula->id, 'situacao' => 'cursando']);
    }

    #[Test]
    public function chamada_rejeita_data_fora_do_periodo_da_turma(): void
    {
        $matricula = $this->matricula();

        Livewire::test(AcompanhamentoCursoUnp::class)
            ->set('turma_id', $matricula->turma_id)
            ->set('data_aula', '2027-01-01')
            ->set("presencas.{$matricula->id}", 'presente')
            ->call('salvarChamada')
            ->assertHasErrors(['data_aula']);

        $this->assertDatabaseCount('curso_unp_presencas', 0);
    }

    #[Test]
    public function gestor_define_resultado_final_manualmente(): void
    {
        $matricula = $this->matricula();

        Livewire::test(AcompanhamentoCursoUnp::class)
            ->set('turma_id', $matricula->turma_id)
            ->call('abrirFinalizacao', $matricula->id)
            ->set('resultado_final', 'aprovado')
            ->set('observacao_final', 'Concluiu satisfatoriamente.')
            ->call('finalizarAluno')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_matriculas', [
            'id' => $matricula->id,
            'situacao' => 'aprovado',
            'observacao_final' => 'Concluiu satisfatoriamente.',
        ]);
    }

    #[Test]
    public function chamada_aceita_falta_justificada(): void
    {
        $matricula = $this->matricula();

        Livewire::test(AcompanhamentoCursoUnp::class)
            ->set('turma_id', $matricula->turma_id)
            ->set('data_aula', '2026-10-17')
            ->set("presencas.{$matricula->id}", 'justificada')
            ->call('salvarChamada')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_presencas', [
            'matricula_id' => $matricula->id,
            'situacao' => 'justificada',
        ]);
    }
}
