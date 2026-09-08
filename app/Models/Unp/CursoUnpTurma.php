<?php

namespace App\Models\Unp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoUnpTurma extends Model
{
    protected $table = 'curso_unp_turmas';

    protected $fillable = [
        'nome', 'data_inicio', 'data_fim', 'dias_horarios', 'local',
        'instrutor_id', 'limite_alunos', 'status', 'link_whatsapp',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'limite_alunos' => 'integer',
    ];

    public function instrutor(): BelongsTo
    {
        return $this->belongsTo(Instrutor::class);
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(CursoUnpMatricula::class, 'turma_id');
    }
}
