<?php

namespace App\Models\Unp;

use App\Models\Unp\Presidio;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Formatura;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $fillable = ['nome', 'presidio_id', 'dia_hora', 'instrutor_id', 'carga', 'reeducandos', 'inicio', 'fim', 'formatura', 'status'];

    protected $casts = [
        'inicio' => 'date',
        'fim' => 'date',
        'formatura' => 'date',
    ];

    public function presidio(): BelongsTo
    {
        return $this->belongsTo(Presidio::class);
    }

    public function instrutor(): BelongsTo
    {
        return $this->belongsTo(Instrutor::class);
    }

    public function formaturas(): HasMany
    {
        return $this->hasMany(Formatura::class);
    }
}