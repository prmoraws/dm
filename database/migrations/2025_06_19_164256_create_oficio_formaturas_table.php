<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_formaturas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('data_oficio');
            // Chaves estrangeiras para Presidio e Curso (Assunto)
            $table->foreignId('presidio_id')->constrained('presidios')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->dateTime('dia_hora_evento');
            $table->text('materiais');
            $table->text('comunicacao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_formaturas');
    }
};
