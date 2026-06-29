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
        Schema::create('credencial_presidios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credenciado_id')->constrained('credenciados')->onDelete('cascade');
            $table->foreignId('presidio_id')->constrained('presidios');
            $table->string('foto_frente')->nullable();
            $table->string('foto_verso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credencial_presidios');
    }
};
