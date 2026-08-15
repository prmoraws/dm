<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\DadosOficiais;
use App\Models\Politica\V2\TseImportacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaDadosOficiaisTelaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tela_oficial_mostra_auditoria_sem_disparar_download(): void
    {
        TseImportacao::query()->create([
            'execucao' => '00000000-0000-0000-0000-000000000001',
            'ano' => 2022,
            'uf' => 'BA',
            'tipo' => 'candidaturas',
            'escopo' => 'espelho',
            'status' => 'concluida',
            'linhas_lidas' => 100,
            'linhas_selecionadas' => 20,
            'iniciada_em' => now(),
            'concluida_em' => now(),
        ]);

        Livewire::test(DadosOficiais::class)
            ->assertSee('Cobertura oficial no banco')
            ->assertSee('2022')
            ->assertSee('candidaturas')
            ->assertSee('20');
    }
}
