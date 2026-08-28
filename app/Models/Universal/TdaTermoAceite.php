<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TdaTermoAceite extends Model
{
    public const ADESAO = 'adesao_servico_voluntario';
    public const IMAGEM_VOZ = 'cessao_imagem_voz';
    public const UNIFORME = 'utilizacao_uniforme';

    public const VERSOES = [
        self::ADESAO => 'V.2209/25',
        self::IMAGEM_VOZ => 'V.1308/25',
        self::UNIFORME => 'V.1908/25',
    ];

    protected $table = 'tda_termo_aceites';

    protected $fillable = [
        'captacao_tda_id', 'cadastro_tda_id', 'tipo', 'versao',
        'hash_documento', 'hash_assinatura', 'aceito_em', 'ip_hash',
        'user_agent', 'dados_snapshot', 'pdf_assinado', 'revogado_em',
        'motivo_revogacao',
    ];

    protected $casts = [
        'aceito_em' => 'datetime',
        'revogado_em' => 'datetime',
        'dados_snapshot' => 'array',
    ];

    public function captacaoTda(): BelongsTo
    {
        return $this->belongsTo(CaptacaoTda::class);
    }

    public function cadastroTda(): BelongsTo
    {
        return $this->belongsTo(CadastroTda::class);
    }
}
