<?php

namespace App\Models\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaptacaoPessoa extends Model
{
    /**
     * A tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'captacao_pessoas';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'celular',
        'telefone',
        'email',
        'endereco',
        'bairro',
        'cep',
        'cidade_id',
        'estado_id',
        'profissao',
        'aptidoes',
        'conversao',
        'obra',
        'testemunho',
        'bloco_id',
        'regiao_id',
        'igreja_id',
        'categoria_id',
        'cargo_id',
        'grupo_id',
        'foto',
        'assinatura',
        'trabalho',
        'batismo',
        'preso',
        // Campos de controle
        'status',
        'motivo_rejeicao',
        'revisado_por',
        'revisado_em',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'trabalho' => 'array',
        'batismo' => 'array',
        'preso' => 'array',
        'conversao' => 'date',
        'obra' => 'date',
        'revisado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- Relacionamentos (idênticos ao Model Pessoa para consistência) ---

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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }
    
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}