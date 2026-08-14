<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Garante que o schema restaurado/limpo tenha as coordenadas usadas
     * pelo mapa V2 sem tentar recriar colunas já existentes em produção.
     */
    public function up(): void
    {
        if (! Schema::hasTable('politica_cidades')) {
            return;
        }

        $needsLatitude = ! Schema::hasColumn('politica_cidades', 'latitude');
        $needsLongitude = ! Schema::hasColumn('politica_cidades', 'longitude');

        if (! $needsLatitude && ! $needsLongitude) {
            return;
        }

        Schema::table('politica_cidades', function (Blueprint $table) use ($needsLatitude, $needsLongitude) {
            if ($needsLatitude) {
                $table->decimal('latitude', 10, 8)->nullable();
            }

            if ($needsLongitude) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    /**
     * Conservador por design: essas colunas já existiam em bancos restaurados
     * antes desta migration. Não as removemos em rollback para evitar perda
     * de coordenadas preexistentes.
     */
    public function down(): void
    {
        // Intencionalmente sem operação destrutiva.
    }
};
