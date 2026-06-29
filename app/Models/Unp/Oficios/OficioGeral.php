<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OficioGeral extends Model
{
    use HasFactory;
    protected $fillable = ['data_oficio', 'presidio_id', 'assunto', 'inicio', 'meio', 'fim'];
    protected $casts = ['data_oficio' => 'date'];
    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
}
