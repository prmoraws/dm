<?php

namespace Tests\Feature;

use App\Livewire\Unp\CaptacaoCursoUnpWizard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptacaoCursoUnpWizardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function rota_publica_do_curso_unp_carrega(): void
    {
        $this->get(route('curso-unp.inscricao'))
            ->assertOk()
            ->assertSee('Inscrição de novos voluntários da UNP')
            ->assertSee('Curso Preparatório de Voluntários')
            ->assertSee('images/unp/curso-unp-compartilhamento.png', false)
            ->assertSee('images/unp/unp-logo.png', false);
    }

    #[Test]
    public function aceite_de_privacidade_e_obrigatorio(): void
    {
        Livewire::test(CaptacaoCursoUnpWizard::class)
            ->call('nextStep')
            ->assertHasErrors(['lgpd_aceito' => 'accepted'])
            ->set('lgpd_aceito', true)
            ->call('nextStep')
            ->assertSet('step', 2);
    }

    #[Test]
    public function bloco_regiao_e_igreja_sao_obrigatorios(): void
    {
        Livewire::test(CaptacaoCursoUnpWizard::class)
            ->set('step', 2)
            ->call('nextStep')
            ->assertHasErrors(['bloco_id', 'regiao_id', 'igreja_id']);
    }

    #[Test]
    public function datas_de_batismo_sao_condicionais(): void
    {
        Livewire::test(CaptacaoCursoUnpWizard::class)
            ->set('step', 4)
            ->set('batizado_aguas', '1')
            ->set('batizado_espirito_santo', '1')
            ->set('mes_ingresso_igreja', 1)
            ->set('ano_ingresso_igreja', 2020)
            ->call('nextStep')
            ->assertHasErrors(['data_batismo_aguas', 'data_batismo_espirito_santo'])
            ->assertSet('batizado_aguas', '1')
            ->assertSet('batizado_espirito_santo', '1');
    }

    #[Test]
    public function link_do_grupo_esta_configurado(): void
    {
        $this->assertSame(
            'https://chat.whatsapp.com/Kke7dGlwvV3EgAAsUHvG0X?mode=gi_t',
            config('curso_unp.whatsapp_url')
        );
    }
}
