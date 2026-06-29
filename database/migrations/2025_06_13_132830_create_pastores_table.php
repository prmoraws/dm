<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pastores', function (Blueprint $table) {
            $table->id();
            $table->string('sede', 255);
            $table->string('pastor', 255);
            $table->string('telefone', 255);
            $table->string('esposa', 255);
            $table->string('tel_epos', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pastores');
    }
};