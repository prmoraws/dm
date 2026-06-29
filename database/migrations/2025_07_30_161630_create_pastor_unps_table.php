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
        Schema::create('pastor_unps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloco_id')->constrained('blocos')->onDelete('cascade');
            $table->foreignId('regiao_id')->constrained('regiaos')->onDelete('cascade');
            $table->string('nome', 255);
            $table->date('nascimento')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('whatsapp', 255);
            $table->string('telefone', 255)->nullable();
            $table->enum('cargo', ['responsavel', 'auxiliar']);
            $table->string('chegada', 255)->nullable();
            $table->date('entrada')->nullable();
            $table->boolean('preso')->default(false);
            $table->json('trabalho')->nullable();
            $table->string('nome_esposa', 255)->nullable();
            $table->string('email_esposa', 255)->nullable();
            $table->string('whatsapp_esposa', 255)->nullable();
            $table->string('telefone_esposa', 255)->nullable();
            $table->string('obra', 255)->nullable();
            $table->string('casado', 255)->nullable(); // Corrigido aqui
            $table->boolean('consagrada_esposa')->nullable();
            $table->boolean('preso_esposa')->nullable();
            $table->json('trabalho_esposa')->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('foto_esposa', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pastor_unps');
    }
};
