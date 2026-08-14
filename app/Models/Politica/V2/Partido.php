<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'politica_partidos';
    protected $fillable = ['tse_codigo', 'numero', 'sigla', 'nome', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    public function candidaturas() { return $this->hasMany(Candidatura::class, 'partido_id'); }
    public function filiacoes() { return $this->hasMany(Filiacao::class, 'partido_id'); }
    public function mandatos() { return $this->hasMany(Mandato::class, 'partido_id'); }
}
