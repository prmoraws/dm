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

    public function getSituacaoCredencialAttribute(): array
    {
        $credenciais = $this->relationLoaded('credencialPresidios')
            ? $this->credencialPresidios
            : $this->credencialPresidios()->get();

        if ($credenciais->isEmpty()) {
            return ['rotulo' => 'Sem credencial', 'classe' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200'];
        }

        $emitidas = $credenciais->where('unidade_nao_faz', false);
        $hoje = today();
        $limite = today()->addDays(30);

        if ($emitidas->contains(fn (CredencialPresidio $item) => $item->data_vencimento?->isBefore($hoje))) {
            return ['rotulo' => 'Vencida', 'classe' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'];
        }

        if ($emitidas->contains(fn (CredencialPresidio $item) => $item->data_vencimento?->betweenIncluded($hoje, $limite))) {
            return ['rotulo' => 'Vencendo', 'classe' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'];
        }

        if ($emitidas->contains(fn (CredencialPresidio $item) => $item->data_vencimento?->gte($hoje))) {
            return ['rotulo' => 'Válida', 'classe' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'];
        }

        if ($emitidas->isEmpty()) {
            return ['rotulo' => 'Unidade não emite', 'classe' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300'];
        }

        return ['rotulo' => 'Sem validade', 'classe' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'];
    }
}
