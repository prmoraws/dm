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
            Schema::create('cidade_candidato', function (Blueprint $table) {
                $table->primary(['cidade_id', 'candidato_id']); // Chave primária composta
                $table->foreignId('cidade_id')->constrained('politica_cidades')->cascadeOnDelete();
                $table->foreignId('candidato_id')->constrained('politica_candidatos')->cascadeOnDelete();
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cidade_candidato_pivot');
    }
};
