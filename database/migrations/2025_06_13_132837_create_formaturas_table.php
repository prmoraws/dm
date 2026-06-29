<?php

use App\Models\Unp\Presidio;
use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Presidio::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Curso::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Instrutor::class)->constrained('instrutores')->onUpdate('cascade'); // Corrigido para 'instrutores'
            $table->date('inicio');
            $table->date('fim');
            $table->date('formatura')->nullable();
            $table->string('lista', 255)->nullable();
            $table->string('conteudo', 255)->nullable();
            $table->string('oficio', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('formaturas', function (Blueprint $table) {
            $table->dropForeign(['presidio_id', 'curso_id', 'instrutor_id']);
        });
        Schema::dropIfExists('formaturas');
    }
};