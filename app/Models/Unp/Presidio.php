<?php

namespace App\Models\Unp;

use App\Models\Unp\Curso;
use App\Models\Unp\Formatura;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Presidio extends Model
{
    protected $fillable = ['nome', 'diretor', 'contato_diretor', 'adjunto', 'contato_adjunto', 'laborativa', 'contato_laborativa', 'visita', 'interno'];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function formaturas(): HasMany
    {
        return $this->hasMany(Formatura::class);
    }
}