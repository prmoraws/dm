<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class TseImportacao extends Model
{
    protected $table = 'politica_tse_importacoes';

    protected $fillable = [
        'execucao', 'ano', 'uf', 'tipo', 'escopo', 'status', 'arquivo', 'sha256',
        'linhas_lidas', 'linhas_selecionadas', 'inseridos', 'atualizados', 'ignorados',
        'iniciada_em', 'concluida_em', 'ultimo_erro', 'meta',
    ];

    protected $casts = [
        'iniciada_em' => 'datetime',
        'concluida_em' => 'datetime',
        'meta' => 'array',
    ];
}
