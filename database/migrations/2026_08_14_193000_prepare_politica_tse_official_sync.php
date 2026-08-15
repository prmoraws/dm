<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('politica_cidades', function (Blueprint $table) {
            if (! Schema::hasColumn('politica_cidades', 'tse_codigo')) {
                $table->string('tse_codigo', 12)->nullable()->after('ibge_code');
                $table->index('tse_codigo', 'idx_politica_cidades_tse_codigo');
            }
        });

        Schema::table('politica_politicos', function (Blueprint $table) {
            if (! Schema::hasColumn('politica_politicos', 'identidade_publica_hash')) {
                $table->char('identidade_publica_hash', 64)->nullable()->after('slug');
                $table->index('identidade_publica_hash', 'idx_politica_politicos_identidade_hash');
            }
        });

        if (! Schema::hasTable('politica_tse_importacoes')) {
            Schema::create('politica_tse_importacoes', function (Blueprint $table) {
                $table->id();
                $table->string('execucao', 36)->unique();
                $table->unsignedSmallInteger('ano');
                $table->char('uf', 2)->default('BA');
                $table->string('tipo', 30);
                $table->string('escopo', 30)->default('espelho');
                $table->string('status', 20)->default('executando');
                $table->string('arquivo', 1024)->nullable();
                $table->char('sha256', 64)->nullable();
                $table->unsignedBigInteger('linhas_lidas')->default(0);
                $table->unsignedBigInteger('linhas_selecionadas')->default(0);
                $table->unsignedBigInteger('inseridos')->default(0);
                $table->unsignedBigInteger('atualizados')->default(0);
                $table->unsignedBigInteger('ignorados')->default(0);
                $table->timestamp('iniciada_em')->nullable();
                $table->timestamp('concluida_em')->nullable();
                $table->text('ultimo_erro')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['ano', 'uf', 'tipo', 'status'], 'idx_politica_tse_importacoes_ano_uf_tipo');
                $table->index('concluida_em', 'idx_politica_tse_importacoes_concluida');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_tse_importacoes');

        // As colunas de identificação territorial/pública podem ter sido preenchidas
        // por fontes oficiais. O rollback preserva esses dados deliberadamente.
    }
};
