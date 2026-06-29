<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('captacao_pessoas', function (Blueprint $table) {
            $table->id();

            // Campos replicados da tabela 'pessoas' (tornados nulos para flexibilidade no formulário público)
            $table->string('nome');
            $table->string('celular');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep')->nullable();
            $table->string('profissao')->nullable();
            $table->text('aptidoes')->nullable();
            $table->date('conversao')->nullable();
            $table->date('obra')->nullable();
            $table->text('testemunho')->nullable();
            $table->string('foto')->nullable();

            // Chaves estrangeiras (também nulas para preenchimento posterior pelo admin, se necessário)
            $table->foreignId('bloco_id')->nullable()->constrained('blocos')->onDelete('set null');
            $table->foreignId('regiao_id')->nullable()->constrained('regiaos')->onDelete('set null');
            $table->foreignId('igreja_id')->nullable()->constrained('igrejas')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('set null');
            $table->foreignId('cargo_id')->nullable()->constrained('cargos')->onDelete('set null');
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->onDelete('set null');
            $table->foreignId('cidade_id')->nullable()->constrained('cidades')->onDelete('set null');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null');

            // Campos JSON para checkboxes (replicados da tabela 'pessoas')
            $table->json('trabalho')->nullable();
            $table->json('batismo')->nullable();
            $table->json('preso')->nullable();

            // --- Campos de controle para o fluxo de captação ---
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('motivo_rejeicao')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('revisado_em')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captacao_pessoas');
    }
};