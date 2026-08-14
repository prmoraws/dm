<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class Acompanhamento extends Model
{
    protected $table = 'politica_acompanhamentos';
    protected $fillable = ['politico_id', 'grupo', 'prioridade', 'ordem', 'ativo', 'observacoes'];
    protected $casts = ['ativo' => 'boolean'];

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
}
