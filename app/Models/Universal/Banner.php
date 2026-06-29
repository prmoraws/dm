<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['nome', 'descricao', 'foto'];
}