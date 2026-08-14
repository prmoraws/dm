<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class ApuracaoHistorico extends Model
{
    protected $table = 'politica_apuracao_historico';
    protected $fillable = ['apuracao_id', 'candidatura_id', 'capturado_em', 'votos', 'percentual', 'percentual_secoes'];
    protected $casts = ['capturado_em' => 'datetime', 'percentual' => 'decimal:4', 'percentual_secoes' => 'decimal:4'];

    public function apuracao() { return $this->belongsTo(Apuracao::class, 'apuracao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
}
