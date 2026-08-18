<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('politica_filiacoes', function (Blueprint $table) {
            if (! Schema::hasColumn('politica_filiacoes', 'ano_inicio')) {
                $table->unsignedSmallInteger('ano_inicio')->nullable()->after('data_fim');
            }
            if (! Schema::hasColumn('politica_filiacoes', 'ano_fim')) {
                $table->unsignedSmallInteger('ano_fim')->nullable()->after('ano_inicio');
            }
            if (! Schema::hasColumn('politica_filiacoes', 'periodo_texto')) {
                $table->string('periodo_texto', 80)->nullable()->after('ano_fim');
            }
            if (! Schema::hasColumn('politica_filiacoes', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('periodo_texto');
            }
            if (! Schema::hasColumn('politica_filiacoes', 'fonte_url')) {
                $table->string('fonte_url', 1024)->nullable()->after('fonte_id');
            }
            if (! Schema::hasColumn('politica_filiacoes', 'fonte_oficial')) {
                $table->boolean('fonte_oficial')->default(false)->after('fonte_url');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'ano_inicio', 'ano_fim', 'periodo_texto', 'observacoes', 'fonte_url', 'fonte_oficial',
        ];

        $existing = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn('politica_filiacoes', $column)
        ));

        if ($existing !== []) {
            Schema::table('politica_filiacoes', fn (Blueprint $table) => $table->dropColumn($existing));
        }
    }
};
