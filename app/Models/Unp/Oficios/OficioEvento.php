<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioEvento extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_oficio',
        'presidio_id',
        'assunto',
        'dia_hora_evento',
        'descricao',
        'materiais',
    ];

    protected $casts = [
        'data_oficio' => 'date',
        'dia_hora_evento' => 'datetime',
    ];

    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
}