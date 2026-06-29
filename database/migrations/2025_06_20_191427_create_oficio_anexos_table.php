<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_anexos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->date('data');
            $table->json('esposas')->nullable();
            $table->json('convidados')->nullable();
            $table->json('comunicacao')->nullable();
            $table->json('organizacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oficio_anexos');
    }
};
