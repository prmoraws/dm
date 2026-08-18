<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class TseSolicitacao extends Model
{
    protected $table = 'politica_tse_solicitacoes';

    protected $fillable = [
        'ano', 'uf', 'escopo', 'somente', 'origem', 'status', 'solicitante_user_id',
        'solicitada_em', 'iniciada_em', 'concluida_em', 'ultimo_erro', 'meta',
    ];

    protected $casts = [
        'solicitada_em' => 'datetime',
        'iniciada_em' => 'datetime',
        'concluida_em' => 'datetime',
        'meta' => 'array',
    ];
}
