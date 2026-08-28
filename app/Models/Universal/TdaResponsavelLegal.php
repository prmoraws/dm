<?php

namespace App\Models\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TdaResponsavelLegal extends Model
{
    protected $table = 'tda_responsaveis_legais';

    protected $fillable = [
        'captacao_tda_id', 'cadastro_tda_id', 'nome', 'nacionalidade',
        'estado_civil', 'profissao', 'rg', 'cpf', 'endereco', 'numero',
        'complemento', 'bairro', 'cep', 'estado_id', 'cidade_id',
        'data_nascimento',
    ];

    protected $casts = ['data_nascimento' => 'date'];

    public function captacaoTda(): BelongsTo
    {
        return $this->belongsTo(CaptacaoTda::class);
    }

    public function cadastroTda(): BelongsTo
    {
        return $this->belongsTo(CadastroTda::class);
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
