<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'politica_zonas';
    protected $fillable = ['cidade_id', 'numero', 'tse_codigo'];

    public function cidade() { return $this->belongsTo(Cidade::class, 'cidade_id'); }
    public function secoes() { return $this->hasMany(Secao::class, 'zona_id'); }
    public function resultados() { return $this->hasMany(ResultadoZona::class, 'zona_id'); }
    public function apuracoes() { return $this->hasMany(Apuracao::class, 'zona_id'); }
}
