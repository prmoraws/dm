<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage; // 1. Adicione este import

class PastorUnp extends Model
{
    use HasFactory;

    protected $table = 'pastor_unps';

    protected $fillable = [
        'bloco_id',
        'regiao_id',
        'nome',
        'nascimento',
        'email',
        'whatsapp',
        'telefone',
        'cargo',
        'chegada',
        'entrada',
        'preso',
        'trabalho',
        'foto',
        'nome_esposa',
        'email_esposa',
        'whatsapp_esposa',
        'telefone_esposa',
        'obra',
        'casado',
        'consagrada_esposa',
        'preso_esposa',
        'trabalho_esposa',
        'foto_esposa',
    ];

    protected $casts = [
        'nascimento' => 'date',
        'entrada' => 'date',
        'preso' => 'boolean',
        // CORREÇÃO: O cast para 'boolean' foi removido do campo 'casado'
        'consagrada_esposa' => 'boolean',
        'preso_esposa' => 'boolean',
        'trabalho' => 'array',
        'trabalho_esposa' => 'array',
    ];
     /**
     * O método "booted" é executado quando o model é inicializado.
     */
    protected static function booted(): void
    {
        // Define uma ação a ser executada ANTES de um registro ser deletado
        static::deleting(function (PastorUnp $pastor) {
            // Verifica se este pastor tem um carro associado
            if ($pastor->carroUnp) {
                $carro = $pastor->carroUnp;
                
                // Lista de todos os campos de foto do carro
                $photoFields = ['foto_frente', 'foto_tras', 'foto_direita', 'foto_esquerda', 'foto_dentro', 'foto_cambio'];
                
                // Deleta cada foto do armazenamento
                foreach ($photoFields as $field) {
                    if ($carro->$field) {
                        Storage::disk('public_disk')->delete($carro->$field);
                    }
                }
                
                // Deleta o registro do carro do banco de dados
                $carro->delete();
            }
        });
    }

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class);
    }
    /**
     * Define a relação com o CarroUnp.
     */
    public function carroUnp(): HasOne
    {
        return $this->hasOne(CarroUnp::class, 'pastor_unp_id');
    }
}

