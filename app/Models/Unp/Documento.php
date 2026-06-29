<?php
namespace App\Models\Unp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'classe', 'titulo', 'descricao', 'upload'];
    protected $casts = ['upload' => 'array']; // Trata a coluna JSON como um array PHP
}