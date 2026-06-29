<?php

namespace App\Models\Unp\Oficios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacaoCurso extends Model
{
    use HasFactory;

    protected $table = 'informacao_cursos'; // Garante o nome correto da tabela
    protected $fillable = ['nome', 'informacao'];
}
