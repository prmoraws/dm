<?php

namespace App\Models\Politica;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bairro extends Model
{
    use HasFactory;

    protected $table = 'politica_bairros';

    protected $fillable = ['cidade_id', 'nome'];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function locaisVotacao()
    {
        return $this->hasMany(LocalVotacao::class, 'bairro_id');
    }
}