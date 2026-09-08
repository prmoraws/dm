<?php

use App\Http\Controllers\{
    FormaturaPdfController,
    DadosPdfController,
    CredencialPdfController,
    EventoPdfController,
    TrabalhoPdfController,
    CursoPdfController,
    GeralPdfController,
    CopPdfController,
    AnexosPdfController,
    ListaPdfController
};
use App\Livewire\Evento\Cestas;
use App\Livewire\Evento\Dashboard as EventoDashboard;
use App\Livewire\Universal\Dashboard as UniversalDashboard;
use App\Livewire\Unp\Dashboard as UnpDashboard;
use App\Livewire\Evento\{Entregas, Instituicoes, Terreiros};
use App\Livewire\Universal\{Banners, Blocos, Categorias, Pastores, PastorUnp, CarroUnp, Pessoas, Regiaos, Igrejas, GestaoCaptacoes};
use App\Http\Controllers\Universal\{PastorUnpPrintController, CadastroTdaPdfController, CadastroTdaTermoPdfController};
use App\Livewire\Unp\{Cargos, Cursos, Formaturas, Grupos, Instrutores, Presidios, Documentos, DashboardBatismo, CaptacaoCursoUnpWizard, TurmasCursoUnp, GestaoCaptacoesCursoUnp, AcompanhamentoCursoUnp, CursoUnpDashboard};
use App\Livewire\Unp\Oficios\{Anexos, Convidados, DadosCurso, InformacaoCurso, ListaCertificado, OficioCredencial, OficioEvento, OficioFormatura, OficioGeral, OficioTrabalho, OficioCop, OficioCurso, Reeducandos};
use App\Livewire\Universal\{CaptacaoUnp, Credenciados, CaptacaoCredenciadoWizard, GestaoCaptacaoCredenciados, CaptacaoSucesso, EdicaoCarroPublica, CaptacaoTdaWizard, GestaoCaptacoesTda, CadastrosTda, TdaDashboard};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Livewire\Adm\Users as UserManagement;
use App\Livewire\Adm\Teams as TeamManagement;
use App\Livewire\Adm\Dashboard as AdmDashboard;
use App\Livewire\Adm\Captacoes as CaptacaoManagement;
use App\Livewire\Universal\CaptacaoPessoaWizard;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Politica\CityDashboard;
use App\Livewire\Politica\CityView;
use App\Livewire\Politica\CandidatesManager;
use App\Livewire\Politica\EspelhoManager;
use App\Livewire\Politica\V2\Dashboard as PoliticaDashboardV2;
use App\Livewire\Politica\V2\AcompanhamentoPrioritario;
use App\Livewire\Politica\V2\PoliticoShow;
use App\Livewire\Politica\V2\EspelhoInteligente;
use App\Livewire\Politica\V2\EspelhoOperacionalEdit;
use App\Livewire\Politica\V2\MapaInterativo;
use App\Livewire\Politica\V2\DadosOficiais;
use App\Livewire\Politica\V2\Eleicoes2026;
use App\Livewire\Politica\V2\HistoricoAcompanhados;
use App\Livewire\Politica\V2\ComparativoTerritorial;
use App\Livewire\Politica\V2\InteligenciaTerritorial;
use App\Livewire\Politica\V2\PainelExecutivo;
use App\Http\Controllers\Politica\PainelExecutivoExportController;
use App\Http\Controllers\Politica\EspelhoCidadeExportController;
use App\Livewire\Politica\V2\QualidadeDados;
use App\Http\Controllers\Politica\PoliticaQualidadeDadosExportController;
use App\Http\Controllers\Universal\PessoaPrintController;
use Illuminate\Support\Facades\Artisan;
use App\Livewire\Unp\FormularioBatismo;




Route::get('/', function () {
    return view('welcome');
});


Route::get('/captacao-unp', CaptacaoUnp::class)->name('captacao.unp');
Route::get('/cadastro-pessoas', CaptacaoPessoaWizard::class)->name('captacao.pessoa.create');
Route::get('/captacao/credenciado', CaptacaoCredenciadoWizard::class)->name('captacao.credenciado');
Route::get('/captacao/sucesso', CaptacaoSucesso::class)->name('captacao.sucesso');
Route::get('/carro/editar-veiculo', EdicaoCarroPublica::class)->name('carro.public.edit');
Route::get('/batismo', FormularioBatismo::class)->name('batismo.publico');

Route::get('/cadastro-tda', CaptacaoTdaWizard::class)->name('captacao.tda.create');
Route::get('/curso-unp/inscricao', CaptacaoCursoUnpWizard::class)->name('curso-unp.inscricao');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Rota padrão do dashboard do Jetstream
    Route::get('/dashboard', function () {
        if (strtolower((string) auth()->user()?->currentTeam?->name) === 'tda') {
            return redirect()->route('tda.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'unp', 'middleware' => 'team.access:Unp'], function () {
        Route::get('/dashboard', UnpDashboard::class)->name('dashboard.unp');
        Route::get('/cargos', Cargos::class)->name('cargos');
        Route::get('/grupos', Grupos::class)->name('grupos');
        Route::get('/formaturas', Formaturas::class)->name('formaturas');
        Route::get('/dashboard-batismo', DashboardBatismo::class)->name('batismo.dashboard');
        Route::get('/instrutores', Instrutores::class)->name('instrutores');
        Route::get('/presidios', Presidios::class)->name('presidios');
        Route::get('/cursos', Cursos::class)->name('cursos');
        Route::get('/curso-unp/dashboard', CursoUnpDashboard::class)->name('curso-unp.dashboard');
        Route::get('/curso-unp/turmas', TurmasCursoUnp::class)->name('curso-unp.turmas');
        Route::get('/curso-unp/captacoes', GestaoCaptacoesCursoUnp::class)->name('curso-unp.captacoes');
        Route::get('/curso-unp/acompanhamento', AcompanhamentoCursoUnp::class)->name('curso-unp.acompanhamento');
        Route::get('/documentos', Documentos::class)->name('documentos');
        // Subgrupo de Ofícios
        Route::group(['prefix' => 'oficios'], function () {
            Route::get('/oficio-formaturas', OficioFormatura::class)->name('oficio-formaturas');
            Route::get('/convidados', Convidados::class)->name('oficios.convidados');
            Route::get('/anexos', Anexos::class)->name('oficios.anexos');
            Route::get('/credenciais', OficioCredencial::class)->name('oficios.credenciais');
            Route::get('/eventos', OficioEvento::class)->name('oficios.eventos');
            Route::get('/trabalho', OficioTrabalho::class)->name('oficios.trabalho');
            Route::get('/cop', OficioCop::class)->name('oficios.cop');
            Route::get('/cursos', OficioCurso::class)->name('oficios.cursos');
            Route::get('/geral', OficioGeral::class)->name('oficios.geral');
            Route::get('/informacao-cursos', InformacaoCurso::class)->name('oficios.informacao-cursos');
            Route::get('/dados-cursos', DadosCurso::class)->name('oficios.dados-cursos');
            Route::get('/reeducandos', Reeducandos::class)->name('oficios.reeducandos');
            Route::get('/lista-certificados', ListaCertificado::class)->name('oficios.lista-certificados');
        });
    });

    // Rotas do Grupo Universal
    Route::group(['prefix' => 'universal', 'middleware' => 'team.access:Universal'], function () {
        Route::get('/blocos', Blocos::class)->name('blocos');
        Route::get('/regiaos', Regiaos::class)->name('regiaos');
        Route::get('/igrejas', Igrejas::class)->name('igrejas');
        Route::get('/categorias', Categorias::class)->name('categorias');
        Route::get('/pastores', Pastores::class)->name('pastores');
        Route::get('/pastor-unp', PastorUnp::class)->name('pastor-unp');
        Route::get('/carros-unp', CarroUnp::class)->name('carros-unp');
        Route::get('/pastor-unp/{id}/print', [PastorUnpPrintController::class, 'show'])->name('pastor-unp.print');
        Route::get('/pessoas', Pessoas::class)->name('universal.pessoas');
        Route::get('/pessoas/{pessoa}/print', [PessoaPrintController::class, 'showFichaVoluntario'])->name('universal.pessoas.print.ficha');
        Route::get('/banners', Banners::class)->name('banners');
        Route::get('/dashboard', UniversalDashboard::class)->name('dashboard.uni');
        Route::get('/universal/credenciados', Credenciados::class)->name('universal.credenciados');
        // ... adicione todas as outras rotas 'universal' aqui
    });

    // Rotas exclusivas do time Terapia do Amor. O time Adm continua liberado
    // automaticamente pelo middleware CheckTeamAccess.
    Route::group(['prefix' => 'tda', 'middleware' => 'team.access:TDA'], function () {
        Route::get('/dashboard', TdaDashboard::class)->name('tda.dashboard');
        Route::get('/cadastros-aprovados', CadastrosTda::class)->name('universal.cadastros-tda');
        Route::get('/cadastros-aprovados/{cadastroTda}/pdf', [CadastroTdaPdfController::class, 'visualizar'])
            ->name('universal.cadastros-tda.pdf.visualizar');
        Route::get('/cadastros-aprovados/{cadastroTda}/pdf/baixar', [CadastroTdaPdfController::class, 'baixar'])
            ->name('universal.cadastros-tda.pdf.baixar');
        Route::get('/cadastros-aprovados/{cadastroTda}/termos', [CadastroTdaTermoPdfController::class, 'visualizarTodos'])
            ->name('universal.cadastros-tda.termos.visualizar-todos');
        Route::get('/cadastros-aprovados/{cadastroTda}/termos/baixar', [CadastroTdaTermoPdfController::class, 'baixarTodos'])
            ->name('universal.cadastros-tda.termos.baixar-todos');
        Route::get('/cadastros-aprovados/{cadastroTda}/termos/{tipo}', [CadastroTdaTermoPdfController::class, 'visualizar'])
            ->name('universal.cadastros-tda.termos.visualizar');
        Route::get('/cadastros-aprovados/{cadastroTda}/termos/{tipo}/baixar', [CadastroTdaTermoPdfController::class, 'baixar'])
            ->name('universal.cadastros-tda.termos.baixar');
        Route::get('/gestao-captacoes', GestaoCaptacoesTda::class)
            ->name('secretaria.gestao-captacoes-tda');
    });

    // Rotas do Grupo Eventos
    Route::group(['prefix' => 'evento', 'middleware' => 'team.access:Eventos'], function () {
        Route::get('/dashboard', EventoDashboard::class)->name('dashboard.ev');
        Route::get('/terreiros', Terreiros::class)->name('terreiros');
        Route::get('/instituicoes', Instituicoes::class)->name('instituicoes');
        Route::get('/cestas', Cestas::class)->name('cestas');
        Route::get('/entregas', Entregas::class)->name('entregas');
        // ... adicione todas as outras rotas 'evento' aqui
    });

    Route::middleware(['team.access:Adm'])->prefix('adm')->group(function () {
        Route::get('/users', UserManagement::class)->name('adm.users');
        Route::get('/teams', TeamManagement::class)->name('adm.teams');
        Route::get('/dashboard', AdmDashboard::class)->name('adm.dashboard');
        Route::get('/captacoes', CaptacaoManagement::class)->name('adm.captacoes');
    });

    // Rotas de iOS vizualização.
    Route::get('/oficios/{id}/pdf-view', [FormaturaPdfController::class, 'showPdfView'])->name('oficios.pdf.view');
    Route::get('/dados-cursos/{id}/pdf-view', [DadosPdfController::class, 'showPdfView'])->name('dados.pdf.view');
    Route::get('/oficios-credencial/{id}/pdf-view', [CredencialPdfController::class, 'showPdfView'])->name('credencial.pdf.view');
    Route::get('/oficios-evento/{id}/pdf-view', [EventoPdfController::class, 'showPdfView'])->name('evento.pdf.view');
    Route::get('/oficios-trabalho/{id}/pdf-view', [TrabalhoPdfController::class, 'showPdfView'])->name('trabalho.pdf.view');
    Route::get('/oficios-curso/{id}/pdf-view', [CursoPdfController::class, 'showPdfView'])->name('curso.pdf.view');
    Route::get('/oficios-geral/{id}/pdf-view', [GeralPdfController::class, 'showPdfView'])->name('geral.pdf.view');
    Route::get('/oficios-cop/{id}/pdf-view', [CopPdfController::class, 'showPdfView'])->name('cop.pdf.view');
    Route::get('/anexos/{id}/pdf-view', [AnexosPdfController::class, 'showPdfView'])->name('anexos.pdf.view');
    Route::get('/lista-certificados/{id}/pdf-view', [ListaPdfController::class, 'showPdfView'])->name('lista.pdf.view');

    // Rotas do Grupo Política
    Route::group(['prefix' => 'politica', 'middleware' => 'team.access:Politica'], function () {
        Route::get('/dashboard', PoliticaDashboardV2::class)->name('politica.dashboard');
        Route::get('/acompanhamento', AcompanhamentoPrioritario::class)->name('politica.acompanhamento');
        Route::get('/politicos/{politico}', PoliticoShow::class)->name('politica.politicos.show');
        Route::get('/cidades', CityDashboard::class)->name('politica.cidades');
        Route::get('/espelho/{cidade}', EspelhoInteligente::class)->name('politica.espelho.inteligente');
        Route::get('/espelho/{cidade}/relatorio/pdf', [EspelhoCidadeExportController::class, 'pdf'])->name('politica.espelho.relatorio.pdf');
        Route::get('/espelho/{cidade}/relatorio/excel', [EspelhoCidadeExportController::class, 'excel'])->name('politica.espelho.relatorio.excel');

        Route::get('/espelho/{cidade}/editar', EspelhoOperacionalEdit::class)->name('politica.espelho.edit');
        Route::get('/mapa', MapaInterativo::class)->name('politica.mapa');
        Route::get('/dados-oficiais', DadosOficiais::class)->name('politica.dados-oficiais');
        Route::get('/eleicoes-2026', Eleicoes2026::class)->name('politica.eleicoes-2026');
        Route::get('/historico-acompanhados', HistoricoAcompanhados::class)->name('politica.historico-acompanhados');
        Route::get('/comparativo-territorial', ComparativoTerritorial::class)->name('politica.comparativo-territorial');
        Route::get('/inteligencia-territorial', InteligenciaTerritorial::class)->name('politica.inteligencia-territorial');
        Route::get('/painel-executivo', PainelExecutivo::class)->name('politica.painel-executivo');
        Route::get('/painel-executivo/exportar/pdf', [PainelExecutivoExportController::class, 'pdf'])->name('politica.painel-executivo.pdf');
        Route::get('/painel-executivo/exportar/excel', [PainelExecutivoExportController::class, 'excel'])->name('politica.painel-executivo.excel');
        Route::get('/qualidade-dados', QualidadeDados::class)->name('politica.qualidade-dados');
        Route::get('/qualidade-dados/exportar/pdf', [PoliticaQualidadeDadosExportController::class, 'pdf'])->name('politica.qualidade-dados.pdf');
        Route::get('/qualidade-dados/exportar/excel', [PoliticaQualidadeDadosExportController::class, 'excel'])->name('politica.qualidade-dados.excel');

        // Rotas legadas preservadas apenas para auditoria/transição.
        Route::get('/legado/cidade/{cidade}', CityView::class)->name('politica.legado.cidade.view');
        Route::get('/legado/candidatos', CandidatesManager::class)->name('politica.legado.candidatos');
        Route::get('/legado/cidade/{cidade}/edit', EspelhoManager::class)->name('politica.legado.espelho.edit');
    });

    // Rotas do Grupo Secretária
    Route::group(['prefix' => 'secretaria', 'middleware' => 'team.access:Secretaria'], function () {
        Route::get('/gestao-captacoes', GestaoCaptacoes::class)->name('secretaria.gestao-captacoes');
        Route::get('/pessoas', Pessoas::class)->name('secretaria.pessoas');
        Route::get('/credenciados', Credenciados::class)->name('secretaria.credenciados');
        Route::get('/pessoas/{pessoa}/print', [PessoaPrintController::class, 'showFichaVoluntario'])->name('secretaria.pessoas.print.ficha');
        Route::get('/secretaria/gestao-credenciados', GestaoCaptacaoCredenciados::class)->name('secretaria.gestao-credenciados');
        // Você pode adicionar outras rotas da secretaria aqui no futuro
    });



});
