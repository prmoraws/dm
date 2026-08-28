<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $colunas = [
            'unidade_nao_faz' => fn (Blueprint $table) => $table->boolean('unidade_nao_faz')->default(false)->after('foto_verso'),
            'data_primeira_credencial' => fn (Blueprint $table) => $table->date('data_primeira_credencial')->nullable()->after('unidade_nao_faz'),
            'data_renovacao' => fn (Blueprint $table) => $table->date('data_renovacao')->nullable()->after('data_primeira_credencial'),
            'data_vencimento' => fn (Blueprint $table) => $table->date('data_vencimento')->nullable()->after('data_renovacao'),
        ];

        foreach ($colunas as $nome => $adicionar) {
            if (! Schema::hasColumn('credencial_presidios', $nome)) {
                Schema::table('credencial_presidios', $adicionar);
            }
        }
    }

    public function down(): void
    {
        $colunas = array_values(array_filter(
            ['data_primeira_credencial', 'data_renovacao'],
            fn (string $coluna) => Schema::hasColumn('credencial_presidios', $coluna)
        ));

        if ($colunas !== []) {
            Schema::table('credencial_presidios', fn (Blueprint $table) => $table->dropColumn($colunas));
        }
    }
};
