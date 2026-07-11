<?php

namespace App\Models\Unp;

use App\Models\Universal\Bloco;
use App\Models\Unp\Presidio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batismo extends Model
{
    protected $table = 'batismos';

    protected $fillable = ['nome', 'bloco_id', 'presidio_id', 'quantidade', 'data_batismo'];

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function presidio(): BelongsTo
    {
        return $this->belongsTo(Presidio::class);
    }
}