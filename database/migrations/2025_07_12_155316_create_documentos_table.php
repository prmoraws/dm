<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->enum('classe', ['oficio', 'lista', 'doação', 'outros']);
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->json('upload')->nullable(); // Para múltiplos arquivos
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
