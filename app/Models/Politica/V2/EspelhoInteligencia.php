<?php

namespace App\Models\Politica\V2;

use App\Models\Politica\Cidade;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EspelhoInteligencia extends Model
{
    protected $table = 'politica_espelho_inteligencia';
    protected $fillable = [
        'cidade_id', 'eleicao_id', 'candidatura_id', 'responsavel_user_id', 'contexto_chave', 'classificacao',
        'prioridade', 'meta_votos', 'meta_percentual', 'observacoes', 'revisado_em',
    ];
    protected $casts = ['meta_percentual' => 'decimal:4', 'revisado_em' => 'datetime'];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->candidatura_id) {
                $model->contexto_chave = 'eleicao:'.($model->eleicao_id ?? 'na').':candidatura:'.$model->candidatura_id;
            } elseif ($model->eleicao_id) {
                $model->contexto_chave = 'eleicao:'.$model->eleicao_id;
            } else {
                $model->contexto_chave = 'geral';
            }
        });
    }

    public function cidade() { return $this->belongsTo(Cidade::class, 'cidade_id'); }
    public function eleicao() { return $this->belongsTo(Eleicao::class, 'eleicao_id'); }
    public function candidatura() { return $this->belongsTo(Candidatura::class, 'candidatura_id'); }
    public function responsavel() { return $this->belongsTo(User::class, 'responsavel_user_id'); }
}
