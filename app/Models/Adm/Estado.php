<?php

namespace App\Models\Adm;

use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    protected $fillable = ['uf', 'nome'];

    public function cidades(): HasMany
    {
        return $this->hasMany(Cidade::class);
    }

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}