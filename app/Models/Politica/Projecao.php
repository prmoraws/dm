<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projecao extends Model
{
    use HasFactory;

    protected $table = 'politica_projecoes';

    protected $fillable = [
        'cidade_id',
        'candidato_id',
        'ano_eleicao_futura',
        'meta_votos_coeficiente',
        'votos_garantidos_igreja',
        'votos_faltantes',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}