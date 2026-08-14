<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('politica_candidaturas', function (Blueprint $table) {
            $table->foreignId('cidade_id')
                ->nullable()
                ->after('uf')
                ->constrained('politica_cidades')
                ->nullOnDelete();
            $table->string('origem', 30)->default('manual')->after('cidade_id');
            $table->unsignedBigInteger('legacy_candidato_id')->nullable()->after('origem')->index();
            $table->string('origem_chave', 191)->nullable()->after('legacy_candidato_id')->unique();

            $table->index(
                ['eleicao_id', 'cargo_id', 'cidade_id'],
                'idx_politica_candidaturas_eleicao_cargo_cidade'
            );
        });

        Schema::create('politica_espelho_operacional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->unique()->constrained('politica_cidades')->cascadeOnDelete();
            $table->unsignedBigInteger('legacy_espelho_id')->nullable()->unique();
            $table->string('presidente_local')->nullable();
            $table->string('indicacao_bispo')->nullable();
            $table->unsignedInteger('filiados_republicanos')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('dados_publicos_legados')->nullable();
            $table->timestamp('revisado_em')->nullable();
            $table->timestamps();
        });

        Schema::create('politica_migracoes_dados', function (Blueprint $table) {
            $table->id();
            $table->string('chave', 120)->unique();
            $table->string('status', 30)->default('pendente');
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->json('estatisticas')->nullable();
            $table->text('ultimo_erro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_migracoes_dados');
        Schema::dropIfExists('politica_espelho_operacional');

        Schema::table('politica_candidaturas', function (Blueprint $table) {
            $table->dropIndex('idx_politica_candidaturas_eleicao_cargo_cidade');
            $table->dropUnique(['origem_chave']);
            $table->dropForeign(['cidade_id']);
            $table->dropColumn(['cidade_id', 'origem', 'legacy_candidato_id', 'origem_chave']);
        });
    }
};
