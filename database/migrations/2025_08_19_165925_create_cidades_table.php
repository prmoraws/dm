<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('politica_cidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('ibge_code')->unique()->nullable();
            $table->unsignedBigInteger('populacao')->nullable(); // Corrigido
            $table->unsignedBigInteger('votos_validos_ultima_eleicao')->nullable(); // Corrigido
            $table->unsignedInteger('cadeiras_camara')->nullable(); // Corrigido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cidades');
    }
};
