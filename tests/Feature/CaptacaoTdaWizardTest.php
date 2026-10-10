<?php

namespace Tests\Feature;

use App\Livewire\Universal\CaptacaoTdaWizard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaptacaoTdaWizardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function rota_publica_do_cadastro_tda_carrega(): void
    {
        $this->get(route('captacao.tda.create'))
            ->assertOk()
            ->assertSee('Ficha de Cadastro TDA');
    }

    #[Test]
    public function aceite_de_privacidade_e_obrigatorio_para_avancar(): void
    {
        Livewire::test(CaptacaoTdaWizard::class)
            ->call('nextStep')
            ->assertHasErrors(['lgpd_aceito' => 'accepted'])
            ->set('lgpd_aceito', true)
            ->call('nextStep')
            ->assertSet('step', 2);
    }

    #[Test]
    public function segunda_etapa_exige_bloco_regiao_igreja_e_funcao(): void
    {
        Livewire::test(CaptacaoTdaWizard::class)
            ->set('lgpd_aceito', true)
            ->call('nextStep')
            ->call('nextStep')
            ->assertHasErrors(['bloco_id', 'regiao_id', 'igreja_id', 'funcao_grupo']);
    }

    #[Test]
    public function alteracao_de_estado_limpa_a_cidade_anterior(): void
    {
        Livewire::test(CaptacaoTdaWizard::class)
            ->set('cidade_id', 999)
            ->set('estado_id', null)
            ->assertSet('cidade_id', null);
    }

    #[Test]
    public function datas_e_email_permanecem_ao_voltar_entre_etapas(): void
    {
        Livewire::test(CaptacaoTdaWizard::class)
            ->set('data_nascimento', '1990-05-20')
            ->set('email', 'teste@example.com')
            ->set('inicio_iurd', '2010-03-15')
            ->set('step', 6)
            ->call('previousStep')
            ->assertSet('data_nascimento', '1990-05-20')
            ->assertSet('email', 'teste@example.com')
            ->assertSet('inicio_iurd', '2010-03-15');
    }

    #[Test]
    public function cadastro_tem_nove_etapas_e_revisao_sem_termos(): void
    {
        $component = Livewire::test(CaptacaoTdaWizard::class)
            ->assertSet('totalSteps', 9)
            ->set('step', 9)
            ->assertSee('Revisão e envio')
            ->assertSee('Enviar cadastro');
        $html = $component->html();
        $this->assertStringNotContainsString('aceitarTermo', $html);
        $this->assertStringNotContainsString('<canvas', $html);
        $this->assertStringNotContainsString('testemunha', $html);
    }

    #[Test]
    public function ultima_etapa_de_dados_avanca_diretamente_para_revisao(): void
    {
        Livewire::test(CaptacaoTdaWizard::class)
            ->set('sexo', 'masculino')
            ->set('intellimen_reunioes', false)
            ->set('intellimen_desafios', false)
            ->set('step', 8)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 9)
            ->call('previousStep')
            ->assertSet('step', 8);
    }
}
