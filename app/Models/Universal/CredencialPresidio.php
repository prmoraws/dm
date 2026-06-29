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
        'foto_verso'
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