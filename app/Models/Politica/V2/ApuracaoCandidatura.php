<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class ApuracaoCandidatura extends Model
{
    protected $table = 'politica_apuracao_candidaturas';
    protected $fillable = ['apuracao_id', 'candidatura_id', 'posicao', 'votos', 'percentual', 'situacao', 'eleito', 'sincronizado_em'];
    protected $casts = ['percentual' => 'decimal:4', 'eleito' => 'boolean', 'sincronizado_em' => 'datetime'];

    public function apuracao() { return $this->belongsTo(Apuracao::class, 'apuracao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
}
