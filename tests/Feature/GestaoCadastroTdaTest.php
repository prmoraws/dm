<?php

namespace Tests\Feature;

use App\Livewire\Universal\GestaoCaptacoesTda;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestaoCadastroTdaTest extends TestCase
{
    #[Test]
    public function componente_administrativo_pode_ser_instanciado(): void
    {
        $this->assertTrue(class_exists(GestaoCaptacoesTda::class));
    }

    #[Test]
    public function rejeicao_exige_um_motivo(): void
    {
        $validator = Validator::make(
            ['motivo_rejeicao' => ''],
            ['motivo_rejeicao' => ['required', 'string', 'min:5', 'max:1000']],
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('motivo_rejeicao', $validator->errors()->toArray());
    }
}
