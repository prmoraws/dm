<?php

namespace App\Models\Universal;

use App\Models\Universal\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends Model
{
    protected $fillable = ['nome'];

    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class);
    }
}