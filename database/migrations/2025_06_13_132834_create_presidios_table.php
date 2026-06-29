<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presidios', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255)->index();
            $table->string('diretor', 255);
            $table->string('contato_diretor', 255);
            $table->string('adjunto', 255)->nullable();
            $table->string('contato_adjunto', 255)->nullable();
            $table->string('laborativa', 255)->nullable();
            $table->string('contato_laborativa', 255)->nullable();
            $table->text('visita')->nullable();
            $table->text('interno')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presidios');
    }
};