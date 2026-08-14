<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica_espelho_inteligencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
            $table->foreignId('eleicao_id')->nullable()->constrained('politica_eleicoes')->nullOnDelete();
            $table->foreignId('candidatura_id')->nullable()->constrained('politica_candidaturas')->nullOnDelete();
            $table->foreignId('responsavel_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('contexto_chave', 100)->default('geral');
            $table->string('classificacao', 40)->default('monitoramento');
            $table->unsignedTinyInteger('prioridade')->default(3);
            $table->unsignedBigInteger('meta_votos')->nullable();
            $table->decimal('meta_percentual', 8, 4)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('revisado_em')->nullable();
            $table->timestamps();

            $table->unique(['cidade_id', 'contexto_chave'], 'uq_politica_espelho_inteligencia_contexto');
            $table->index(['classificacao', 'prioridade'], 'idx_politica_espelho_inteligencia_classificacao');
            $table->index(['eleicao_id', 'candidatura_id', 'prioridade'], 'idx_politica_espelho_inteligencia_eleicao_candidato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_espelho_inteligencia');
    }
};
