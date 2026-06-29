<?php

namespace App\Models\Evento;

use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    protected $table = 'instituicoes'; // Define explicitamente o nome da tabela
    protected $fillable = ['nome', 'contato', 'bairro', 'convidados', 'onibus', 'bloco', 'iurd', 'pastor', 'telefone', 'endereco', 'localização'];
}