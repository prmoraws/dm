<?php

namespace App\Models\Unp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioFormatura extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data_oficio',
        'presidio_id',
        'curso_id',
        'dia_hora_evento',
        'materiais',
        'comunicacao',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_oficio' => 'datetime',
        'dia_hora_evento' => 'datetime',
    ];

    /**
     * Get the presidio that the ofício belongs to.
     */
    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }

    /**
     * Get the curso (assunto) that the ofício belongs to.
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
