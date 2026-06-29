<?php

namespace App\Models\Unp;

use App\Models\Universal\Bloco;
use App\Models\Universal\Categoria;
use App\Models\Universal\Igreja;
use App\Models\Unp\Curso;
use App\Models\Unp\Formatura;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instrutor extends Model
{
    protected $table = 'instrutores';
    protected $fillable = ['bloco_id', 'categoria_id', 'igreja_id', 'foto', 'nome', 'telefone', 'rg', 'cpf', 'profissao', 'batismo', 'testemunho', 'carga', 'certificado', 'inscricao'];

    protected $casts = [
        'batismo' => 'array',
        'certificado' => 'boolean',
        'inscricao' => 'boolean',
    ];

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function formaturas(): HasMany
    {
        return $this->hasMany(Formatura::class);
    }
}