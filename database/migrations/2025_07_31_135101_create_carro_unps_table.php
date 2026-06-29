<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carro_unps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pastor_unp_id')->constrained('pastor_unps')->onDelete('cascade');
            $table->foreignId('bloco_id')->constrained('blocos')->onDelete('cascade');
            $table->string('modelo');
            $table->string('ano');
            $table->string('placa')->unique();
            $table->integer('km')->nullable();
            $table->text('demanda')->nullable();
            $table->string('foto_frente');
            $table->string('foto_tras');
            $table->string('foto_direita')->nullable();
            $table->string('foto_esquerda')->nullable();
            $table->string('foto_dentro')->nullable();
            $table->string('foto_cambio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carro_unps');
    }
};