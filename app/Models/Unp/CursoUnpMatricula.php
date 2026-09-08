<?php

namespace App\Models\Unp;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoUnpMatricula extends Model
{
    protected $table = 'curso_unp_matriculas';

    protected $fillable = [
        'captacao_id', 'turma_id', 'situacao', 'matriculado_em', 'matriculado_por',
        'finalizado_em', 'finalizado_por', 'observacao_final',
    ];

    protected $casts = [
        'matriculado_em' => 'datetime',
        'finalizado_em' => 'datetime',
    ];

    public function captacao(): BelongsTo
    {
        return $this->belongsTo(CursoUnpCaptacao::class, 'captacao_id');
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(CursoUnpTurma::class, 'turma_id');
    }

    public function matriculador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matriculado_por');
    }

    public function finalizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalizado_por');
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(CursoUnpPresenca::class, 'matricula_id');
    }
}
