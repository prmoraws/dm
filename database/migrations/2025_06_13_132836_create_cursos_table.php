<?php

use App\Models\Unp\Presidio;
use App\Models\Unp\Instrutor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->foreignIdFor(Presidio::class)->constrained()->onUpdate('cascade');
            $table->string('dia_hora', 255);
            $table->foreignIdFor(Instrutor::class)->constrained('instrutores')->onUpdate('cascade'); // Corrigido para 'instrutores'
            $table->string('carga', 255);
            $table->unsignedInteger('reeducandos')->default(0);
            $table->date('inicio');
            $table->date('fim');
            $table->date('formatura')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign(['presidio_id', 'instrutor_id']);
        });
        Schema::dropIfExists('cursos');
    }
};