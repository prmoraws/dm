<?php

use App\Models\Universal\Bloco;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regiaos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->foreignIdFor(Bloco::class)->constrained()->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('regiaos', function (Blueprint $table) {
            $table->dropForeign(['bloco_id']);
        });
        Schema::dropIfExists('regiaos');
    }
};