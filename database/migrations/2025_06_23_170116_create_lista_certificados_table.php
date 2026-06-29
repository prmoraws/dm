<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_certificados', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Nome da lista (Ex: "Certificados - Refrigeração 2025")
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->json('reeducandos'); // Armazenará os IDs dos reeducandos selecionados
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lista_certificados');
    }
};
