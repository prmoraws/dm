<?php

use App\Models\Universal\Bloco;
use App\Models\Universal\Regiao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('igrejas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->foreignIdFor(Bloco::class)->constrained()->onUpdate('cascade');
            $table->foreignIdFor(Regiao::class)->constrained()->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            $table->dropForeign(['bloco_id', 'regiao_id']);
        });
        Schema::dropIfExists('igrejas');
    }
};