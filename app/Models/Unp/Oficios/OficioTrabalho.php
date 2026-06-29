<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OficioTrabalho extends Model
{
    use HasFactory;
    protected $fillable = ['data_oficio', 'presidio_id', 'dia_hora_evento', 'evangelistas', 'materiais'];
    protected $casts = ['data_oficio' => 'date', 'dia_hora_evento' => 'datetime', 'evangelistas' => 'array'];
    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
}
