<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OficioCurso extends Model
{
    use HasFactory;
    protected $fillable = ['data_oficio', 'presidio_id', 'curso_id', 'material'];
    protected $casts = ['data_oficio' => 'date'];

    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
