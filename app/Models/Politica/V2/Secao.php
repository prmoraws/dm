<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\LocalVotacao;
use Illuminate\Database\Eloquent\Model;

class Secao extends Model
{
    protected $table = 'politica_secoes';
    protected $fillable = ['eleicao_id', 'zona_id', 'local_votacao_id', 'numero', 'eleitores_aptos', 'situacao'];

    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function zona() { return $this->belongsTo(Zona::class, 'zona_id'); }
    public function localVotacao() { return $this->belongsTo(LocalVotacao::class, 'local_votacao_id'); }
    public function resultados() { return $this->hasMany(ResultadoSecao::class, 'secao_id'); }
}
