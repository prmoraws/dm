<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('politica_espelhos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cidade_id')->unique()->constrained('politica_cidades')->cascadeOnDelete();
            $table->string('presidente_local')->nullable();
            $table->string('indicacao_bispo')->nullable();
            $table->unsignedInteger('filiados_republicanos')->nullable(); // Corrigido
            $table->string('prefeito_atual_nome')->nullable();
            $table->string('prefeito_atual_partido')->nullable();
            $table->unsignedBigInteger('prefeito_atual_votos')->nullable(); // Corrigido
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espelhos');
    }
};
