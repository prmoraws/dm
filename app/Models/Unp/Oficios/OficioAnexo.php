<?php
namespace App\Models\Unp\Oficios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioAnexo extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'data', 'esposas', 'convidados', 'comunicacao', 'organizacao'];
    protected $casts = [
        'esposas' => 'array',
        'convidados' => 'array',
        'comunicacao' => 'array',
        'organizacao' => 'array',
        'data' => 'date',
    ];
}