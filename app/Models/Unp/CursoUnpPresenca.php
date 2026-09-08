<?php

namespace App\Models\Unp;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoUnpPresenca extends Model
{
    protected $table = 'curso_unp_presencas';

    protected $fillable = [
        'matricula_id', 'data_aula', 'situacao', 'observacao', 'registrado_por',
    ];

    protected $casts = [
        'data_aula' => 'date',
    ];

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(CursoUnpMatricula::class, 'matricula_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
