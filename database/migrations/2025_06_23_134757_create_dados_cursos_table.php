<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dados_cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Nome identificador para este registro de dados
            $table->foreignId('presidio_id')->constrained('presidios')->onDelete('cascade');
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('instrutor_id')->constrained('instrutores')->onDelete('cascade');
            $table->foreignId('informacao_curso_id')->constrained('informacao_cursos')->onDelete('cascade');
            $table->date('inicio');
            $table->date('fim');
            $table->string('carga');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dados_cursos');
    }
};
