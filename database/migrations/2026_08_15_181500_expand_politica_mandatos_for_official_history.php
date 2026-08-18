<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('politica_mandatos', function (Blueprint $table) {
            if (! Schema::hasColumn('politica_mandatos', 'tipo')) {
                $table->string('tipo', 30)->default('mandato')->after('fonte_id');
            }
            if (! Schema::hasColumn('politica_mandatos', 'ano_inicio')) {
                $table->unsignedSmallInteger('ano_inicio')->nullable()->after('uf');
            }
            if (! Schema::hasColumn('politica_mandatos', 'ano_fim')) {
                $table->unsignedSmallInteger('ano_fim')->nullable()->after('ano_inicio');
            }
            if (! Schema::hasColumn('politica_mandatos', 'periodo_texto')) {
                $table->string('periodo_texto', 80)->nullable()->after('ano_fim');
            }
            if (! Schema::hasColumn('politica_mandatos', 'detalhes')) {
                $table->text('detalhes')->nullable()->after('situacao');
            }
            if (! Schema::hasColumn('politica_mandatos', 'fonte_url')) {
                $table->string('fonte_url', 1024)->nullable()->after('fonte');
            }
            if (! Schema::hasColumn('politica_mandatos', 'fonte_oficial')) {
                $table->boolean('fonte_oficial')->default(false)->after('fonte_url');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'tipo', 'ano_inicio', 'ano_fim', 'periodo_texto',
            'detalhes', 'fonte_url', 'fonte_oficial',
        ];

        $existing = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn('politica_mandatos', $column)
        ));

        if ($existing !== []) {
            Schema::table('politica_mandatos', fn (Blueprint $table) => $table->dropColumn($existing));
        }
    }
};
