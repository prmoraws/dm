<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarroUnp extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'carro_unps';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pastor_unp_id',
        'bloco_id',
        'modelo',
        'ano',
        'placa',
        'km',
        'demanda',
        'foto_frente',
        'foto_tras',
        'foto_direita',
        'foto_esquerda',
        'foto_dentro',
        'foto_cambio',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'km' => 'integer',
    ];

    /**
     * Define a relação com PastorUnp.
     */
    public function pastorUnp(): BelongsTo
    {
        return $this->belongsTo(PastorUnp::class, 'pastor_unp_id');
    }

    /**
     * Define a relação com o Bloco.
     */
    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }
}