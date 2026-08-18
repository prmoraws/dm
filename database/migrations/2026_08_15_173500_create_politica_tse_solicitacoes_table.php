<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('politica_tse_solicitacoes')) {
            return;
        }

        Schema::create('politica_tse_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('ano');
            $table->char('uf', 2)->default('BA');
            $table->string('escopo', 30)->default('espelho');
            $table->string('somente', 30)->default('candidaturas');
            $table->string('origem', 20)->default('manual');
            $table->string('status', 20)->default('pendente');
            $table->unsignedBigInteger('solicitante_user_id')->nullable();
            $table->timestamp('solicitada_em')->nullable();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->text('ultimo_erro')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'solicitada_em'], 'idx_politica_tse_solicitacoes_status');
            $table->index(['ano', 'uf', 'somente'], 'idx_politica_tse_solicitacoes_recorte');
            $table->index('solicitante_user_id', 'idx_politica_tse_solicitacoes_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('politica_tse_solicitacoes');
    }
};
