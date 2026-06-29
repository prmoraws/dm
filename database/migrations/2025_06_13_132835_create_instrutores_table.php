<?php

use App\Models\Universal\Bloco;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instrutores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bloco::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Categoria::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Igreja::class)->constrained()->onUpdate('cascade');
            $table->string('foto', 255);
            $table->string('nome', 255);
            $table->string('telefone', 255);
            $table->string('rg', 255)->nullable();
            $table->string('cpf', 255)->nullable();
            $table->string('profissao', 255);
            $table->json('batismo')->nullable();
            $table->text('testemunho')->nullable();
            $table->string('carga', 255)->nullable();
            $table->boolean('certificado')->default(0);
            $table->boolean('inscricao')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('instrutores', function (Blueprint $table) {
            $table->dropForeign(['bloco_id', 'categoria_id', 'igreja_id']);
        });
        Schema::dropIfExists('instrutores');
    }
};