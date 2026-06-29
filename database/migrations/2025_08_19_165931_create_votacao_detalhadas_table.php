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
        Schema::create('politica_votacao_detalhada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_votacao_id')->constrained('politica_locais_votacao')->cascadeOnDelete();
            $table->foreignId('candidato_id')->constrained('politica_candidatos')->cascadeOnDelete();
            $table->year('ano_eleicao');
            $table->string('cargo');
            $table->integer('votos_recebidos');
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index(['ano_eleicao', 'cargo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votacao_detalhadas');
    }
};
