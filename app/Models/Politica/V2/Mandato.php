<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class Mandato extends Model
{
    protected $table = 'politica_mandatos';
    protected $fillable = [
        'politico_id', 'cargo_id', 'partido_id', 'eleicao_origem_id', 'fonte_id',
        'legislatura', 'esfera', 'uf', 'data_inicio', 'data_fim', 'situacao', 'fonte',
    ];
    protected $casts = ['data_inicio' => 'date', 'data_fim' => 'date'];

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
    public function cargo() { return $this->belongsTo(Cargo::class, 'cargo_id'); }
    public function partido() { return $this->belongsTo(Partido::class, 'partido_id'); }
    public function eleicaoOrigem() { return $this->belongsTo(Eleicao::class, 'eleicao_origem_id'); }
}
