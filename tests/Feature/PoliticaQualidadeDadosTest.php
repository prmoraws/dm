<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\QualidadeDados;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\EspelhoInteligencia;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Models\Politica\V2\ResultadoZona;
use App\Models\Politica\V2\Zona;
use App\Services\Politica\V2\PoliticaQualidadeDadosService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaQualidadeDadosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
        Cache::forget('politica:v2:qualidade-dados:v1');
    }

    #[Test]
    public function soma_municipal_maior_que_total_da_candidatura_e_critica(): void
    {
        [$politico, $cargo, $partido, $eleicao] = $this->cenarioBase();
        $cidade = $this->cidade('Cidade Auditoria', 400001);
        $candidatura = $this->candidatura($politico, $cargo, $partido, $eleicao, 100);
        $this->resultadoMunicipal($candidatura, $cidade, 120);

        $dados = app(PoliticaQualidadeDadosService::class)->snapshot(true);
        $achado = collect($dados['achados'])->firstWhere('codigo', 'eleitoral.soma_municipal_maior_total');

        $this->assertNotNull($achado);
        $this->assertSame('critico', $achado['nivel']);
        $this->assertStringContainsString('total=100', $achado['contexto']);
        $this->assertStringContainsString('soma municipal=120', $achado['contexto']);
    }

    #[Test]
    public function soma_zonal_maior_que_resultado_municipal_e_critica_sem_recalcular_votos(): void
    {
        [$politico, $cargo, $partido, $eleicao] = $this->cenarioBase();
        $cidade = $this->cidade('Cidade Zonal', 400011);
        $candidatura = $this->candidatura($politico, $cargo, $partido, $eleicao, 100);
        $this->resultadoMunicipal($candidatura, $cidade, 50);

        $zona = Zona::query()->create(['cidade_id' => $cidade->id, 'numero' => 1, 'tse_codigo' => '001']);
        ResultadoZona::query()->create([
            'eleicao_id' => $eleicao->id,
            'candidatura_id' => $candidatura->id,
            'zona_id' => $zona->id,
            'votos' => 60,
        ]);

        $dados = app(PoliticaQualidadeDadosService::class)->snapshot(true);
        $achado = collect($dados['achados'])->firstWhere('codigo', 'eleitoral.zonas_maior_municipio');

        $this->assertNotNull($achado);
        $this->assertSame('critico', $achado['nivel']);
        $this->assertStringContainsString('município=50', $achado['contexto']);
        $this->assertStringContainsString('zonas=60', $achado['contexto']);
        $this->assertSame(50, ResultadoMunicipal::query()->where('candidatura_id', $candidatura->id)->value('votos'));
    }

    #[Test]
    public function detecta_multiplos_favoritos_no_mesmo_municipio_eleicao_e_cargo(): void
    {
        [$politico, $cargo, $partido, $eleicao] = $this->cenarioBase();
        $cidade = $this->cidade('Cidade Favoritos', 400021);
        $c1 = $this->candidatura($politico, $cargo, $partido, $eleicao, 10);

        $outro = Politico::query()->create([
            'nome_completo' => 'Outro Candidato',
            'nome_publico' => 'Outro Candidato',
            'slug' => 'outro-candidato-auditoria',
        ]);
        $c2 = $this->candidatura($outro, $cargo, $partido, $eleicao, 20);

        foreach ([$c1, $c2] as $candidatura) {
            EspelhoInteligencia::query()->create([
                'cidade_id' => $cidade->id,
                'eleicao_id' => $eleicao->id,
                'candidatura_id' => $candidatura->id,
                'classificacao' => 'favorito',
                'prioridade' => 1,
            ]);
        }

        $dados = app(PoliticaQualidadeDadosService::class)->snapshot(true);
        $achado = collect($dados['achados'])->firstWhere('codigo', 'espelho.multiplos_favoritos');

        $this->assertNotNull($achado);
        $this->assertSame('critico', $achado['nivel']);
        $this->assertStringContainsString('CIDADE FAVORITOS', $achado['contexto']);
    }

    #[Test]
    public function componente_exibe_auditoria_no_corpo_e_paginas_principais_possuem_section_title(): void
    {
        app(PoliticaQualidadeDadosService::class)->snapshot(true);

        Livewire::test(QualidadeDados::class)
            ->assertSee('Auditoria automática da Política V2')
            ->assertSee('Achados rastreáveis')
            ->assertSee('Metodologia e segurança')
            ->assertDontSee('@endif', false);

        $views = [
            'resources/views/livewire/politica/v2/dashboard.blade.php',
            'resources/views/livewire/politica/v2/acompanhamento-prioritario.blade.php',
            'resources/views/livewire/politica/v2/politico-show.blade.php',
            'resources/views/livewire/politica/city-dashboard.blade.php',
            'resources/views/livewire/politica/v2/espelho-inteligente.blade.php',
            'resources/views/livewire/politica/v2/espelho-operacional-edit.blade.php',
            'resources/views/livewire/politica/v2/mapa-interativo.blade.php',
            'resources/views/livewire/politica/v2/dados-oficiais.blade.php',
            'resources/views/livewire/politica/v2/eleicoes-2026.blade.php',
            'resources/views/livewire/politica/v2/historico-acompanhados.blade.php',
            'resources/views/livewire/politica/v2/comparativo-territorial.blade.php',
            'resources/views/livewire/politica/v2/inteligencia-territorial.blade.php',
            'resources/views/livewire/politica/v2/painel-executivo.blade.php',
            'resources/views/livewire/politica/v2/qualidade-dados.blade.php',
        ];

        foreach ($views as $view) {
            $this->assertStringContainsString("@section('title'", file_get_contents(base_path($view)), $view);
        }
    }

    private function cenarioBase(): array
    {
        $politico = Politico::query()->where('slug', 'jurailton-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $partido = Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );
        $eleicao = Eleicao::query()->create([
            'ano' => 2022,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleição Auditoria',
            'status' => 'concluida',
        ]);

        return [$politico, $cargo, $partido, $eleicao];
    }

    private function cidade(string $nome, int $ibge): Cidade
    {
        return Cidade::query()->create([
            'nome' => $nome,
            'ibge_code' => $ibge,
            'tse_codigo' => (string) ($ibge % 100000),
            'latitude' => -12.0,
            'longitude' => -38.0,
        ]);
    }

    private function candidatura(Politico $politico, Cargo $cargo, Partido $partido, Eleicao $eleicao, int $votos): Candidatura
    {
        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10',
            'nome_urna' => $politico->nome_publico,
            'uf' => 'BA',
            'origem' => 'manual',
            'votos_total' => $votos,
        ]);
    }

    private function resultadoMunicipal(Candidatura $candidatura, Cidade $cidade, int $votos): ResultadoMunicipal
    {
        return ResultadoMunicipal::query()->create([
            'eleicao_id' => $candidatura->eleicao_id,
            'candidatura_id' => $candidatura->id,
            'cidade_id' => $cidade->id,
            'votos' => $votos,
        ]);
    }
}
