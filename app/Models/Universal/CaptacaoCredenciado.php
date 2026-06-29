<?php

namespace App\Models\Universal;

use Illuminate\Database\Eloquent\Model;

class CaptacaoCredenciado extends Model
{
    protected $fillable = [
        'nome', 'celular', 'email', 'cpf', 'bloco_id', 'regiao_id', 'igreja_id',
        'categoria_id', 'cargo_id', 'grupo_id', 'estado_id', 'cidade_id',
        'endereco', 'bairro', 'cep', 'profissao', 'aptidoes', 'conversao',
        'obra', 'testemunho', 'foto', 'identidade_frente', 'identidade_verso',
        'trabalho', 'batismo', 'preso', 'credenciais_payload', 'status'
    ];

    protected $casts = [
        'trabalho' => 'array',
        'batismo' => 'array',
        'preso' => 'array',
        'credenciais_payload' => 'array', // Crucial para as múltiplas credenciais
        'conversao' => 'date',
        'obra' => 'date'
    ];
}