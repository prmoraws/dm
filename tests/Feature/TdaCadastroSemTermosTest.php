<?php

namespace Tests\Feature;

use App\Livewire\Universal\CaptacaoTdaWizard;
use App\Livewire\Universal\GestaoCaptacoesTda;
use App\Livewire\Universal\CadastrosTda;
use App\Models\Adm\Estado;
use App\Models\Adm\Cidade;
use App\Models\Universal\{Bloco, Regiao, Igreja, CaptacaoTda, CadastroTda};
use App\Models\{User, Team};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TdaCadastroSemTermosTest extends TestCase
{
    use RefreshDatabase;

    private function dados(): array
    {
        $bloco = Bloco::create(['nome' => 'Bloco Teste']);
        $regiao = Regiao::create(['nome' => 'Região Teste', 'bloco_id' => $bloco->id]);
        $igreja = Igreja::create(['nome' => 'Igreja Teste', 'bloco_id' => $bloco->id, 'regiao_id' => $regiao->id]);
        $estado = Estado::create(['nome' => 'Bahia', 'uf' => 'BA']);
        $cidade = Cidade::create(['nome' => 'Salvador', 'estado_id' => $estado->id, 'cidade_id' => 2927408]);
        return [
            'bloco_id' => $bloco->id, 'regiao_id' => $regiao->id, 'igreja_id' => $igreja->id,
            'estado_id' => $estado->id, 'cidade_id' => $cidade->id,
            'endereco_igreja' => 'Rua da Igreja, 10', 'funcao_grupo' => 'Membro',
            'data_ingresso_grupo' => '2021-01-01', 'rg' => '123456789', 'cep' => '40000000',
            'nome' => 'Maria de Teste', 'nacionalidade' => 'Brasileira', 'data_nascimento' => '1990-01-01',
            'estado_civil' => 'solteiro', 'cpf' => '52998224725', 'celular' => '71999999999',
            'sexo' => 'feminino', 'endereco' => 'Rua Teste', 'numero' => '1', 'bairro' => 'Centro',
            'escolaridade' => 'Ensino médio', 'tem_filhos' => false,
            'emergencia_nome' => 'Contato Teste', 'emergencia_celular' => '71988888888',
            'condicao_atual' => 'membro', 'inicio_iurd' => '2020-01-01',
            'batizado_aguas' => false, 'batizado_espirito_santo' => false, 'ja_se_afastou' => false,
            'dias_reunioes' => ['domingo'], 'dias_evangelizacao' => ['sabado'],
            'godllywood_autoajuda' => false, 'meditacao_univer' => false,
            'colaborador' => false, 'obreiro' => false, 'levita' => false,
        ];
    }

    public static function participantes(): array
    {
        return [['1990-01-01'], ['2010-01-01']];
    }

    #[DataProvider('participantes')]
    public function test_envia_cadastro_sem_termos_assinaturas_ou_testemunha(string $nascimento): void
    {
        Storage::fake('public_disk');
        RateLimiter::clear('captacao-tda:envio:127.0.0.1');
        $dados = $this->dados();
        $dados['data_nascimento'] = $nascimento;
        $component = Livewire::test(CaptacaoTdaWizard::class);
        foreach ($dados as $campo => $valor) $component->set($campo, $valor);
        $component->set('foto', UploadedFile::fake()->image('foto.jpg'))
            ->set('lgpd_aceito', true)->set('step', 9)->call('submit')
            ->assertHasNoErrors()->assertSet('enviado', true);
        $captacao = CaptacaoTda::sole();
        $this->assertNull($captacao->assinatura);
        $this->assertNull($captacao->testemunha_assinatura);
        $this->assertDatabaseCount('tda_termo_aceites', 0);
        $this->assertDatabaseCount('tda_responsaveis_legais', 0);
        Storage::disk('public_disk')->assertExists($captacao->foto);
        $component->call('submit');
        $this->assertDatabaseCount('captacao_tdas', 1);
    }

    public static function historicos(): array
    {
        return [[false], [true]];
    }

    #[DataProvider('historicos')]
    public function test_aprova_sem_exigir_termos_e_preserva_assinaturas_antigas(bool $historico): void
    {
        Storage::fake('public_disk');
        $dados = $this->dados();
        $dados['foto'] = 'tda/captacao/foto.jpg';
        Storage::disk('public_disk')->put($dados['foto'], 'foto teste');
        if ($historico) {
            $dados['assinatura'] = 'tda/captacao/assinatura.png';
            $dados['testemunha_assinatura'] = 'tda/captacao/testemunha.png';
            Storage::disk('public_disk')->put($dados['assinatura'], 'assinatura antiga');
            Storage::disk('public_disk')->put($dados['testemunha_assinatura'], 'testemunha antiga');
        }
        $captacao = CaptacaoTda::create($dados);
        $user = User::forceCreate([
            'name' => 'Gestor TDA Teste',
            'email' => 'gestor-tda@example.com',
            'password' => bcrypt('senha-apenas-para-teste'),
        ]);
        $team = Team::forceCreate(['name' => 'TDA', 'user_id' => $user->id, 'personal_team' => false]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $falhasAprovacao = [];
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Log\Events\MessageLogged::class,
            function ($evento) use (&$falhasAprovacao): void {
                if ($evento->message === 'Falha ao aprovar TDA') {
                    $falhasAprovacao[] = $evento->context;
                }
            }
        );
        $this->actingAs($user);
        Livewire::test(GestaoCaptacoesTda::class)->call('aprovar', $captacao->id)->assertHasNoErrors();
        $this->assertSame(
            'aprovado',
            $captacao->fresh()->status,
            'Diagnóstico: ' . json_encode([
                'mensagem' => session('error'),
                'excecoes' => $falhasAprovacao,
            ], JSON_UNESCAPED_UNICODE)
        );
        $cadastro = CadastroTda::sole();
        $this->assertSame($captacao->id, $cadastro->captacao_tda_id);
        $this->assertDatabaseCount('tda_termo_aceites', 0);
        if ($historico) {
            $this->assertSame($dados['assinatura'], $cadastro->assinatura);
            $this->assertSame('assinatura antiga', Storage::disk('public_disk')->get($dados['assinatura']));
            $this->assertSame('testemunha antiga', Storage::disk('public_disk')->get($dados['testemunha_assinatura']));
        }
        Livewire::test(CadastrosTda::class)->call('visualizar', $cadastro->id)
            ->assertSee('Visualizar PDF')->assertDontSee('Termos assinados');
    }
}
