<?php
namespace App\Models\Unp\Oficios;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convidado extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'cpf', 'rg', 'classe'];
}
