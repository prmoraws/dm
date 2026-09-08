<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso_unp_turmas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('dias_horarios');
            $table->string('local');
            $table->foreignId('instrutor_id')->nullable()->constrained('instrutores')->nullOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('limite_alunos')->nullable();
            $table->string('status', 30)->default('planejamento');
            $table->string('link_whatsapp', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'data_inicio'], 'idx_curso_unp_turmas_status_inicio');
        });

        Schema::create('curso_unp_captacoes', function (Blueprint $table) {
            $table->id();
            $table->uuid('protocolo')->unique();
            $table->foreignId('bloco_id')->nullable()->constrained('blocos')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('regiao_id')->nullable()->constrained('regiaos')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('igreja_id')->nullable()->constrained('igrejas')->nullOnDelete()->cascadeOnUpdate();
            $table->string('foto');
            $table->string('nome');
            $table->string('celular', 20);
            $table->boolean('batizado_aguas')->default(false);
            $table->date('data_batismo_aguas')->nullable();
            $table->boolean('batizado_espirito_santo')->default(false);
            $table->date('data_batismo_espirito_santo')->nullable();
            $table->string('estado_civil', 30);
            $table->boolean('casado_civil')->default(false);
            $table->boolean('casado_igreja')->default(false);
            $table->text('endereco_completo');
            $table->unsignedTinyInteger('mes_ingresso_igreja');
            $table->unsignedSmallInteger('ano_ingresso_igreja');
            $table->string('status', 30)->default('pendente');
            $table->timestamp('lgpd_aceito_em');
            $table->string('ip_hash', 64);
            $table->string('user_agent', 500)->nullable();
            $table->text('motivo_rejeicao')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_em')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'idx_curso_unp_captacoes_status_data');
            $table->index('nome', 'idx_curso_unp_captacoes_nome');
            $table->index('celular', 'idx_curso_unp_captacoes_celular');
        });

        Schema::create('curso_unp_matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('captacao_id')->constrained('curso_unp_captacoes')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('turma_id')->constrained('curso_unp_turmas')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('situacao', 30)->default('matriculado');
            $table->timestamp('matriculado_em');
            $table->foreignId('matriculado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalizado_em')->nullable();
            $table->foreignId('finalizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacao_final')->nullable();
            $table->timestamps();

            $table->unique(['captacao_id', 'turma_id'], 'uq_curso_unp_matricula_captacao_turma');
            $table->index(['turma_id', 'situacao'], 'idx_curso_unp_matriculas_turma_situacao');
        });

        Schema::create('curso_unp_presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('curso_unp_matriculas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('data_aula');
            $table->string('situacao', 20);
            $table->text('observacao')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['matricula_id', 'data_aula'], 'uq_curso_unp_presenca_matricula_data');
            $table->index(['data_aula', 'situacao'], 'idx_curso_unp_presencas_data_situacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_unp_presencas');
        Schema::dropIfExists('curso_unp_matriculas');
        Schema::dropIfExists('curso_unp_captacoes');
        Schema::dropIfExists('curso_unp_turmas');
    }
};
