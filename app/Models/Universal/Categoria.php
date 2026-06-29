<?php

namespace App\Models\Universal;

use App\Models\Unp\Instrutor;
use App\Models\Universal\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = ['nome', 'descricao'];

    public function instrutores(): HasMany
    {
        return $this->hasMany(Instrutor::class);
    }

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class);
    }
}