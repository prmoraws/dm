<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['captacao_tdas', 'cadastro_tdas'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table): void {
                $table->string('testemunha_nome')->nullable()->after('assinatura');
                $table->string('testemunha_rg', 30)->nullable()->after('testemunha_nome');
                $table->string('testemunha_assinatura')->nullable()->after('testemunha_rg');
            });
        }
    }

    public function down(): void
    {
        foreach (['captacao_tdas', 'cadastro_tdas'] as $tabela) {
            Schema::table($tabela, function (Blueprint $table): void {
                $table->dropColumn(['testemunha_nome', 'testemunha_rg', 'testemunha_assinatura']);
            });
        }
    }
};
