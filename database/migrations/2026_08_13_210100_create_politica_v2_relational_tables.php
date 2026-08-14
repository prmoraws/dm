<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica_candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('politico_id')->constrained('politica_politicos')->cascadeOnDelete();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('politica_cargos')->restrictOnDelete();
            $table->foreignId('partido_id')->nullable()->constrained('politica_partidos')->nullOnDelete();
            $table->string('tse_sq_candidato', 30)->nullable();
            $table->string('numero_urna', 10)->nullable();
            $table->string('nome_urna')->nullable();
            $table->char('uf', 2)->nullable();
            $table->string('situacao_registro', 50)->nullable();
            $table->string('situacao_eleicao', 50)->nullable();
            $table->string('coligacao')->nullable();
            $table->string('federacao')->nullable();
            $table->unsignedBigInteger('votos_total')->default(0);
            $table->decimal('percentual_total', 8, 4)->nullable();
            $table->boolean('eleito')->default(false);
            $table->boolean('segundo_turno')->default(false);
            $table->string('foto_url', 1024)->nullable();
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();

            $table->unique(['eleicao_id', 'tse_sq_candidato'], 'uq_politica_candidaturas_eleicao_sq');
            $table->index(['eleicao_id', 'cargo_id', 'uf'], 'idx_politica_candidaturas_eleicao_cargo_uf');
            $table->index(['politico_id', 'eleicao_id'], 'idx_politica_candidaturas_politico_eleicao');
            $table->index(['eleicao_id', 'votos_total'], 'idx_politica_candidaturas_eleicao_votos');
        });

        Schema::create('politica_filiacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('politico_id')->constrained('politica_politicos')->cascadeOnDelete();
            $table->foreignId('partido_id')->constrained('politica_partidos')->restrictOnDelete();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('fonte')->nullable();
            $table->string('fonte_id')->nullable();
            $table->timestamps();

            $table->index(['politico_id', 'data_inicio'], 'idx_politica_filiacoes_politico_inicio');
        });

        Schema::create('politica_mandatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('politico_id')->constrained('politica_politicos')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('politica_cargos')->restrictOnDelete();
            $table->foreignId('partido_id')->nullable()->constrained('politica_partidos')->nullOnDelete();
            $table->foreignId('eleicao_origem_id')->nullable()->constrained('politica_eleicoes')->nullOnDelete();
            $table->string('fonte_id')->nullable();
            $table->string('legislatura', 50)->nullable();
            $table->string('esfera', 20)->nullable();
            $table->char('uf', 2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('situacao', 40)->default('ativo');
            $table->string('fonte')->nullable();
            $table->timestamps();

            $table->index(['politico_id', 'situacao'], 'idx_politica_mandatos_politico_situacao');
            $table->index(['cargo_id', 'uf'], 'idx_politica_mandatos_cargo_uf');
        });

        Schema::create('politica_acompanhamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('politico_id')->unique()->constrained('politica_politicos')->cascadeOnDelete();
            $table->string('grupo', 40);
            $table->unsignedTinyInteger('prioridade')->default(1);
            $table->unsignedSmallInteger('ordem')->default(100);
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['grupo', 'ativo', 'ordem'], 'idx_politica_acompanhamentos_grupo_ativo_ordem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_acompanhamentos');
        Schema::dropIfExists('politica_mandatos');
        Schema::dropIfExists('politica_filiacoes');
        Schema::dropIfExists('politica_candidaturas');
    }
};
