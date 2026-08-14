<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use Illuminate\Database\Eloquent\Model;

class ResultadoMunicipal extends Model
{
    protected $table = 'politica_resultados_municipais';
    protected $fillable = [
        'eleicao_id', 'candidatura_id', 'cidade_id', 'votos', 'percentual', 'posicao',
        'secoes_total', 'secoes_totalizadas', 'eleitores', 'comparecimento', 'abstencoes', 'sincronizado_em',
    ];
    protected $casts = ['percentual' => 'decimal:4', 'sincronizado_em' => 'datetime'];

    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
    public function cidade() { return $this->belongsTo(Cidade::class, 'cidade_id'); }
}
