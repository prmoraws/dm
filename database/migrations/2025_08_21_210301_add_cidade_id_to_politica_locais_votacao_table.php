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
        Schema::table('politica_locais_votacao', function (Blueprint $table) {
            // Adiciona a coluna cidade_id para otimizar as consultas
            $table->foreignId('cidade_id')
                  ->nullable()
                  ->after('bairro_id') // Posiciona a coluna depois de bairro_id
                  ->constrained('politica_cidades')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('politica_locais_votacao', function (Blueprint $table) {
            //
        });
    }
};
