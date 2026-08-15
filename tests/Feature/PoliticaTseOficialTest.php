<?php

namespace Tests\Feature;

use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Models\Politica\V2\ResultadoMunicipal;
use App\Services\Politica\V2\TseOfficialSyncService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaTseOficialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);

        Cidade::query()->create([
            'nome' => 'Salvador',
            'ibge_code' => 2927408,
            'tse_codigo' => '38490',
            'latitude' => -12.97,
            'longitude' => -38.50,
        ]);
    }

    #[Test]
    public function importa_recorte_oficial_sem_duplicar_prioritario_e_filtra_legislativo(): void
    {
        $rogeriaPolitico = Politico::query()->where('slug', 'rogeria-santos')->firstOrFail();
        $eleicaoLegada = Eleicao::query()->create([
            'ano' => 2022,
            'turno' => 1,
            'tipo' => 'geral',
            'descricao' => 'Eleições Gerais 2022 - legado',
            'uf' => 'BA',
            'status' => 'historica',
        ]);
        $cargoLegado = Cargo::query()->where('nome', 'Deputado Federal')->firstOrFail();
        $partidoLegado = Partido::query()->create(['numero' => 10, 'sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos']);
        $legacy = Candidatura::query()->create([
            'politico_id' => $rogeriaPolitico->id,
            'eleicao_id' => $eleicaoLegada->id,
            'cargo_id' => $cargoLegado->id,
            'partido_id' => $partidoLegado->id,
            'origem' => 'legacy_v1',
            'legacy_candidato_id' => 4623,
            'origem_chave' => 'legacy_v1:4623:2022:deputado-federal',
            'votos_total' => 82012,
        ]);

        $arquivo = $this->csv('consulta-cand-2022.csv', [
            $this->candidateHeader(),
            $this->candidateRow('BA', '6', 'DEPUTADO FEDERAL', '1001', '1010', 'ROGERIA DE ALMEIDA PEREIRA DOS SANTOS', 'ROGERIA SANTOS', '10', 'REPUBLICANOS', 'Republicanos'),
            $this->candidateRow('BA', '6', 'DEPUTADO FEDERAL', '1002', '1313', 'CANDIDATO OUTRO PARTIDO', 'OUTRO', '13', 'PT', 'Partido dos Trabalhadores'),
            $this->candidateRow('SP', '6', 'DEPUTADO FEDERAL', '1003', '1011', 'REPUBLICANOS DE OUTRA UF', 'OUTRA UF', '10', 'REPUBLICANOS', 'Republicanos'),
            $this->candidateRow('BA', '3', 'GOVERNADOR', '2001', '13', 'GOVERNADOR UM', 'GOVERNADOR UM', '13', 'PT', 'Partido dos Trabalhadores'),
            $this->candidateRow('BA', '3', 'GOVERNADOR', '2002', '44', 'GOVERNADOR DOIS', 'GOVERNADOR DOIS', '44', 'UNIAO', 'União Brasil'),
            $this->candidateRow('BR', '1', 'PRESIDENTE', '3001', '13', 'LUIZ INACIO LULA DA SILVA', 'LULA', '13', 'PT', 'Partido dos Trabalhadores'),
        ]);

        $service = app(TseOfficialSyncService::class);
        $stats = $service->sincronizarCandidaturas(2022, 'BA', 'espelho', $arquivo);

        $this->assertSame(4, $stats['linhas_selecionadas']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '1002']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '1003']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '1001', 'origem' => 'tse_dados_abertos']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '2001']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '2002']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '3001']);

        $rogeria = Politico::query()->where('slug', 'rogeria-santos')->firstOrFail();
        $oficial = Candidatura::query()->where('politico_id', $rogeria->id)->where('tse_sq_candidato', '1001')->firstOrFail();
        $this->assertSame($legacy->id, $oficial->id);
        $this->assertSame('legacy_v1:4623:2022:deputado-federal', $oficial->origem_chave);
        $this->assertSame('tse_dados_abertos', $oficial->origem);
        $this->assertSame(1, Candidatura::query()->where('politico_id', $rogeria->id)->where('tse_sq_candidato', '1001')->count());
    }

    #[Test]
    public function agrega_resultado_oficial_por_municipio_e_zona_e_preserva_total_presidencial_nacional(): void
    {
        $candidatos = $this->csv('consulta-cand-resultados.csv', [
            $this->candidateHeader(),
            $this->candidateRow('BA', '6', 'DEPUTADO FEDERAL', '1001', '1010', 'ROGERIA DE ALMEIDA PEREIRA DOS SANTOS', 'ROGERIA SANTOS', '10', 'REPUBLICANOS', 'Republicanos'),
            $this->candidateRow('BR', '1', 'PRESIDENTE', '3001', '13', 'LUIZ INACIO LULA DA SILVA', 'LULA', '13', 'PT', 'Partido dos Trabalhadores'),
        ]);

        $service = app(TseOfficialSyncService::class);
        $service->sincronizarCandidaturas(2022, 'BA', 'espelho', $candidatos);

        $resultados = $this->csv('votacao-2022.csv', [
            $this->resultHeader(),
            $this->resultRow('BA', 'DEPUTADO FEDERAL', '1001', '38490', 'SALVADOR', '1', '100', 'ELEITO POR QP'),
            $this->resultRow('BA', 'DEPUTADO FEDERAL', '1001', '38490', 'SALVADOR', '2', '50', 'ELEITO POR QP'),
            $this->resultRow('BA', 'PRESIDENTE', '3001', '38490', 'SALVADOR', '1', '1000', 'ELEITO'),
            $this->resultRow('SP', 'PRESIDENTE', '3001', '71072', 'SAO PAULO', '1', '2000', 'ELEITO'),
        ]);

        $stats = $service->sincronizarResultados(2022, 'BA', 'espelho', $resultados);

        $rogeria = Candidatura::query()->where('tse_sq_candidato', '1001')->firstOrFail();
        $lula = Candidatura::query()->where('tse_sq_candidato', '3001')->firstOrFail();

        $this->assertSame(150, (int) $rogeria->fresh()->votos_total);
        $this->assertSame(3000, (int) $lula->fresh()->votos_total);
        $this->assertSame(150, (int) ResultadoMunicipal::query()->where('candidatura_id', $rogeria->id)->sum('votos'));
        $this->assertSame(1000, (int) ResultadoMunicipal::query()->where('candidatura_id', $lula->id)->sum('votos'));
        $this->assertSame(2, DB::table('politica_resultados_zonas')->where('candidatura_id', $rogeria->id)->count());
        $this->assertTrue((bool) $rogeria->fresh()->eleito);
        $this->assertGreaterThanOrEqual(3, $stats['resultados_zonas']);
    }

    #[Test]
    public function em_2024_importa_apenas_vereador_e_prefeito_republicanos(): void
    {
        $arquivo = $this->csv('consulta-cand-2024.csv', [
            $this->candidateHeader(2024, '619', '06/10/2024', 'SALVADOR', '38490'),
            $this->candidateRow('BA', '13', 'VEREADOR', '4001', '10123', 'VEREADOR REP', 'VEREADOR REP', '10', 'REPUBLICANOS', 'Republicanos', 2024, '619', '06/10/2024', 'SALVADOR', '38490'),
            $this->candidateRow('BA', '13', 'VEREADOR', '4002', '13123', 'VEREADOR PT', 'VEREADOR PT', '13', 'PT', 'Partido dos Trabalhadores', 2024, '619', '06/10/2024', 'SALVADOR', '38490'),
            $this->candidateRow('BA', '11', 'PREFEITO', '5001', '10', 'PREFEITO REP', 'PREFEITO REP', '10', 'REPUBLICANOS', 'Republicanos', 2024, '619', '06/10/2024', 'SALVADOR', '38490'),
            $this->candidateRow('BA', '11', 'PREFEITO', '5002', '13', 'PREFEITO PT', 'PREFEITO PT', '13', 'PT', 'Partido dos Trabalhadores', 2024, '619', '06/10/2024', 'SALVADOR', '38490'),
        ]);

        app(TseOfficialSyncService::class)->sincronizarCandidaturas(2024, 'BA', 'espelho', $arquivo);

        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '4001']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '4002']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '5001']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '5002']);
        $this->assertSame(2, Candidatura::query()->whereHas('eleicao', fn ($q) => $q->where('ano', 2024))->count());
    }

    private function csv(string $name, array $rows): string
    {
        $dir = storage_path('framework/testing/politica-tse');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir.'/'.$name;
        $handle = fopen($path, 'wb');
        foreach ($rows as $row) {
            fputcsv($handle, $row, ';', '"', '\\');
        }
        fclose($handle);
        return $path;
    }

    private function candidateHeader(int $ano = 2022, string $codigoEleicao = '544', string $data = '02/10/2022', string $ue = 'BAHIA', string $sgUe = 'BA'): array
    {
        return ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'];
    }

    private function candidateRow(string $uf, string $cdCargo, string $cargo, string $sq, string $numero, string $nome, string $urna, string $nrPartido, string $sigla, string $partido, int $ano = 2022, string $codigoEleicao = '544', string $data = '02/10/2022', string $ue = 'BAHIA', string $sgUe = 'BA'): array
    {
        return [$ano,1,$codigoEleicao,"Eleições Gerais {$ano}",$data,$uf,$sgUe,$ue,$cdCargo,$cargo,$sq,$numero,$nome,$urna,'12','APTO',$nrPartido,$sigla,$partido,'','', 'BA','01/01/1970'];
    }

    private function resultHeader(): array
    {
        return ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','QT_VOTOS_NOMINAIS','DS_SIT_TOT_TURNO'];
    }

    private function resultRow(string $uf, string $cargo, string $sq, string $codigoMunicipio, string $municipio, string $zona, string $votos, string $situacao): array
    {
        $cdCargo = $cargo === 'PRESIDENTE' ? '1' : '6';
        return [2022,1,'544','Eleições Gerais 2022','02/10/2022',$uf,$codigoMunicipio,$municipio,$zona,$cdCargo,$cargo,$sq,$votos,$situacao];
    }
}
