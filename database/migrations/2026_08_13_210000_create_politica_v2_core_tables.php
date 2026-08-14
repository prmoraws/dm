<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('politica_politicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo');
            $table->string('nome_publico');
            $table->string('slug')->unique();
            $table->date('data_nascimento')->nullable();
            $table->char('uf_nascimento', 2)->nullable();
            $table->string('foto_url', 1024)->nullable();
            $table->text('biografia')->nullable();
            $table->json('links')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('nome_publico', 'idx_politica_politicos_nome_publico');
            $table->index('ativo', 'idx_politica_politicos_ativo');
        });

        Schema::create('politica_partidos', function (Blueprint $table) {
            $table->id();
            $table->string('tse_codigo', 20)->nullable()->unique();
            $table->unsignedSmallInteger('numero')->nullable()->unique();
            $table->string('sigla', 20)->unique();
            $table->string('nome');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('politica_cargos', function (Blueprint $table) {
            $table->id();
            $table->string('tse_codigo', 10)->nullable()->unique();
            $table->string('nome', 100)->unique();
            $table->string('esfera', 20)->nullable();
            $table->string('abrangencia', 20)->nullable();
            $table->unsignedSmallInteger('ordem')->default(100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('politica_eleicoes', function (Blueprint $table) {
            $table->id();
            $table->string('tse_eleicao_codigo', 30)->nullable()->unique();
            $table->string('tse_pleito_codigo', 30)->nullable();
            $table->unsignedSmallInteger('ano');
            $table->unsignedTinyInteger('turno')->default(1);
            $table->string('tipo', 40);
            $table->string('descricao');
            $table->date('data_eleicao')->nullable();
            $table->char('uf', 2)->nullable();
            $table->string('status', 30)->default('planejada');
            $table->timestamp('inicio_apuracao')->nullable();
            $table->timestamp('fim_apuracao')->nullable();
            $table->timestamps();

            $table->index(['ano', 'turno'], 'idx_politica_eleicoes_ano_turno');
            $table->index(['status', 'data_eleicao'], 'idx_politica_eleicoes_status_data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_eleicoes');
        Schema::dropIfExists('politica_cargos');
        Schema::dropIfExists('politica_partidos');
        Schema::dropIfExists('politica_politicos');
    }
};
