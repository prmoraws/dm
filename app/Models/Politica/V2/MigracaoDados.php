<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class MigracaoDados extends Model
{
    protected $table = 'politica_migracoes_dados';

    protected $fillable = [
        'chave',
        'status',
        'iniciada_em',
        'concluida_em',
        'estatisticas',
        'ultimo_erro',
    ];

    protected $casts = [
        'iniciada_em' => 'datetime',
        'concluida_em' => 'datetime',
        'estatisticas' => 'array',
    ];
}
