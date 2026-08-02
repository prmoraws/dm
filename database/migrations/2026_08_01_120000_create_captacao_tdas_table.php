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
        Schema::create('captacao_tdas', function (Blueprint $table) {
            $table->id();

            // --- Vínculo com igreja e grupo (nulos: preenchimento progressivo no wizard) ---
            $table->foreignId('bloco_id')->nullable()->constrained('blocos')->onDelete('set null');
            $table->foreignId('regiao_id')->nullable()->constrained('regiaos')->onDelete('set null');
            $table->foreignId('igreja_id')->nullable()->constrained('igrejas')->onDelete('set null');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null');
            $table->date('data_ingresso_grupo')->nullable();
            $table->string('funcao_grupo')->nullable();
            $table->string('foto')->nullable();

            // --- Dados pessoais ---
            $table->string('nome');
            $table->date('data_nascimento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('rg')->nullable();
            $table->string('cpf')->nullable();
            $table->string('celular');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('cep')->nullable();
            $table->string('bairro')->nullable();
            $table->foreignId('cidade_id')->nullable()->constrained('cidades')->onDelete('set null');
            $table->string('email')->nullable();
            $table->string('escolaridade')->nullable();
            $table->string('profissao')->nullable();
            $table->boolean('tem_filhos')->nullable();
            $table->unsignedTinyInteger('quantidade_filhos')->nullable();
            $table->string('idade_filhos')->nullable();

            // --- Contato de emergência ---
            $table->string('emergencia_nome')->nullable();
            $table->string('emergencia_celular')->nullable();
            $table->string('emergencia_facebook')->nullable();
            $table->string('emergencia_instagram')->nullable();

            // --- Dados espirituais ---
            $table->enum('condicao_atual', ['membro', 'cpo', 'colaborador', 'obreiro', 'levita', 'auxiliar'])->nullable();
            $table->date('inicio_iurd')->nullable();
            $table->boolean('batizado_aguas')->nullable();
            $table->date('data_batismo_aguas')->nullable();
            $table->boolean('batizado_espirito_santo')->nullable();
            $table->date('data_batismo_espirito_santo')->nullable();
            $table->boolean('ja_se_afastou')->nullable();
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
            $table->boolean('colaborador')->nullable();
            $table->date('data_graduacao_colaborador')->nullable();
            $table->boolean('obreiro')->nullable();
            $table->date('data_graduacao_obreiro')->nullable();
            $table->boolean('levita')->nullable();
            $table->date('data_graduacao_levita')->nullable();
            $table->json('dias_trabalho_reuniao')->nullable();

            // --- Controle do fluxo de captação (igual ao captacao_pessoas) ---
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('motivo_rejeicao')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('revisado_em')->nullable();

            $table->timestamps();

            // --- Índices para busca/listagem (mesmo padrão adotado em produção para pessoas) ---
            $table->index('nome', 'idx_captacao_tdas_nome');
            $table->index('celular', 'idx_captacao_tdas_celular');
            $table->index('status', 'idx_captacao_tdas_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captacao_tdas');
    }
};
