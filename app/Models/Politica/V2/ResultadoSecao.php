<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class ResultadoSecao extends Model
{
    protected $table = 'politica_resultados_secoes';
    protected $fillable = ['eleicao_id', 'candidatura_id', 'secao_id', 'votos'];

    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
    public function secao() { return $this->belongsTo(Secao::class, 'secao_id'); }
}
