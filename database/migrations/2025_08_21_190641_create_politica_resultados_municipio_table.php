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
            Schema::create('politica_resultados_municipio', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
                $table->foreignId('candidato_id')->constrained('politica_candidatos')->cascadeOnDelete();
                $table->year('ano_eleicao');
                $table->string('cargo');
                $table->integer('votos_recebidos');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('politica_resultados_municipio');
    }
};
