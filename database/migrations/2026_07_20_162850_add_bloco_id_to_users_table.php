<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna permitindo nulo inicialmente para não quebrar usuários existentes
            $table->unsignedBigInteger('bloco_id')->nullable()->after('password');

            // Cria a chave estrangeira referenciando a tabela blocos
            $table->foreign('bloco_id')->references('id')->on('blocos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bloco_id']);
            $table->dropColumn('bloco_id');
        });
    }
};
