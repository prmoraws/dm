<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Candidatura extends Model
{
    use HasFactory;

    protected $table = 'politica_candidaturas';
    protected $fillable = [
        'politico_id', 'eleicao_id', 'cargo_id', 'partido_id', 'tse_sq_candidato', 'tse_chave_historica',
        'numero_urna', 'nome_urna', 'uf', 'cidade_id', 'origem', 'legacy_candidato_id', 'origem_chave', 'situacao_registro', 'situacao_eleicao',
        'coligacao', 'federacao', 'votos_total', 'percentual_total', 'eleito',
        'segundo_turno', 'foto_url', 'sincronizado_em',
    ];
    protected $casts = [
        'votos_total' => 'integer',
        'percentual_total' => 'decimal:4',
        'eleito' => 'boolean',
        'segundo_turno' => 'boolean',
        'sincronizado_em' => 'datetime',
    ];


    public function isRegistroOficialTse(): bool
    {
        return $this->origem === 'tse_dados_abertos'
            && (filled($this->tse_sq_candidato) || filled($this->tse_chave_historica));
    }

    public function situacaoRegistroEhCodigoTecnico(): bool
    {
        $situacao = trim((string) $this->situacao_registro);

        return $situacao === '' || Str::startsWith($situacao, '#');
    }

    public function situacaoRegistroExibicao(): string
    {
        $situacao = trim((string) $this->situacao_registro);

        if ($situacao === '') {
            return 'Aguardando situação do TSE';
        }

        if (Str::startsWith($situacao, '#')) {
            return 'Situação ainda não disponibilizada';
        }

        return $situacao;
    }

    public function situacaoRegistroTom(): string
    {
        if ($this->situacaoRegistroEhCodigoTecnico()) {
            return 'amber';
        }

        $situacao = Str::upper((string) $this->situacao_registro);

        if (Str::contains($situacao, ['INDEFER', 'CANCEL', 'RENÚNCIA', 'RENUNCIA', 'FALEC', 'INAPTO'])) {
            return 'rose';
        }

        if (Str::contains($situacao, ['DEFER', 'APTO'])) {
            return 'emerald';
        }

        return 'slate';
    }

    public function politico() { return $this->belongsTo(Politico::class, 'politico_id'); }
    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function cargo() { return $this->belongsTo(Cargo::class, 'cargo_id'); }
    public function partido() { return $this->belongsTo(Partido::class, 'partido_id'); }
    public function cidade() { return $this->belongsTo(Cidade::class, 'cidade_id'); }
    public function resultadosMunicipais() { return $this->hasMany(ResultadoMunicipal::class, 'candidatura_id'); }
    public function resultadosZonas() { return $this->hasMany(ResultadoZona::class, 'candidatura_id'); }
    public function resultadosSecoes() { return $this->hasMany(ResultadoSecao::class, 'candidatura_id'); }
    public function apuracoes() { return $this->hasMany(ApuracaoCandidatura::class, 'candidatura_id'); }
}
