<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleicao extends Model
{
    use HasFactory;

    protected $table = 'politica_eleicoes';
    protected $fillable = [
        'tse_eleicao_codigo', 'tse_pleito_codigo', 'ano', 'turno', 'tipo', 'descricao',
        'data_eleicao', 'uf', 'status', 'inicio_apuracao', 'fim_apuracao',
    ];
    protected $casts = [
        'data_eleicao' => 'date',
        'inicio_apuracao' => 'datetime',
        'fim_apuracao' => 'datetime',
    ];

    public function candidaturas() { return $this->hasMany(Candidatura::class, 'eleicao_id'); }
    public function apuracoes() { return $this->hasMany(Apuracao::class, 'eleicao_id'); }
    public function secoes() { return $this->hasMany(Secao::class, 'eleicao_id'); }
}
