<?php

namespace App\Models\Unp;

use App\Models\Unp\Presidio;
use App\Models\Unp\Curso;
use App\Models\Unp\Instrutor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formatura extends Model
{
    protected $fillable = ['presidio_id', 'curso_id', 'instrutor_id', 'inicio', 'fim', 'formatura', 'lista', 'conteudo', 'oficio'];

    protected $casts = [
        'inicio' => 'date',
        'fim' => 'date',
        'formatura' => 'date',
    ];

    public function presidio(): BelongsTo
    {
        return $this->belongsTo(Presidio::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function instrutor(): BelongsTo
    {
        return $this->belongsTo(Instrutor::class);
    }
}