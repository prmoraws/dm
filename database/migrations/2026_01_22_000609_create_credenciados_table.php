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
        Schema::create('credenciados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloco_id')->constrained('blocos');
            $table->foreignId('regiao_id')->constrained('regiaos');
            $table->foreignId('igreja_id')->constrained('igrejas');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('cargo_id')->constrained('cargos');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('cidade_id')->constrained('cidades');
            $table->foreignId('estado_id')->constrained('estados');

            $table->string('nome');
            $table->string('celular');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('endereco');
            $table->string('bairro');
            $table->string('cep')->nullable();
            $table->string('profissao')->nullable();
            $table->text('aptidoes')->nullable();
            $table->date('conversao')->nullable();
            $table->date('obra')->nullable();
            $table->text('testemunho')->nullable();

            // Fotos e Documentos
            $table->string('foto')->nullable();
            $table->string('identidade_frente')->nullable();
            $table->string('identidade_verso')->nullable();

            // Campos de Array (JSON)
            $table->longText('trabalho')->nullable();
            $table->longText('batismo')->nullable();
            $table->longText('preso')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credenciados');
    }
};
