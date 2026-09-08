<?php

namespace Tests\Feature;

use App\Models\Unp\CursoUnpCaptacao;
use App\Models\Unp\CursoUnpMatricula;
use App\Models\Unp\CursoUnpPresenca;
use App\Models\Unp\CursoUnpTurma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CursoUnpEstruturaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tabelas_do_curso_unp_sao_criadas_sem_alterar_tabela_cursos_existente(): void
    {
        $this->assertTrue(Schema::hasTable('cursos'));
        $this->assertTrue(Schema::hasTable('curso_unp_turmas'));
        $this->assertTrue(Schema::hasTable('curso_unp_captacoes'));
        $this->assertTrue(Schema::hasTable('curso_unp_matriculas'));
        $this->assertTrue(Schema::hasTable('curso_unp_presencas'));
    }

    #[Test]
    public function models_usam_tabelas_exclusivas_do_novo_modulo(): void
    {
        $this->assertSame('curso_unp_turmas', (new CursoUnpTurma)->getTable());
        $this->assertSame('curso_unp_captacoes', (new CursoUnpCaptacao)->getTable());
        $this->assertSame('curso_unp_matriculas', (new CursoUnpMatricula)->getTable());
        $this->assertSame('curso_unp_presencas', (new CursoUnpPresenca)->getTable());
    }

    #[Test]
    public function presenca_e_unica_por_matricula_e_data(): void
    {
        $indices = Schema::getIndexes('curso_unp_presencas');
        $this->assertTrue(collect($indices)->contains(
            fn (array $index) => ($index['name'] ?? null) === 'uq_curso_unp_presenca_matricula_data'
                && (bool) ($index['unique'] ?? false)
        ));
    }
}
