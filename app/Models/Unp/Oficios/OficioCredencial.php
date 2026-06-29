<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioCredencial extends Model
{
    use HasFactory;

    protected $fillable = ['presidio_id', 'nome', 'cpf', 'rg'];

    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
}