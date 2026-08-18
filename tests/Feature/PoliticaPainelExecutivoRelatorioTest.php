<?php

namespace Tests\Feature;

use App\Exports\Politica\PainelExecutivoExport;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\PainelExecutivoRelatorioService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaPainelExecutivoRelatorioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function relatorio_preserva_recorte_e_nao_cria_delta_quando_so_existe_um_pleito(): void
    {
        $rogeria = Politico::query()->where('slug', 'rogeria-santos')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Deputado Federal')->firstOrFail();
        $partido = $this->partido();
        $cidade = $this->cidade('Cidade Unica', 400001);
        $candidatura = $this->candidatura($rogeria, $cargo, $partido, 2022, 82012);
        $this->resultado($candidatura, $cidade, 1000);

        $dados = app(PainelExecutivoRelatorioService::class)->gerar($rogeria->slug);
        $linha = $dados['acompanhados_linhas'][0];

        $this->assertSame('Rogéria Santos', $dados['recorte_label']);
        $this->assertSame('Aguardando', $linha[1]);
        $this->assertNull($linha[3]);
        $this->assertSame(2022, $linha[4]);
        $this->assertNull($linha[5]);
        $this->assertSame(82012, $linha[6]);
        $this->assertNull($linha[7]);
    }

    #[Test]
    public function excel_tem_abas_executivas_e_metodologia_sem_nova_fonte_de_dados(): void
    {
        $dados = app(PainelExecutivoRelatorioService::class)->gerar('todos');
        $abas = collect((new PainelExecutivoExport($dados))->sheets());

        $this->assertSame([
            'Resumo',
            'Acompanhados',
            'Variações negativas',
            'Sinais positivos',
            'Pendências espelho',
            'Metodologia',
        ], $abas->map(fn ($sheet) => $sheet->title())->all());

        $this->assertSame(['Indicador', 'Valor'], $abas[0]->headings());
        $this->assertSame(['Critério', 'Descrição'], $abas[5]->headings());
        $this->assertStringContainsString('ausência nunca é convertida em zero', mb_strtolower($dados['metodologia_linhas'][1][1]));
    }

    #[Test]
    public function pdf_executivo_renderiza_corpo_e_metodologia_sem_diretiva_blade_vazada(): void
    {
        $dados = app(PainelExecutivoRelatorioService::class)->gerar('todos');
        $html = view('politica.v2.pdf.painel-executivo', $dados)->render();

        $this->assertStringContainsString('Painel Executivo de Prioridades', $html);
        $this->assertStringContainsString('Metodologia e rastreabilidade', $html);
        $this->assertStringContainsString('Todos os 4 históricos', $html);
        $this->assertStringNotContainsString('@endif', $html);
        $this->assertStringNotContainsString('@if', $html);
    }

    private function partido(): Partido
    {
        return Partido::query()->firstOrCreate(
            ['sigla' => 'REPUBLICANOS'],
            ['numero' => 10, 'nome' => 'Republicanos', 'ativo' => true]
        );
    }

    private function candidatura(Politico $politico, Cargo $cargo, Partido $partido, int $ano, int $votos): Candidatura
    {
        $eleicao = Eleicao::query()->create([
            'ano' => $ano,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => (string) $ano,
            'status' => 'concluida',
        ]);

        return Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'partido_id' => $partido->id,
            'numero_urna' => '10',
            'nome_urna' => $politico->nome_publico,
            'uf' => 'BA',
            'votos_total' => $votos,
            'situacao_eleicao' => 'ELEITO',
        ]);
    }

    private function cidade(string $nome, int $ibge): Cidade
    {
        return Cidade::query()->create([
            'nome' => $nome,
            'ibge_code' => $ibge,
            'latitude' => -12.5,
            'longitude' => -38.5,
        ]);
    }

    private function resultado(Candidatura $candidatura, Cidade $cidade, int $votos): void
    {
        ResultadoMunicipal::query()->create([
            'eleicao_id' => $candidatura->eleicao_id,
            'candidatura_id' => $candidatura->id,
            'cidade_id' => $cidade->id,
            'votos' => $votos,
        ]);
    }
}
