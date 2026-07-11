<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batismos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('bloco_id')->constrained('blocos')->onDelete('cascade');
            $table->foreignId('presidio_id')->constrained('presidios')->onDelete('cascade');
            $table->integer('quantidade')->unsigned()->default(1); // Campo adicionado aqui diretamente!
            $table->date('data_batismo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batismos');
    }
};