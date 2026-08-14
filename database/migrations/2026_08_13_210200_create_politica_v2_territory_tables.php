<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica_zonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero');
            $table->string('tse_codigo', 20)->nullable();
            $table->timestamps();

            $table->unique(['cidade_id', 'numero'], 'uq_politica_zonas_cidade_numero');
        });

        Schema::create('politica_secoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('politica_zonas')->cascadeOnDelete();
            $table->foreignId('local_votacao_id')->nullable()->constrained('politica_locais_votacao')->nullOnDelete();
            $table->unsignedInteger('numero');
            $table->unsignedInteger('eleitores_aptos')->nullable();
            $table->string('situacao', 30)->nullable();
            $table->timestamps();

            $table->unique(['eleicao_id', 'zona_id', 'numero'], 'uq_politica_secoes_eleicao_zona_numero');
            $table->index(['eleicao_id', 'local_votacao_id'], 'idx_politica_secoes_eleicao_local');
        });

        Schema::create('politica_resultados_municipais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('candidatura_id')->constrained('politica_candidaturas')->cascadeOnDelete();
            $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
            $table->unsignedBigInteger('votos')->default(0);
            $table->decimal('percentual', 8, 4)->nullable();
            $table->unsignedInteger('posicao')->nullable();
            $table->unsignedInteger('secoes_total')->nullable();
            $table->unsignedInteger('secoes_totalizadas')->nullable();
            $table->unsignedBigInteger('eleitores')->nullable();
            $table->unsignedBigInteger('comparecimento')->nullable();
            $table->unsignedBigInteger('abstencoes')->nullable();
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();

            $table->unique(['eleicao_id', 'candidatura_id', 'cidade_id'], 'uq_politica_resultados_municipais');
            $table->index(['eleicao_id', 'cidade_id', 'votos'], 'idx_politica_resultados_municipais_ranking');
            $table->index(['candidatura_id', 'votos'], 'idx_politica_resultados_municipais_candidato_votos');
        });

        Schema::create('politica_resultados_zonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('candidatura_id')->constrained('politica_candidaturas')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('politica_zonas')->cascadeOnDelete();
            $table->unsignedBigInteger('votos')->default(0);
            $table->decimal('percentual', 8, 4)->nullable();
            $table->unsignedInteger('secoes_total')->nullable();
            $table->unsignedInteger('secoes_totalizadas')->nullable();
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();

            $table->unique(['eleicao_id', 'candidatura_id', 'zona_id'], 'uq_politica_resultados_zonas');
            $table->index(['eleicao_id', 'zona_id', 'votos'], 'idx_politica_resultados_zonas_ranking');
        });

        Schema::create('politica_resultados_secoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('candidatura_id')->constrained('politica_candidaturas')->cascadeOnDelete();
            $table->foreignId('secao_id')->constrained('politica_secoes')->cascadeOnDelete();
            $table->unsignedInteger('votos')->default(0);
            $table->timestamps();

            $table->unique(['eleicao_id', 'candidatura_id', 'secao_id'], 'uq_politica_resultados_secoes');
            $table->index(['eleicao_id', 'secao_id'], 'idx_politica_resultados_secoes_eleicao_secao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_resultados_secoes');
        Schema::dropIfExists('politica_resultados_zonas');
        Schema::dropIfExists('politica_resultados_municipais');
        Schema::dropIfExists('politica_secoes');
        Schema::dropIfExists('politica_zonas');
    }
};
