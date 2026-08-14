<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class FonteEstado extends Model
{
    protected $table = 'politica_fontes_estado';
    protected $fillable = [
        'chave', 'fonte', 'tipo_arquivo', 'url', 'etag', 'last_modified', 'idg',
        'ultimo_check_em', 'ultima_alteracao_em', 'ultimo_sucesso_em', 'http_status',
        'erros_consecutivos', 'ultimo_erro', 'meta',
    ];
    protected $casts = [
        'ultimo_check_em' => 'datetime',
        'ultima_alteracao_em' => 'datetime',
        'ultimo_sucesso_em' => 'datetime',
        'meta' => 'array',
    ];
}
