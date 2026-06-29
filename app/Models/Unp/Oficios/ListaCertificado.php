<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Curso;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListaCertificado extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'curso_id', 'reeducandos'];
    protected $casts = ['reeducandos' => 'array'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
