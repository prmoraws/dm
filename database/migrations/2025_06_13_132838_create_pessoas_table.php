<?php

use App\Models\Universal\Bloco;
use App\Models\Universal\Regiao;
use App\Models\Universal\Igreja;
use App\Models\Universal\Categoria;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Bloco::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Regiao::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Igreja::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Categoria::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Cargo::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Grupo::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Cidade::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Estado::class)->constrained()->onUpdate('cascade');
            $table->string('foto', 255);
            $table->string('nome', 255);
            $table->string('celular', 255);
            $table->string('telefone', 255);
            $table->string('email', 255)->unique();
            $table->string('endereco', 255);
            $table->string('bairro', 255);
            $table->string('cep', 255);
            $table->string('profissao', 255);
            $table->text('aptidoes');
            $table->date('conversao')->nullable();
            $table->date('obra')->nullable();
            $table->json('trabalho');
            $table->json('batismo'); //
            $table->json('preso');
            $table->text('testemunho');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeign(['bloco_id', 'regiao_id', 'igreja_id', 'categoria_id', 'cargo_id', 'grupo_id', 'cidade_id', 'estado_id']);
        });
        Schema::dropIfExists('pessoas');
    }
};
