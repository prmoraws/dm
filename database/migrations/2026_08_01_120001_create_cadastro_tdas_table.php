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
        Schema::create('cadastro_tdas', function (Blueprint $table) {
            $table->id();

            // --- Vínculo com igreja e grupo ---
            $table->foreignId('bloco_id')->constrained('blocos')->onUpdate('cascade');
            $table->foreignId('regiao_id')->constrained('regiaos')->onUpdate('cascade');
            $table->foreignId('igreja_id')->constrained('igrejas')->onUpdate('cascade');
            $table->foreignId('estado_id')->constrained('estados')->onUpdate('cascade');
            $table->date('data_ingresso_grupo');
            $table->string('funcao_grupo');
            $table->string('foto');

            // --- Dados pessoais ---
            $table->string('nome');
            $table->date('data_nascimento');
            $table->string('estado_civil');
            $table->string('rg');
            $table->string('cpf')->unique();
            $table->string('celular');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('endereco');
            $table->string('numero')->nullable();
            $table->string('cep');
            $table->string('bairro');
            $table->foreignId('cidade_id')->constrained('cidades')->onUpdate('cascade');
            $table->string('email')->nullable();
            $table->string('escolaridade');
            $table->string('profissao')->nullable();
            $table->boolean('tem_filhos')->default(false);
            $table->unsignedTinyInteger('quantidade_filhos')->nullable();
            $table->string('idade_filhos')->nullable();

            // --- Contato de emergência ---
            $table->string('emergencia_nome');
            $table->string('emergencia_celular');
            $table->string('emergencia_facebook')->nullable();
            $table->string('emergencia_instagram')->nullable();

            // --- Dados espirituais ---
            $table->enum('condicao_atual', ['membro', 'cpo', 'colaborador', 'obreiro', 'levita', 'auxiliar']);
            $table->date('inicio_iurd')->nullable();
            $table->boolean('batizado_aguas')->default(false);
            $table->date('data_batismo_aguas')->nullable();
            $table->boolean('batizado_espirito_santo')->default(false);
            $table->date('data_batismo_espirito_santo')->nullable();
            $table->boolean('ja_se_afastou')->default(false);
            $table->json('dias_reunioes')->nullable();
            $table->json('dias_evangelizacao')->nullable();

            // --- Controle de exibição (não impresso na ficha) ---
            $table->enum('sexo', ['feminino', 'masculino'])->nullable();

            // --- Específicos: mulher ---
            $table->boolean('godllywood_autoajuda')->nullable();
            $table->boolean('meditacao_univer')->nullable();

            // --- Específicos: homem ---
            $table->boolean('intellimen_reunioes')->nullable();
            $table->boolean('intellimen_desafios')->nullable();

            // --- Colaborador / Obreiro / Levita ---
            $table->boolean('colaborador')->default(false);
            $table->date('data_graduacao_colaborador')->nullable();
            $table->boolean('obreiro')->default(false);
            $table->date('data_graduacao_obreiro')->nullable();
            $table->boolean('levita')->default(false);
            $table->date('data_graduacao_levita')->nullable();
            $table->json('dias_trabalho_reuniao')->nullable();

            $table->timestamps();

            // --- Índices para busca/listagem administrativa ---
            $table->index('nome', 'idx_cadastro_tdas_nome');
            $table->index('celular', 'idx_cadastro_tdas_celular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cadastro_tdas');
    }
};
