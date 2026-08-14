<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class ResultadoZona extends Model
{
    protected $table = 'politica_resultados_zonas';
    protected $fillable = ['eleicao_id', 'candidatura_id', 'zona_id', 'votos', 'percentual', 'secoes_total', 'secoes_totalizadas', 'sincronizado_em'];
    protected $casts = ['percentual' => 'decimal:4', 'sincronizado_em' => 'datetime'];

    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
    public function zona() { return $this->belongsTo(Zona::class, 'zona_id'); }
}
