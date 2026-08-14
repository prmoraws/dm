<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use Illuminate\Database\Eloquent\Model;

class Apuracao extends Model
{
    protected $table = 'politica_apuracoes';
    protected $fillable = [
        'eleicao_id', 'cargo_id', 'abrangencia_tipo', 'abrangencia_chave', 'uf', 'cidade_id',
        'zona_id', 'tse_idg', 'gerado_tse_em', 'status', 'secoes_total', 'secoes_totalizadas',
        'secoes_nao_totalizadas', 'percentual_secoes', 'eleitores_total', 'comparecimento',
        'abstencoes', 'totalizacao_final', 'sincronizado_em',
    ];
    protected $casts = [
        'gerado_tse_em' => 'datetime',
        'percentual_secoes' => 'decimal:4',
        'totalizacao_final' => 'boolean',
        'sincronizado_em' => 'datetime',
    ];

    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function cargo() { return $this->belongsTo(Cargo::class, 'cargo_id'); }
    public function cidade() { return $this->belongsTo(Cidade::class, 'cidade_id'); }
    public function zona() { return $this->belongsTo(Zona::class, 'zona_id'); }
    public function candidaturas() { return $this->hasMany(ApuracaoCandidatura::class, 'apuracao_id'); }
    public function historico() { return $this->hasMany(ApuracaoHistorico::class, 'apuracao_id'); }
}
