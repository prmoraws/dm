<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unp_oficio_convidados', function (Blueprint $table) {
            $table->id();
            $table->string('oficio_tipo', 30);
            $table->unsignedBigInteger('oficio_id');
            $table->foreignId('convidado_id')->constrained('convidados')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['oficio_tipo', 'oficio_id', 'convidado_id'], 'unp_oficio_convidados_unique');
            $table->index(['oficio_tipo', 'oficio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unp_oficio_convidados');
    }
};
