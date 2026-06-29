<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Model;

class Pastor extends Model
{
    protected $table = 'pastores'; // Define explicitamente o nome da tabela
    protected $fillable = ['sede', 'pastor', 'telefone', 'esposa', 'tel_epos'];
}