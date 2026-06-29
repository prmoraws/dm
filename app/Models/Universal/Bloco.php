<?php

namespace App\Models\Universal;

use App\Models\Unp\Instrutor;
use App\Models\Universal\Igreja;
use App\Models\Universal\Pessoa;
use App\Models\Universal\Regiao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bloco extends Model
{
    protected $fillable = ['nome'];

    public function regiaos(): HasMany
    {
        return $this->hasMany(Regiao::class);
    }

    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class);
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