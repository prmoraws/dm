<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('politica_candidaturas') || Schema::hasColumn('politica_candidaturas', 'tse_chave_historica')) {
            return;
        }

        Schema::table('politica_candidaturas', function (Blueprint $table): void {
            $table->string('tse_chave_historica', 64)->nullable()->after('tse_sq_candidato');
            $table->unique(
                ['eleicao_id', 'tse_chave_historica'],
                'uq_politica_candidaturas_eleicao_hist'
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('politica_candidaturas') || ! Schema::hasColumn('politica_candidaturas', 'tse_chave_historica')) {
            return;
        }

        Schema::table('politica_candidaturas', function (Blueprint $table): void {
            $table->dropUnique('uq_politica_candidaturas_eleicao_hist');
            $table->dropColumn('tse_chave_historica');
        });
    }
};
