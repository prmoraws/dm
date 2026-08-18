<?php

namespace Tests\Feature;

use App\Models\Politica\V2\Acompanhamento;
use App\Models\Politica\V2\Apuracao;
use App\Models\Politica\V2\ApuracaoHistorico;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\TseApuracaoCollectorService;
use App\Services\Politica\V2\TseApuracaoOrchestrator;
use App\Services\Politica\V2\TseApuracaoUrlBuilder;
use App\Services\Politica\V2\TseEa14Parser;
use App\Services\Politica\V2\TseEa20Parser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class PoliticaApuracao2026Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function monta_urls_oficiais_sem_hardcode_do_codigo_da_eleicao(): void
    {
        $eleicao = Eleicao::query()->create([
            'tse_eleicao_codigo' => '987',
            'ano' => 2026,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Teste 2026',
        ]);
        $presidente = Cargo::query()->create(['tse_codigo' => '0001', 'nome' => 'Presidente', 'ordem' => 10]);
        $governador = Cargo::query()->create(['tse_codigo' => '0003', 'nome' => 'Governador', 'ordem' => 20]);

        $urls = app(TseApuracaoUrlBuilder::class);

        $this->assertSame(
            'https://resultados.tse.jus.br/oficial/ele2026/987/dados/br/br-e000987-ab.json',
            $urls->ea14($eleicao),
        );
        $this->assertSame(
            'https://resultados.tse.jus.br/oficial/ele2026/987/dados/br/br-c0001-e000987-u.json',
            $urls->ea20($eleicao, $presidente, 'BA'),
        );
        $this->assertSame(
            'https://resultados.tse.jus.br/oficial/ele2026/987/dados/ba/ba-c0003-e000987-u.json',
            $urls->ea20($eleicao, $governador, 'BA'),
        );
    }

    #[Test]
    public function interpreta_acompanhamento_e_resultado_unificado_em_formato_tse(): void
    {
        $ea14 = app(TseEa14Parser::class)->parse([
            'ele' => '987', 't' => '1', 'f' => 'o', 'dg' => '04/10/2026', 'hg' => '18:00:00', 'idg' => 44,
            'abr' => [
                ['and' => 'p', 'tpabr' => 'br', 'cdabr' => 'br', 'dt' => '04/10/2026', 'ht' => '17:59:00', 's' => ['ts' => 100, 'st' => 40, 'snt' => 60, 'pstn' => '40.000000000'], 'e' => ['te' => 1000, 'c' => 350, 'a' => 50]],
                ['and' => 'p', 'tpabr' => 'uf', 'cdabr' => 'ba', 'dt' => '04/10/2026', 'ht' => '17:58:00', 's' => ['ts' => 20, 'st' => 10, 'snt' => 10, 'pstn' => '50.000000000'], 'e' => ['te' => 200, 'c' => 80, 'a' => 20]],
            ],
        ], 'BA');

        $this->assertSame('p', $ea14['br']['andamento']);
        $this->assertSame(40.0, $ea14['br']['percentual_secoes']);
        $this->assertSame(50.0, $ea14['uf']['percentual_secoes']);

        $ea20 = app(TseEa20Parser::class)->parse($this->payloadEa20());
        $this->assertSame('p', $ea20['andamento']);
        $this->assertSame(40.0, $ea20['percentual_secoes']);
        $this->assertCount(2, $ea20['candidatos']);
        $this->assertSame('1001', $ea20['candidatos'][0]['sq_candidato']);
        $this->assertSame(12345, $ea20['candidatos'][0]['votos']);
        $this->assertSame(1, $ea20['candidatos'][0]['posicao']);
    }

    #[Test]
    public function persiste_apenas_candidatura_conhecida_cria_checkpoint_e_snapshot(): void
    {
        Storage::fake('local');

        $eleicao = Eleicao::query()->create([
            'tse_eleicao_codigo' => '987', 'ano' => 2026, 'turno' => 1, 'tipo' => 'geral', 'descricao' => 'Teste 2026',
        ]);
        $cargo = Cargo::query()->create(['tse_codigo' => '0001', 'nome' => 'Presidente', 'ordem' => 10]);
        $partido = Partido::query()->create(['numero' => 13, 'sigla' => 'PT', 'nome' => 'PT']);
        $politico = Politico::query()->create(['nome_completo' => 'Candidato Um', 'nome_publico' => 'Candidato Um', 'slug' => 'candidato-um']);
        Acompanhamento::query()->create(['politico_id' => $politico->id, 'grupo' => 'presidencia', 'prioridade' => 1, 'ordem' => 1, 'ativo' => true]);
        $candidatura = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'tse_sq_candidato' => '1001',
            'numero_urna' => '13',
            'nome_urna' => 'CANDIDATO UM',
            'uf' => 'BR',
            'origem' => 'tse_dados_abertos',
        ]);

        $resultado = app(TseApuracaoCollectorService::class)->persistir($eleicao, $cargo, $this->payloadEa20(), 'BR');

        $this->assertSame(1, $resultado['candidatos_vinculados']);
        $this->assertSame(1, $resultado['candidatos_nao_vinculados']);
        $this->assertDatabaseHas('politica_apuracoes', ['eleicao_id' => $eleicao->id, 'cargo_id' => $cargo->id, 'status' => 'em_andamento']);
        $this->assertDatabaseHas('politica_apuracao_candidaturas', ['candidatura_id' => $candidatura->id, 'votos' => 12345]);
        $this->assertSame(12345, $candidatura->fresh()->votos_total);
        $this->assertSame(1, ApuracaoHistorico::query()->count());
        Storage::disk('local')->assertExists($resultado['snapshot']);
    }

    #[Test]
    public function coleta_real_nunca_faz_http_enquanto_live_estiver_desabilitado(): void
    {
        config()->set('politica.apuracao.live_enabled', false);
        Http::fake();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Coleta ao vivo desabilitada');

        try {
            app(TseApuracaoOrchestrator::class)->coletar(2026, 1, 'BA');
        } finally {
            Http::assertNothingSent();
        }
    }

    private function payloadEa20(): array
    {
        return [
            'ele' => '987',
            'idg' => 55,
            'dg' => '04/10/2026',
            'hg' => '18:01:00',
            'and' => 'p',
            's' => ['ts' => 100, 'st' => 40, 'snt' => 60, 'pstn' => '40.000000000'],
            'e' => ['te' => 1000, 'c' => 350, 'a' => 50],
            'carper' => [[
                'cd' => '1',
                'cand' => [
                    ['sqcand' => '1001', 'n' => '13', 'nm' => 'CANDIDATO UM', 'cc' => 'PT', 'vap' => '12345', 'pvapn' => '55.250000000', 'e' => 'n', 'st' => 'Válido'],
                    ['sqcand' => '9999', 'n' => '99', 'nm' => 'FORA DO RECORTE', 'cc' => 'XX', 'vap' => '10000', 'pvapn' => '44.750000000', 'e' => 'n', 'st' => 'Válido'],
                ],
            ]],
        ];
    }
}
