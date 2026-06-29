<?php

namespace App\Models\Adm;

use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cidade extends Model
{
    protected $fillable = ['cidade_id', 'estado_id', 'nome'];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}