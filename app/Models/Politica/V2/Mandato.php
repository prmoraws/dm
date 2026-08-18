<?php

namespace App\Models\Politica\V2;

use Illuminate\Database\Eloquent\Model;

class Mandato extends Model
{
    protected $table = 'politica_mandatos';
    protected $fillable = [
        'politico_id', 'cargo_id', 'partido_id', 'eleicao_origem_id', 'fonte_id',
        'tipo', 'legislatura', 'esfera', 'uf', 'ano_inicio', 'ano_fim', 'periodo_texto',
        'data_inicio', 'data_fim', 'situacao', 'detalhes', 'fonte', 'fonte_url', 'fonte_oficial',
    ];
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ano_inicio' => 'integer',
        'ano_fim' => 'integer',
        'fonte_oficial' => 'boolean',
    ];

    public function periodoExibicao(): string
    {
        if ($this->periodo_texto) {
            return $this->periodo_texto;
        }

        if ($this->data_inicio || $this->data_fim) {
            $inicio = $this->data_inicio?->format('d/m/Y') ?: '—';
            $fim = $this->data_fim?->format('d/m/Y') ?: 'atual';

            return $inicio.' – '.$fim;
        }

        if ($this->ano_inicio || $this->ano_fim) {
            return trim(($this->ano_inicio ?: '—').' – '.($this->ano_fim ?: 'atual'));
        }

        return 'Período não informado';
    }

    public function fonteLabel(): string
    {
        return match ($this->fonte) {
            'CAMARA_DOS_DEPUTADOS' => 'Câmara dos Deputados',
            'ALBA' => 'Assembleia Legislativa da Bahia',
            default => $this->fonte ?: 'Fonte não informada',
        };
    }

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
    public function cargo() { return $this->belongsTo(Cargo::class, 'cargo_id'); }
    public function partido() { return $this->belongsTo(Partido::class, 'partido_id'); }
    public function eleicaoOrigem() { return $this->belongsTo(Eleicao::class, 'eleicao_origem_id'); }
}
