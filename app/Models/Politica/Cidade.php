<?php

namespace App\Models\Politica;

use App\Models\Universal\Igreja;
use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\Zona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    protected $table = 'politica_cidades';

    protected $fillable = [
        'nome',
        'ibge_code',
        'latitude', // Adicionado
        'longitude', // Adicionado
        'populacao',
        'votos_validos_ultima_eleicao',
        'cadeiras_camara',
    ];

    public function bairros()
    {
        return $this->hasMany(Bairro::class, 'cidade_id');
    }

    public function espelho()
    {
        return $this->hasOne(Espelho::class, 'cidade_id');
    }

    public function projecoes()
    {
        return $this->hasMany(Projecao::class, 'cidade_id');
    }

    public function igrejas()
    {
        return $this->hasMany(Igreja::class, 'cidade_id');
    }

        /**
     * Sempre converte o nome da cidade para maiúsculas antes de salvar.
     */
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = mb_strtoupper($value, 'UTF-8');
    }


    public function zonas()
    {
        return $this->hasMany(Zona::class, 'cidade_id');
    }

    public function resultadosEleitorais()
    {
        return $this->hasMany(ResultadoMunicipal::class, 'cidade_id');
    }

    public function apuracoes()
    {
        return $this->hasMany(Apuracao::class, 'cidade_id');
    }

    public function inteligencia()
    {
        return $this->hasMany(EspelhoInteligencia::class, 'cidade_id');
    }

    public function candidatosFavoritos()
    {
        return $this->belongsToMany(Candidato::class, 'cidade_candidato', 'cidade_id', 'candidato_id');
    }
}