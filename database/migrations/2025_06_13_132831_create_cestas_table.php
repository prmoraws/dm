<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cestas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('terreiro', 255);
            $table->string('contato', 255);
            $table->integer('cestas');
            $table->string('observacao', 255)->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cestas');
    }
};