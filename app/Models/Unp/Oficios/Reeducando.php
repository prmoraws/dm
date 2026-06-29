<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Curso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reeducando extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'documento', 'curso_id'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
