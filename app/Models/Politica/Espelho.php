<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espelho extends Model
{
    use HasFactory;

    protected $table = 'politica_espelhos';

    protected $fillable = [
        'cidade_id',
        'presidente_local',
        'indicacao_bispo',
        'filiados_republicanos',
        'prefeito_atual_nome',
        'prefeito_atual_partido',
        'prefeito_atual_votos',
        'observacoes',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}