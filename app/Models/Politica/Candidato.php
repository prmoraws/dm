<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    use HasFactory;

    protected $table = 'politica_candidatos';

    protected $fillable = ['nome', 'partido'];

    public function votacoes()
    {
        return $this->hasMany(VotacaoDetalhada::class, 'candidato_id');
    }

    public function projecoes()
    {
        return $this->hasMany(Projecao::class, 'candidato_id');
    }

    public function cidadesFavoritas()
    {
        return $this->belongsToMany(Cidade::class, 'cidade_candidato', 'candidato_id', 'cidade_id');
    }
}