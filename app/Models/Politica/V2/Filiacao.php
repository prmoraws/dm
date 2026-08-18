<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class Filiacao extends Model
{
    protected $table = 'politica_filiacoes';
    protected $fillable = [
        'politico_id', 'partido_id', 'data_inicio', 'data_fim', 'ano_inicio', 'ano_fim',
        'periodo_texto', 'observacoes', 'fonte', 'fonte_id', 'fonte_url', 'fonte_oficial',
    ];
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ano_inicio' => 'integer',
        'ano_fim' => 'integer',
        'fonte_oficial' => 'boolean',
    ];

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
    public function partido() { return $this->belongsTo(Partido::class, 'partido_id'); }

    public function fonteLabel(): string
    {
        return match (strtoupper((string) $this->fonte)) {
            'ALBA' => 'Assembleia Legislativa da Bahia',
            'TSE', 'TSE_DADOS_ABERTOS' => 'Dados Abertos TSE',
            'ALBA_TSE', 'TSE_ALBA' => 'ALBA + Dados Abertos TSE',
            default => $this->fonte ?: 'Fonte não informada',
        };
    }

    public function periodoExibicao(): string
    {
        if ($this->periodo_texto) {
            return $this->periodo_texto;
        }

        if ($this->ano_inicio || $this->ano_fim) {
            return trim(($this->ano_inicio ?: '—').'–'.($this->ano_fim ?: 'atual'));
        }

        if ($this->data_inicio || $this->data_fim) {
            return trim(($this->data_inicio?->format('d/m/Y') ?: '—').'–'.($this->data_fim?->format('d/m/Y') ?: 'atual'));
        }

        return 'Período não informado';
    }
}
