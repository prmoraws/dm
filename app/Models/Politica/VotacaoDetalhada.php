<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotacaoDetalhada extends Model
{
    use HasFactory;

    protected $table = 'politica_votacao_detalhada';

    protected $fillable = [
        'local_votacao_id',
        'candidato_id',
        'ano_eleicao',
        'cargo',
        'votos_recebidos',
    ];

    public function localVotacao()
    {
        return $this->belongsTo(LocalVotacao::class, 'local_votacao_id');
    }

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}