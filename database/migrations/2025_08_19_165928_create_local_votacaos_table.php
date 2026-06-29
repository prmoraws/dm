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
        Schema::create('politica_locais_votacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bairro_id')->constrained('politica_bairros')->cascadeOnDelete();
            $table->string('nome');
            $table->string('endereco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_votacaos');
    }
};
