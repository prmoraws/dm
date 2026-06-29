<?php

namespace App\Models\Evento;

use Illuminate\Database\Eloquent\Model;

class Cesta extends Model
{
    protected $fillable = ['nome', 'terreiro', 'contato', 'cestas', 'observacao', 'foto'];
}