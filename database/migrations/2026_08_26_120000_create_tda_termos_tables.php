<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('captacao_tdas', function (Blueprint $table) {
            $table->string('endereco_igreja')->nullable()->after('igreja_id');
            $table->string('nacionalidade')->nullable()->after('nome');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('assinatura')->nullable()->after('foto');
        });

        Schema::table('cadastro_tdas', function (Blueprint $table) {
            $table->foreignId('captacao_tda_id')->nullable()->unique()->after('id')
                ->constrained('captacao_tdas')->nullOnDelete();
            $table->string('endereco_igreja')->nullable()->after('igreja_id');
            $table->string('nacionalidade')->nullable()->after('nome');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('assinatura')->nullable()->after('foto');
        });

        Schema::create('tda_responsaveis_legais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('captacao_tda_id')->nullable()->unique()
                ->constrained('captacao_tdas')->nullOnDelete();
            $table->foreignId('cadastro_tda_id')->nullable()->unique()
                ->constrained('cadastro_tdas')->nullOnDelete();
            $table->string('nome');
            $table->string('nacionalidade');
            $table->string('estado_civil');
            $table->string('profissao');
            $table->string('rg', 30);
            $table->string('cpf', 20);
            $table->string('endereco');
            $table->string('numero', 30);
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cep', 10);
            $table->foreignId('estado_id')->constrained('estados')->restrictOnDelete();
            $table->foreignId('cidade_id')->constrained('cidades')->restrictOnDelete();
            $table->date('data_nascimento');
            $table->timestamps();
        });

        Schema::create('tda_termo_aceites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('captacao_tda_id')->nullable()
                ->constrained('captacao_tdas')->nullOnDelete();
            $table->foreignId('cadastro_tda_id')->nullable()
                ->constrained('cadastro_tdas')->nullOnDelete();
            $table->string('tipo', 40);
            $table->string('versao', 20);
            $table->char('hash_documento', 64);
            $table->char('hash_assinatura', 64);
            $table->timestamp('aceito_em');
            $table->char('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->json('dados_snapshot');
            $table->string('pdf_assinado')->nullable();
            $table->timestamp('revogado_em')->nullable();
            $table->text('motivo_revogacao')->nullable();
            $table->timestamps();

            $table->unique(['captacao_tda_id', 'tipo'], 'uq_tda_termo_captacao_tipo');
            $table->unique(['cadastro_tda_id', 'tipo'], 'uq_tda_termo_cadastro_tipo');
            $table->index(['tipo', 'aceito_em'], 'idx_tda_termo_tipo_aceite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tda_termo_aceites');
        Schema::dropIfExists('tda_responsaveis_legais');

        Schema::table('cadastro_tdas', function (Blueprint $table) {
            $table->dropForeign(['captacao_tda_id']);
            $table->dropColumn([
                'captacao_tda_id', 'endereco_igreja', 'nacionalidade',
                'complemento', 'assinatura',
            ]);
        });

        Schema::table('captacao_tdas', function (Blueprint $table) {
            $table->dropColumn([
                'endereco_igreja', 'nacionalidade', 'complemento', 'assinatura',
            ]);
        });
    }
};
