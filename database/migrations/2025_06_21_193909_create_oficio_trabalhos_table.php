<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_trabalhos', function (Blueprint $table) {
            $table->id();
            $table->date('data_oficio');
            $table->foreignId('presidio_id')->constrained('presidios')->onDelete('cascade');
            $table->dateTime('dia_hora_evento');
            $table->json('evangelistas');
            $table->text('materiais')->nullable(); // Novo campo
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('oficio_trabalhos');
    }
};
