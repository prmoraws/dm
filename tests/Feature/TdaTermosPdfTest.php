<?php

namespace Tests\Feature;

use App\Http\Controllers\Universal\CadastroTdaTermoPdfController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TdaTermosPdfTest extends TestCase
{
    #[Test]
    public function rotas_dos_termos_foram_retiradas_e_pdf_cadastral_permanece(): void
    {
        foreach ([
            'universal.cadastros-tda.termos.visualizar-todos',
            'universal.cadastros-tda.termos.baixar-todos',
            'universal.cadastros-tda.termos.visualizar',
            'universal.cadastros-tda.termos.baixar',
        ] as $nome) {
            $this->assertFalse(\Illuminate\Support\Facades\Route::has($nome));
        }
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('universal.cadastros-tda.pdf.visualizar'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('universal.cadastros-tda.pdf.baixar'));
    }

    #[Test]
    public function somente_os_tres_tipos_oficiais_estao_configurados(): void
    {
        $this->assertSame([
            'adesao_servico_voluntario',
            'cessao_imagem_voz',
            'utilizacao_uniforme',
        ], array_keys(config('tda.termos')));

        $this->assertFileExists(resource_path('views/livewire/universal/pdf/termos-tda.blade.php'));
    }

    #[Test]
    public function termos_usam_fundo_oficial_preenchido_sem_pagina_de_comprovante(): void
    {
        $template = file_get_contents(resource_path('views/livewire/universal/pdf/termos-tda.blade.php'));

        $this->assertStringContainsString('class="background"', $template);
        $this->assertStringContainsString('class="field', $template);
        $this->assertStringContainsString('$assinaturaVoluntario', $template);
        $this->assertStringContainsString('$assinaturaPastor', $template);
        $this->assertStringContainsString('$assinaturaTestemunha', $template);
        $this->assertStringNotContainsString('COMPROVANTE DE ACEITE E ASSINATURAS', $template);
        $this->assertStringNotContainsString('class="cert"', $template);
    }
}
