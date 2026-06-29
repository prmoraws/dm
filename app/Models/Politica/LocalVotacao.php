<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalVotacao extends Model
{
    use HasFactory;

    protected $table = 'politica_locais_votacao';

    /**
     * A lista de atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'cidade_id', // <-- CORREÇÃO ADICIONADA AQUI
        'bairro_id', 
        'nome', 
        'endereco'
    ];

    public function bairro()
    {
        return $this->belongsTo(Bairro::class, 'bairro_id');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function votacoes()
    {
        return $this->hasMany(VotacaoDetalhada::class, 'local_votacao_id');
    }
}