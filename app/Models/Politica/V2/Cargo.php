<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'politica_cargos';
    protected $fillable = ['tse_codigo', 'nome', 'esfera', 'abrangencia', 'ordem', 'ativo'];
    protected $casts = ['ativo' => 'boolean'];

    public function candidaturas() { return $this->hasMany(Candidatura::class, 'cargo_id'); }
    public function mandatos() { return $this->hasMany(Mandato::class, 'cargo_id'); }
    public function apuracoes() { return $this->hasMany(Apuracao::class, 'cargo_id'); }
}
