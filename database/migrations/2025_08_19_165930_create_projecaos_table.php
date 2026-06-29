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
        Schema::create('politica_projecoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
            $table->foreignId('candidato_id')->constrained('politica_candidatos')->cascadeOnDelete();
            $table->year('ano_eleicao_futura');
            $table->integer('meta_votos_coeficiente')->nullable();
            $table->integer('votos_garantidos_igreja')->nullable();
            $table->integer('votos_faltantes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projecaos');
    }
};
