<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica_fontes_estado', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 191)->unique();
            $table->string('fonte', 40);
            $table->string('tipo_arquivo', 40)->nullable();
            $table->text('url')->nullable();
            $table->string('etag', 191)->nullable();
            $table->string('last_modified', 191)->nullable();
            $table->string('idg', 191)->nullable();
            $table->timestamp('ultimo_check_em')->nullable();
            $table->timestamp('ultima_alteracao_em')->nullable();
            $table->timestamp('ultimo_sucesso_em')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('erros_consecutivos')->default(0);
            $table->text('ultimo_erro')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['fonte', 'tipo_arquivo'], 'idx_politica_fontes_estado_fonte_tipo');
            $table->index('ultimo_sucesso_em', 'idx_politica_fontes_estado_sucesso');
        });

        Schema::create('politica_apuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleicao_id')->constrained('politica_eleicoes')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('politica_cargos')->restrictOnDelete();
            $table->string('abrangencia_tipo', 20);
            $table->string('abrangencia_chave', 80);
            $table->char('uf', 2)->nullable();
            $table->foreignId('cidade_id')->nullable()->constrained('politica_cidades')->nullOnDelete();
            $table->foreignId('zona_id')->nullable()->constrained('politica_zonas')->nullOnDelete();
            $table->string('tse_idg', 191)->nullable();
            $table->timestamp('gerado_tse_em')->nullable();
            $table->string('status', 30)->default('aguardando');
            $table->unsignedInteger('secoes_total')->nullable();
            $table->unsignedInteger('secoes_totalizadas')->nullable();
            $table->unsignedInteger('secoes_nao_totalizadas')->nullable();
            $table->decimal('percentual_secoes', 8, 4)->nullable();
            $table->unsignedBigInteger('eleitores_total')->nullable();
            $table->unsignedBigInteger('comparecimento')->nullable();
            $table->unsignedBigInteger('abstencoes')->nullable();
            $table->boolean('totalizacao_final')->default(false);
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();

            $table->unique(['eleicao_id', 'cargo_id', 'abrangencia_chave'], 'uq_politica_apuracoes_abrangencia');
            $table->index(['eleicao_id', 'cargo_id', 'status'], 'idx_politica_apuracoes_eleicao_cargo_status');
            $table->index(['uf', 'cidade_id'], 'idx_politica_apuracoes_uf_cidade');
        });

        Schema::create('politica_apuracao_candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apuracao_id')->constrained('politica_apuracoes')->cascadeOnDelete();
            $table->foreignId('candidatura_id')->constrained('politica_candidaturas')->cascadeOnDelete();
            $table->unsignedInteger('posicao')->nullable();
            $table->unsignedBigInteger('votos')->default(0);
            $table->decimal('percentual', 8, 4)->nullable();
            $table->string('situacao', 50)->nullable();
            $table->boolean('eleito')->default(false);
            $table->timestamp('sincronizado_em')->nullable();
            $table->timestamps();

            $table->unique(['apuracao_id', 'candidatura_id'], 'uq_politica_apuracao_candidaturas');
            $table->index(['apuracao_id', 'posicao'], 'idx_politica_apuracao_candidaturas_posicao');
        });

        Schema::create('politica_apuracao_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apuracao_id')->constrained('politica_apuracoes')->cascadeOnDelete();
            $table->foreignId('candidatura_id')->constrained('politica_candidaturas')->cascadeOnDelete();
            $table->timestamp('capturado_em');
            $table->unsignedBigInteger('votos')->default(0);
            $table->decimal('percentual', 8, 4)->nullable();
            $table->decimal('percentual_secoes', 8, 4)->nullable();
            $table->timestamps();

            $table->index(['apuracao_id', 'capturado_em'], 'idx_politica_apuracao_historico_apuracao_tempo');
            $table->index(['candidatura_id', 'capturado_em'], 'idx_politica_apuracao_historico_candidato_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_apuracao_historico');
        Schema::dropIfExists('politica_apuracao_candidaturas');
        Schema::dropIfExists('politica_apuracoes');
        Schema::dropIfExists('politica_fontes_estado');
    }
};
