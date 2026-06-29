<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            // Adiciona a chave estrangeira para a tabela de cidades do módulo de política
            $table->foreignId('cidade_id')
                  ->nullable()
                  ->after('id') // Opcional: posiciona a coluna no banco de dados
                  ->constrained('politica_cidades')
                  ->nullOnDelete(); // Se uma cidade for removida, o campo fica nulo

            // Adiciona a coluna para o número de membros
            $table->integer('numero_membros')->default(0)->after('nome');
        });
    }
};
