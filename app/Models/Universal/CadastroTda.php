<?php

namespace App\Models\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CadastroTda extends Model
{
    /**
     * A tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'cadastro_tdas';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        // Vínculo com igreja e grupo
        'bloco_id',
        'regiao_id',
        'igreja_id',
        'estado_id',
        'data_ingresso_grupo',
        'funcao_grupo',
        'foto',

        // Dados pessoais
        'nome',
        'data_nascimento',
        'estado_civil',
        'rg',
        'cpf',
        'celular',
        'facebook',
        'instagram',
        'endereco',
        'numero',
        'cep',
        'bairro',
        'cidade_id',
        'email',
        'escolaridade',
        'profissao',
        'tem_filhos',
        'quantidade_filhos',
        'idade_filhos',

        // Contato de emergência
        'emergencia_nome',
        'emergencia_celular',
        'emergencia_facebook',
        'emergencia_instagram',

        // Dados espirituais
        'condicao_atual',
        'inicio_iurd',
        'batizado_aguas',
        'data_batismo_aguas',
        'batizado_espirito_santo',
        'data_batismo_espirito_santo',
        'ja_se_afastou',
        'dias_reunioes',
        'dias_evangelizacao',

        // Controle de exibição
        'sexo',

        // Específicos mulher
        'godllywood_autoajuda',
        'meditacao_univer',

        // Específicos homem
        'intellimen_reunioes',
        'intellimen_desafios',

        // Colaborador/Obreiro/Levita
        'colaborador',
        'data_graduacao_colaborador',
        'obreiro',
        'data_graduacao_obreiro',
        'levita',
        'data_graduacao_levita',
        'dias_trabalho_reuniao',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'data_ingresso_grupo' => 'date',
        'data_nascimento' => 'date',
        'tem_filhos' => 'boolean',
        'inicio_iurd' => 'date',
        'batizado_aguas' => 'boolean',
        'data_batismo_aguas' => 'date',
        'batizado_espirito_santo' => 'boolean',
        'data_batismo_espirito_santo' => 'date',
        'ja_se_afastou' => 'boolean',
        'dias_reunioes' => 'array',
        'dias_evangelizacao' => 'array',
        'godllywood_autoajuda' => 'boolean',
        'meditacao_univer' => 'boolean',
        'intellimen_reunioes' => 'boolean',
        'intellimen_desafios' => 'boolean',
        'colaborador' => 'boolean',
        'data_graduacao_colaborador' => 'date',
        'obreiro' => 'boolean',
        'data_graduacao_obreiro' => 'date',
        'levita' => 'boolean',
        'data_graduacao_levita' => 'date',
        'dias_trabalho_reuniao' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- Relacionamentos (mesmo padrão de Pessoa) ---

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class);
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }
}
