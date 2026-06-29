<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OficioCop extends Model
{
    use HasFactory;
    protected $fillable = ['data_oficio', 'presidio_id', 'evento', 'unidade_id', 'dia_hora_evento', 'convidados'];
    protected $casts = ['data_oficio' => 'date', 'dia_hora_evento' => 'datetime', 'convidados' => 'array'];
    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
    public function unidade()
    {
        return $this->belongsTo(Presidio::class, 'unidade_id');
    }
}
