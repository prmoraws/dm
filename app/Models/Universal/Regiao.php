<?php

namespace App\Models\Universal;

use App\Models\Universal\Bloco;
use App\Models\Universal\Igreja;
use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regiao extends Model
{
    protected $fillable = ['nome', 'bloco_id'];

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class);
    }

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}