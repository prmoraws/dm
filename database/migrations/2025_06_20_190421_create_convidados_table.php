<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convidados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf')->unique();
            $table->string('rg')->nullable(); // RG não é obrigatório
            $table->enum('classe', ['esposas', 'convidados', 'comunicação', 'organização']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convidados');
    }
};