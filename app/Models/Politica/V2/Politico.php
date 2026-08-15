<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Politico extends Model
{
    use HasFactory;

    protected $table = 'politica_politicos';

    protected $fillable = [
        'nome_completo', 'nome_publico', 'slug', 'identidade_publica_hash', 'data_nascimento', 'uf_nascimento',
        'foto_url', 'biografia', 'links', 'ativo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'links' => 'array',
        'ativo' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function candidaturas() { return $this->hasMany(Candidatura::class, 'politico_id'); }
    public function filiacoes() { return $this->hasMany(Filiacao::class, 'politico_id'); }
    public function mandatos() { return $this->hasMany(Mandato::class, 'politico_id'); }
    public function acompanhamento() { return $this->hasOne(Acompanhamento::class, 'politico_id'); }
}
