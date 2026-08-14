<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use Illuminate\Database\Eloquent\Model;

class EspelhoOperacional extends Model
{
    protected $table = 'politica_espelho_operacional';

    protected $fillable = [
        'cidade_id',
        'legacy_espelho_id',
        'presidente_local',
        'indicacao_bispo',
        'filiados_republicanos',
        'observacoes',
        'dados_publicos_legados',
        'revisado_em',
    ];

    protected $casts = [
        'filiados_republicanos' => 'integer',
        'dados_publicos_legados' => 'array',
        'revisado_em' => 'datetime',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }
}
