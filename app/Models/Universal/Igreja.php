<?php

namespace App\Models\Universal;

use App\Models\Unp\Instrutor;
use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Igreja extends Model
{
    protected $fillable = ['nome', 'bloco_id', 'regiao_id', 'tipo_id'];

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class);
    }

    public function instrutores(): HasMany
    {
        return $this->hasMany(Instrutor::class);
    }

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}