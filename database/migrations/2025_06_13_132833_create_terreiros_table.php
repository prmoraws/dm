<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terreiros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255)->unique();
            $table->string('contato', 255);
            $table->string('bairro', 255);
            $table->string('terreiro', 255);
            $table->string('convidados', 255);
            $table->string('onibus', 255);
            $table->string('bloco', 255);
            $table->string('iurd', 255);
            $table->string('pastor', 255);
            $table->string('telefone', 255);
            $table->string('endereco', 255);
            $table->string('localização', 255);
            $table->boolean('confirmado')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terreiros');
    }
};