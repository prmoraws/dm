<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class Filiacao extends Model
{
    protected $table = 'politica_filiacoes';
    protected $fillable = ['politico_id', 'partido_id', 'data_inicio', 'data_fim', 'fonte', 'fonte_id'];
    protected $casts = ['data_inicio' => 'date', 'data_fim' => 'date'];

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
    public function partido() { return $this->belongsTo(Partido::class, 'partido_id'); }
}
