<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oficio_gerals', function (Blueprint $table) {
            $table->id();
            $table->date('data_oficio');
            $table->foreignId('presidio_id')->constrained('presidios')->onDelete('cascade');
            $table->string('assunto');
            $table->text('inicio');
            $table->text('meio')->nullable();
            $table->text('fim')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('oficio_gerals'); }
};