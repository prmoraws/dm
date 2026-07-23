<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CredencialPresidio extends Model
{
    protected $fillable = [
        'credenciado_id',
        'presidio_id',
        'foto_frente',
        'foto_verso',
        'unidade_nao_faz', // Novo campo booleano ou integer
        'data_vencimento'   // Novo campo de data
    ];

    protected $casts = [
        'unidade_nao_faz' => 'boolean',
        'data_vencimento' => 'date',
    ];

    public function credenciado(): BelongsTo
    {
        return $this->belongsTo(Credenciado::class);
    }

    public function presidio(): BelongsTo
    {
        // Ajuste o namespace abaixo se o seu model Presidio estiver em outro local
        return $this->belongsTo(\App\Models\Unp\Presidio::class);
    }
}
