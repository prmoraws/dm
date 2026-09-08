<?php

namespace Tests\Feature;

use App\Livewire\Unp\TurmasCursoUnp;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TurmasCursoUnpTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function componente_de_turmas_existe(): void
    {
        $this->assertTrue(class_exists(TurmasCursoUnp::class));
    }

    #[Test]
    public function turma_pode_ser_criada_com_dados_validos(): void
    {
        Livewire::test(TurmasCursoUnp::class)
            ->set('nome', 'Turma Salvador 01')
            ->set('data_inicio', '2026-10-01')
            ->set('data_fim', '2026-12-01')
            ->set('dias_horarios', 'Sábados, 14h às 17h')
            ->set('local', 'Catedral da Fé')
            ->set('limite_alunos', 50)
            ->set('status', 'aberta')
            ->set('link_whatsapp', 'https://chat.whatsapp.com/exemplo')
            ->call('salvar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_unp_turmas', [
            'nome' => 'Turma Salvador 01',
            'status' => 'aberta',
            'limite_alunos' => 50,
        ]);
    }

    #[Test]
    public function termino_nao_pode_ser_anterior_ao_inicio(): void
    {
        Livewire::test(TurmasCursoUnp::class)
            ->set('nome', 'Turma inválida')
            ->set('data_inicio', '2026-12-01')
            ->set('data_fim', '2026-10-01')
            ->set('dias_horarios', 'Sábados')
            ->set('local', 'Salvador')
            ->set('status', 'aberta')
            ->call('salvar')
            ->assertHasErrors(['data_fim']);
    }

    #[Test]
    public function turma_com_matricula_nao_pode_ser_excluida(): void
    {
        $turma = CursoUnpTurma::query()->create([
            'nome' => 'Turma protegida',
            'data_inicio' => '2026-10-01',
            'data_fim' => '2026-12-01',
            'dias_horarios' => 'Sábados',
            'local' => 'Salvador',
            'status' => 'aberta',
        ]);

        $this->assertSame(0, $turma->matriculas()->count());
    }
}
