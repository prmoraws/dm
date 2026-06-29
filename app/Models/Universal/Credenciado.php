<?php

namespace App\Models\Universal;

use App\Models\Adm\Cidade;
use App\Models\Adm\Estado;
use App\Models\Unp\Cargo;
use App\Models\Unp\Grupo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credenciado extends Model
{
    protected $fillable = [
        'bloco_id', 'regiao_id', 'igreja_id', 'categoria_id', 'cargo_id', 
        'grupo_id', 'cidade_id', 'estado_id', 'nome', 'celular', 'telefone', 
        'email', 'endereco', 'bairro', 'cep', 'profissao', 'aptidoes', 
        'conversao', 'obra', 'testemunho', 'foto', 'identidade_frente', 
        'identidade_verso', 'trabalho', 'batismo', 'preso'
    ];

    protected $casts = [
        'conversao' => 'date',
        'obra' => 'date',
        'trabalho' => 'array',
        'batismo' => 'array',
        'preso' => 'array',
    ];

    // Relacionamento com as múltiplas credenciais
    public function credencialPresidios(): HasMany
    {
        return $this->hasMany(CredencialPresidio::class);
    }

    // Relacionamentos idênticos ao model Pessoa
    public function bloco(): BelongsTo { return $this->belongsTo(Bloco::class); }
    public function regiao(): BelongsTo { return $this->belongsTo(Regiao::class); }
    public function igreja(): BelongsTo { return $this->belongsTo(Igreja::class); }
    public function categoria(): BelongsTo { return $this->belongsTo(Categoria::class); }
    public function cargo(): BelongsTo { return $this->belongsTo(Cargo::class); }
    public function grupo(): BelongsTo { return $this->belongsTo(Grupo::class); }
    public function cidade(): BelongsTo { return $this->belongsTo(Cidade::class); }
    public function estado(): BelongsTo { return $this->belongsTo(Estado::class); }
}