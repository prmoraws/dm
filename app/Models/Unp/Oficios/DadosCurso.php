<?php

namespace App\Models\Unp\Oficios;

use App\Models\Unp\Curso;
use App\Models\Unp\Oficios\InformacaoCurso;
use App\Models\Unp\Instrutor;
use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DadosCurso extends Model
{
    use HasFactory;
    protected $table = 'dados_cursos';
    protected $fillable = ['nome', 'presidio_id', 'curso_id', 'instrutor_id', 'informacao_curso_id', 'inicio', 'fim', 'carga'];
    protected $casts = ['inicio' => 'date', 'fim' => 'date'];

    public function presidio()
    {
        return $this->belongsTo(Presidio::class);
    }
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
    public function instrutor()
    {
        return $this->belongsTo(Instrutor::class);
    }
    public function informacaoCurso()
    {
        return $this->belongsTo(InformacaoCurso::class);
    }
}
