<?php

namespace App\Models\Unp;

use App\Models\Universal\Bloco;
use App\Models\Universal\Igreja;
use App\Models\Universal\Regiao;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoUnpCaptacao extends Model
{
    protected $table = 'curso_unp_captacoes';

    protected $fillable = [
        'protocolo', 'bloco_id', 'regiao_id', 'igreja_id', 'foto', 'nome', 'celular',
        'batizado_aguas', 'data_batismo_aguas', 'batizado_espirito_santo',
        'data_batismo_espirito_santo', 'estado_civil', 'casado_civil',
        'casado_igreja', 'endereco_completo', 'mes_ingresso_igreja',
        'ano_ingresso_igreja', 'status', 'lgpd_aceito_em', 'ip_hash', 'user_agent',
        'motivo_rejeicao', 'revisado_por', 'revisado_em',
    ];

    protected $casts = [
        'batizado_aguas' => 'boolean',
        'data_batismo_aguas' => 'date',
        'batizado_espirito_santo' => 'boolean',
        'data_batismo_espirito_santo' => 'date',
        'casado_civil' => 'boolean',
        'casado_igreja' => 'boolean',
        'mes_ingresso_igreja' => 'integer',
        'ano_ingresso_igreja' => 'integer',
        'lgpd_aceito_em' => 'datetime',
        'revisado_em' => 'datetime',
    ];

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class);
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(CursoUnpMatricula::class, 'captacao_id');
    }
}
