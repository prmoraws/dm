<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('captacoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Campo do passo 1
            $table->string('cpf')->unique(); // Campo do passo 1
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->json('payload'); // Armazenará todos os outros dados do formulário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captacoes');
    }
};