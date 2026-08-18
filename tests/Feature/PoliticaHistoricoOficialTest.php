<?php

namespace Tests\Feature;

use App\Livewire\Politica\V2\PoliticoShow;
use App\Models\Politica\Cidade;
use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Mandato;
use App\Models\Politica\V2\Partido;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\PoliticaHistoricoOficialService;
use App\Services\Politica\V2\TseOfficialSyncService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaHistoricoOficialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PoliticaV2Seeder::class);
    }

    #[Test]
    public function historico_especial_importa_quatro_acompanhados_independentemente_do_partido_e_exclui_os_demais(): void
    {
        $arquivo = $this->csv('historico-especial-2006.csv', [
            $this->header(),
            $this->row('BA', '6', 'DEPUTADO FEDERAL', '6001', '2210', 'MARCIO CARLOS MARINHO', 'MARCIO MARINHO', '22', 'PR'),
            $this->row('BA', '7', 'DEPUTADO ESTADUAL', '6002', '10123', 'JOSE DE ARIMATEIA CORIOLANO DE PAIVA', 'JOSE DE ARIMATEIA', '10', 'PRB'),
            $this->row('BA', '6', 'DEPUTADO FEDERAL', '6003', '1313', 'CANDIDATO QUALQUER', 'OUTRO', '13', 'PT'),
            $this->row('BR', '1', 'PRESIDENTE', '6004', '13', 'LUIZ INACIO LULA DA SILVA', 'LULA', '13', 'PT'),
        ]);

        $stats = app(TseOfficialSyncService::class)
            ->sincronizarCandidaturas(2006, 'BA', 'historico-especial', $arquivo);

        $this->assertSame(2, $stats['linhas_selecionadas']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '6001']);
        $this->assertDatabaseHas('politica_candidaturas', ['tse_sq_candidato' => '6002']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '6003']);
        $this->assertDatabaseMissing('politica_candidaturas', ['tse_sq_candidato' => '6004']);
    }



    #[Test]
    public function sigla_historica_compartilha_numero_sem_colidir_com_partido_atual(): void
    {
        $republicanos = Partido::query()->create([
            'numero' => 10,
            'sigla' => 'REPUBLICANOS',
            'nome' => 'Republicanos',
            'ativo' => true,
        ]);
        $prb = Partido::query()->create([
            'numero' => null,
            'sigla' => 'PRB',
            'nome' => 'Partido Republicano Brasileiro',
            'ativo' => true,
        ]);

        $arquivo = $this->csv('historico-prb-numero-10-2018.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [2018,1,'297','Eleições Gerais 2018','07/10/2018','BA','BA','BAHIA','7','DEPUTADO ESTADUAL','50000600001','10123','JURAILTON DE SOUSA SANTOS','JURAILTON SANTOS','12','APTO','10','PRB','PARTIDO REPUBLICANO BRASILEIRO','','','BA','01/01/1970'],
        ]);

        $stats = app(TseOfficialSyncService::class)
            ->sincronizarCandidaturas(2018, 'BA', 'historico-especial', $arquivo);

        $this->assertSame(1, $stats['linhas_selecionadas']);
        $this->assertDatabaseHas('politica_partidos', [
            'id' => $republicanos->id,
            'numero' => 10,
            'sigla' => 'REPUBLICANOS',
            'nome' => 'Republicanos',
        ]);
        $this->assertDatabaseHas('politica_partidos', [
            'id' => $prb->id,
            'numero' => null,
            'sigla' => 'PRB',
            'nome' => 'PARTIDO REPUBLICANO BRASILEIRO',
        ]);

        $candidatura = Candidatura::query()
            ->where('tse_sq_candidato', '50000600001')
            ->firstOrFail();
        $this->assertSame($prb->id, $candidatura->partido_id);
    }

    #[Test]
    public function sigla_historica_e_criada_sem_numero_quando_so_existe_o_partido_atual(): void
    {
        $republicanos = Partido::query()->create([
            'numero' => 10,
            'sigla' => 'REPUBLICANOS',
            'nome' => 'Republicanos',
            'ativo' => true,
        ]);

        $arquivo = $this->csv('historico-prb-alias-2016.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [2016,1,'2','Eleições Municipais 2016','02/10/2016','BA','38490','SALVADOR','13','VEREADOR','','10000','ROGERIA DE ALMEIDA PEREIRA DOS SANTOS','ROGERIA SANTOS','12','APTO','10','PRB','PARTIDO REPUBLICANO BRASILEIRO','','','BA','01/01/1970'],
        ]);

        app(TseOfficialSyncService::class)
            ->sincronizarCandidaturas(2016, 'BA', 'historico-especial', $arquivo);

        $prb = Partido::query()->where('sigla', 'PRB')->firstOrFail();
        $this->assertNull($prb->numero);
        $this->assertSame(10, (int) $republicanos->fresh()->numero);
        $this->assertSame('REPUBLICANOS', $republicanos->fresh()->sigla);

        $candidatura = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'rogeria-santos'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 2016))
            ->firstOrFail();
        $this->assertSame($prb->id, $candidatura->partido_id);
    }

    #[Test]
    public function layouts_historicos_sem_sq_candidato_usam_chave_composta_e_vinculam_resultados(): void
    {
        $candidaturas = $this->csv('historico-sem-sq-2010.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [2010, 1, '224', 'Eleições Gerais 2010', '03/10/2010', 'BA', 'BA', 'BAHIA', '6', 'DEPUTADO FEDERAL', '', '2210', 'MARCIO CARLOS MARINHO', 'MARCIO MARINHO', '12', 'APTO', '10', 'PRB', 'PRB', '', '', 'BA', '01/01/1970'],
        ]);

        $service = app(TseOfficialSyncService::class);
        $stats = $service->sincronizarCandidaturas(2010, 'BA', 'historico-especial', $candidaturas);

        $this->assertSame(1, $stats['linhas_selecionadas']);
        $this->assertSame(1, $stats['identificadores_historicos']);

        $candidatura = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'marcio-marinho'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 2010))
            ->firstOrFail();

        $this->assertNull($candidatura->tse_sq_candidato);
        $this->assertNotNull($candidatura->tse_chave_historica);
        $this->assertTrue($candidatura->isRegistroOficialTse());

        Cidade::query()->create([
            'nome' => 'SALVADOR',
            'ibge_code' => 2927408,
            'tse_codigo' => '38490',
            'latitude' => -12.97,
            'longitude' => -38.50,
        ]);

        $resultados = $this->csv('historico-resultado-sem-sq-2010.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','QT_VOTOS_NOMINAIS','DS_SIT_TOT_TURNO'],
            [2010, 1, '224', 'BA', '38490', 'SALVADOR', '1', '6', 'DEPUTADO FEDERAL', '', '2210', '321', 'SUPLENTE'],
        ]);

        $diagnostico = $service->diagnosticarResultados(2010, 'BA', 'historico-especial', $resultados);
        $this->assertSame(1, $diagnostico['linhas_selecionadas']);

        $service->sincronizarResultados(2010, 'BA', 'historico-especial', $resultados);
        $this->assertDatabaseHas('politica_resultados_municipais', [
            'candidatura_id' => $candidatura->id,
            'votos' => 321,
        ]);
    }


    #[Test]
    public function resultados_duplicados_no_zip_logico_sao_descartados_sem_dobrar_votos(): void
    {
        Cidade::query()->create([
            'nome' => 'SALVADOR',
            'ibge_code' => 2927408,
            'tse_codigo' => '38490',
            'latitude' => -12.97,
            'longitude' => -38.50,
        ]);

        $candidaturas = $this->csv('historico-duplicado-cand-2022.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [2022,1,'546','Eleições Gerais 2022','02/10/2022','BA','BA','BAHIA','6','DEPUTADO FEDERAL','50001619841','1000','ROGERIA DE ALMEIDA PEREIRA DOS SANTOS','ROGERIA SANTOS','12','APTO','10','REPUBLICANOS','REPUBLICANOS','','','BA','01/01/1970'],
        ]);

        $service = app(TseOfficialSyncService::class);
        $service->sincronizarCandidaturas(2022, 'BA', 'historico-especial', $candidaturas);

        $resultados = $this->csv('historico-duplicado-res-2022.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','QT_VOTOS_NOMINAIS','QT_VOTOS','DS_SIT_TOT_TURNO'],
            [2022,1,'546','BA','38490','SALVADOR','1','6','DEPUTADO FEDERAL','50001619841','1000','82012','','ELEITO POR QP'],
            [2022,1,'546','BA','38490','SALVADOR','1','6','DEPUTADO FEDERAL','50001619841','1000','82012','','ELEITO POR QP'],
        ]);

        $stats = $service->sincronizarResultados(2022, 'BA', 'historico-especial', $resultados);
        $rogeria = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'rogeria-santos'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 2022))
            ->firstOrFail();

        $this->assertSame(1, $stats['linhas_selecionadas']);
        $this->assertSame(1, $stats['linhas_duplicadas_descartadas']);
        $this->assertSame(82012, (int) $rogeria->fresh()->votos_total);
        $this->assertSame(82012, (int) $rogeria->resultadosMunicipais()->sum('votos'));
    }

    #[Test]
    public function resultado_historico_usa_qt_votos_quando_qt_votos_nominais_esta_vazio(): void
    {
        Cidade::query()->create([
            'nome' => 'FEIRA DE SANTANA',
            'ibge_code' => 2910800,
            'tse_codigo' => '35114',
            'latitude' => -12.26,
            'longitude' => -38.96,
        ]);

        $candidaturas = $this->csv('historico-1998-cand-votos.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [1998,1,'70','Eleições Gerais 1998','04/10/1998','BA','BA','BAHIA','7','DEPUTADO ESTADUAL','500000507151141','15114','JOSE DE ARIMATEIA CORIOLANO DE PAIVA','JOSE DE ARIMATEIA','12','DEFERIDO','15','PMDB','PMDB','','','BA','01/01/1950'],
        ]);

        $service = app(TseOfficialSyncService::class);
        $service->sincronizarCandidaturas(1998, 'BA', 'historico-especial', $candidaturas);

        $resultados = $this->csv('historico-1998-res-votos.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','QT_VOTOS_NOMINAIS','QT_VOTOS','DS_SIT_TOT_TURNO'],
            [1998,1,'70','BA','35114','FEIRA DE SANTANA','1','7','DEPUTADO ESTADUAL','500000507151141','15114','', '21929','ELEITO'],
        ]);

        $service->sincronizarResultados(1998, 'BA', 'historico-especial', $resultados);
        $arimateia = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'jose-de-arimateia'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 1998))
            ->firstOrFail();

        $this->assertSame(21929, (int) $arimateia->fresh()->votos_total);
    }

    #[Test]
    public function resultado_1998_usa_qt_votos_nominais_validos_quando_nominais_vem_zero(): void
    {
        Cidade::query()->create([
            'nome' => 'FEIRA DE SANTANA',
            'ibge_code' => 2910800,
            'tse_codigo' => '35114',
            'latitude' => -12.26,
            'longitude' => -38.96,
        ]);

        $candidaturas = $this->csv('historico-1998-cand-validos.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [1998,1,'70','Eleições Gerais 1998','04/10/1998','BA','BA','BAHIA','7','DEPUTADO ESTADUAL','500000507151141','15114','JOSE DE ARIMATEIA CORIOLANO DE PAIVA','JOSE DE ARIMATEIA','12','DEFERIDO','15','PMDB','PMDB','','','BA','01/01/1950'],
        ]);

        $service = app(TseOfficialSyncService::class);
        $service->sincronizarCandidaturas(1998, 'BA', 'historico-especial', $candidaturas);

        $resultados = $this->csv('historico-1998-res-validos.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','QT_VOTOS_NOMINAIS','QT_VOTOS_NOMINAIS_VALIDOS','QT_VOTOS','DS_SIT_TOT_TURNO'],
            [1998,1,'70','BA','35114','FEIRA DE SANTANA','1','7','DEPUTADO ESTADUAL','500000507151141','15114','0','21929','','ELEITO'],
        ]);

        $service->sincronizarResultados(1998, 'BA', 'historico-especial', $resultados);
        $arimateia = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'jose-de-arimateia'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 1998))
            ->firstOrFail();

        $this->assertSame(21929, (int) $arimateia->fresh()->votos_total);
        $this->assertSame(21929, (int) $arimateia->resultadosMunicipais()->sum('votos'));
        $this->assertSame(21929, (int) $arimateia->resultadosZonas()->sum('votos'));
    }

    #[Test]
    public function eleicao_municipal_antiga_nao_usa_sq_repetido_de_outro_municipio(): void
    {
        Cidade::query()->create([
            'nome' => 'FEIRA DE SANTANA',
            'ibge_code' => 2910800,
            'tse_codigo' => '35114',
            'latitude' => -12.26,
            'longitude' => -38.96,
        ]);
        Cidade::query()->create([
            'nome' => 'SALVADOR',
            'ibge_code' => 2927408,
            'tse_codigo' => '38490',
            'latitude' => -12.97,
            'longitude' => -38.50,
        ]);

        $candidaturas = $this->csv('historico-2004-cand-municipal.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'],
            [2004,1,'90','Eleições Municipais 2004','03/10/2004','BA','35114','FEIRA DE SANTANA','13','VEREADOR','250','17031','JOSE DE ARIMATEIA CORIOLANO DE PAIVA','JOSE DE ARIMATEIA','12','APTO','17','PSL','PSL','','','BA','01/01/1950'],
        ]);

        $service = app(TseOfficialSyncService::class);
        $service->sincronizarCandidaturas(2004, 'BA', 'historico-especial', $candidaturas);

        $resultados = $this->csv('historico-2004-res-municipal.csv', [
            ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','SG_UF','CD_MUNICIPIO','NM_MUNICIPIO','NR_ZONA','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','QT_VOTOS_NOMINAIS','QT_VOTOS','DS_SIT_TOT_TURNO'],
            [2004,1,'90','BA','35114','FEIRA DE SANTANA','1','13','VEREADOR','250','17031','3359','','ELEITO'],
            // Mesmo SQ/número em outro município: não pertence ao nosso José de Arimateia.
            [2004,1,'90','BA','38490','SALVADOR','1','13','VEREADOR','250','17031','47905','','NÃO ELEITO'],
        ]);

        $stats = $service->sincronizarResultados(2004, 'BA', 'historico-especial', $resultados);
        $arimateia = Candidatura::query()
            ->whereHas('politico', fn ($q) => $q->where('slug', 'jose-de-arimateia'))
            ->whereHas('eleicao', fn ($q) => $q->where('ano', 2004))
            ->firstOrFail();

        $this->assertSame(1, $stats['linhas_selecionadas']);
        $this->assertSame(3359, (int) $arimateia->fresh()->votos_total);
        $this->assertSame(1, $arimateia->resultadosMunicipais()->count());
        $this->assertSame('FEIRA DE SANTANA', $arimateia->resultadosMunicipais()->firstOrFail()->cidade->nome);
    }

    #[Test]
    public function jose_de_arimateia_remove_mandato_estadual_incorreto_de_2007_e_preserva_segundo_mandato_de_vereador(): void
    {
        $jose = Politico::query()->where('slug', 'jose-de-arimateia')->firstOrFail();
        $cargoDeputado = Cargo::query()->where('nome', 'Deputado Estadual')->firstOrFail();
        $prb = Partido::query()->firstOrCreate(
            ['sigla' => 'PRB'],
            ['numero' => null, 'nome' => 'Partido Republicano Brasileiro', 'ativo' => true]
        );

        Mandato::query()->create([
            'politico_id' => $jose->id,
            'cargo_id' => $cargoDeputado->id,
            'partido_id' => $prb->id,
            'tipo' => 'mandato',
            'esfera' => 'estadual',
            'uf' => 'BA',
            'ano_inicio' => 2007,
            'ano_fim' => 2011,
            'periodo_texto' => '2007–2011',
            'situacao' => 'concluido',
            'fonte' => 'ALBA',
            'fonte_id' => '915859',
            'fonte_oficial' => true,
        ]);

        $stats = app(PoliticaHistoricoOficialService::class)->sincronizarMandatosInstitucionais();

        $this->assertSame(1, $stats['removidos']);
        $this->assertDatabaseMissing('politica_mandatos', [
            'politico_id' => $jose->id,
            'cargo_id' => $cargoDeputado->id,
            'ano_inicio' => 2007,
            'ano_fim' => 2011,
            'fonte' => 'ALBA',
        ]);

        $cargoVereador = Cargo::query()->where('nome', 'Vereador')->firstOrFail();
        $this->assertDatabaseHas('politica_mandatos', [
            'politico_id' => $jose->id,
            'cargo_id' => $cargoVereador->id,
            'partido_id' => $prb->id,
            'ano_inicio' => 2009,
            'ano_fim' => 2011,
            'fonte_id' => 'SesEsp0212101',
        ]);
        $this->assertDatabaseHas('politica_mandatos', [
            'politico_id' => $jose->id,
            'cargo_id' => $cargoDeputado->id,
            'partido_id' => $prb->id,
            'ano_inicio' => 2011,
            'ano_fim' => 2015,
        ]);

        $republicanos = Partido::query()->where('sigla', 'REPUBLICANOS')->firstOrFail();
        $this->assertDatabaseHas('politica_mandatos', [
            'politico_id' => $jose->id,
            'cargo_id' => $cargoDeputado->id,
            'partido_id' => $republicanos->id,
            'ano_inicio' => 2019,
            'ano_fim' => 2023,
        ]);
    }

    #[Test]
    public function jose_de_arimateia_tem_filiacoes_partidarias_separadas_dos_mandatos(): void
    {
        $service = app(PoliticaHistoricoOficialService::class);
        $first = $service->sincronizarFiliacoesInstitucionais();
        $second = $service->sincronizarFiliacoesInstitucionais();

        $jose = Politico::query()->where('slug', 'jose-de-arimateia')->firstOrFail();

        $this->assertSame(1, $first['politicos']);
        $this->assertSame(5, $first['registros_configurados']);
        $this->assertSame(5, $first['inseridos']);
        $this->assertSame(0, $second['inseridos']);
        $this->assertSame(5, $second['atualizados']);
        $this->assertSame(5, $jose->filiacoes()->count());

        $psl = Partido::query()->where('sigla', 'PSL')->firstOrFail();
        $this->assertDatabaseHas('politica_filiacoes', [
            'politico_id' => $jose->id,
            'partido_id' => $psl->id,
            'ano_inicio' => 2004,
            'ano_fim' => 2007,
            'periodo_texto' => '2004–2007',
            'fonte_oficial' => 1,
        ]);

        $republicanos = Partido::query()->where('sigla', 'REPUBLICANOS')->firstOrFail();
        $this->assertDatabaseHas('politica_filiacoes', [
            'politico_id' => $jose->id,
            'partido_id' => $republicanos->id,
            'ano_inicio' => 2019,
            'ano_fim' => null,
            'periodo_texto' => '2019–atual',
        ]);
    }

    #[Test]
    public function perfil_de_jose_exibe_filiacao_partidaria_e_alerta_sobre_intervalo_nao_confirmado(): void
    {
        $service = app(PoliticaHistoricoOficialService::class);
        $service->sincronizarMandatosInstitucionais();
        $service->sincronizarFiliacoesInstitucionais();
        $jose = Politico::query()->where('slug', 'jose-de-arimateia')->firstOrFail();

        Livewire::test(PoliticoShow::class, ['politico' => $jose])
            ->assertSee('Filiação partidária')
            ->assertSee('PMDB')
            ->assertSee('PFL')
            ->assertSee('PSL')
            ->assertSee('PRB')
            ->assertSee('REPUBLICANOS')
            ->assertSee('2004–2007')
            ->assertSee('não foi gravada como fato oficial');
    }

    #[Test]
    public function mandatos_institucionais_sao_idempotentes_e_restritos_aos_quatro_politicos(): void
    {
        $service = app(PoliticaHistoricoOficialService::class);
        $first = $service->sincronizarMandatosInstitucionais();
        $count = Mandato::query()->count();
        $second = $service->sincronizarMandatosInstitucionais();

        $this->assertSame(4, $first['politicos']);
        $this->assertGreaterThan(0, $count);
        $this->assertSame($count, Mandato::query()->count());
        $this->assertSame(0, $second['inseridos']);
        $this->assertSame($count, $second['atualizados']);

        $slugsComMandato = Politico::query()
            ->whereHas('mandatos')
            ->pluck('slug')
            ->sort()
            ->values()
            ->all();

        $esperados = collect(config('politica.tse.historico_especial.slugs'))->sort()->values()->all();
        $this->assertSame($esperados, $slugsComMandato);
    }

    #[Test]
    public function perfil_de_politico_especial_exibe_linha_do_tempo_com_fonte_oficial(): void
    {
        app(PoliticaHistoricoOficialService::class)->sincronizarMandatosInstitucionais();
        $marcio = Politico::query()->where('slug', 'marcio-marinho')->firstOrFail();

        Livewire::test(PoliticoShow::class, ['politico' => $marcio])
            ->assertSee('Histórico oficial especial')
            ->assertSee('Eleições, mandatos e cargos públicos')
            ->assertSee('2003–2007')
            ->assertSee('Suplente; assumiu o mandato em outubro de 2008')
            ->assertSee('Assembleia Legislativa da Bahia')
            ->assertSee('Abrir fonte oficial');
    }

    private function csv(string $name, array $rows): string
    {
        $dir = storage_path('framework/testing/politica-historico');
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

    private function header(): array
    {
        return ['ANO_ELEICAO','NR_TURNO','CD_ELEICAO','DS_ELEICAO','DT_ELEICAO','SG_UF','SG_UE','NM_UE','CD_CARGO','DS_CARGO','SQ_CANDIDATO','NR_CANDIDATO','NM_CANDIDATO','NM_URNA_CANDIDATO','CD_SITUACAO_CANDIDATURA','DS_SITUACAO_CANDIDATURA','NR_PARTIDO','SG_PARTIDO','NM_PARTIDO','NM_COLIGACAO','SG_FEDERACAO','SG_UF_NASCIMENTO','DT_NASCIMENTO'];
    }

    private function row(string $uf, string $cdCargo, string $cargo, string $sq, string $numero, string $nome, string $urna, string $nrPartido, string $sigla): array
    {
        return [2006, 1, '144', 'Eleições Gerais 2006', '01/10/2006', $uf, $uf === 'BR' ? 'BR' : 'BA', $uf === 'BR' ? 'BRASIL' : 'BAHIA', $cdCargo, $cargo, $sq, $numero, $nome, $urna, '12', 'APTO', $nrPartido, $sigla, $sigla, '', '', 'BA', '01/01/1970'];
    }
}
