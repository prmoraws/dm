<?php

namespace App\Models\Unp;

use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    protected $fillable = ['nome', 'descricao'];

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}