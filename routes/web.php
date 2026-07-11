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
use App\Http\Controllers\Universal\PastorUnpPrintController;
use App\Livewire\Unp\{Cargos, Cursos, Formaturas, Grupos, Instrutores, Presidios, Documentos, DashboardBatismo};
use App\Livewire\Unp\Oficios\{Anexos, Convidados, DadosCurso, InformacaoCurso, ListaCertificado, OficioCredencial, OficioEvento, OficioFormatura, OficioGeral, OficioTrabalho, OficioCop, OficioCurso, Reeducandos};
use App\Livewire\Universal\{CaptacaoUnp, Credenciados, CaptacaoCredenciadoWizard, GestaoCaptacaoCredenciados, CaptacaoSucesso, EdicaoCarroPublica};
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
use App\Http\Controllers\Universal\PessoaPrintController;
use Illuminate\Support\Facades\Artisan;
use App\Livewire\Unp\FormularioBatismo;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/limpar-tudo', function() {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "Cache limpo com sucesso!";
});


Route::get('/captacao-unp', CaptacaoUnp::class)->name('captacao.unp');
Route::get('/cadastro-pessoas', CaptacaoPessoaWizard::class)->name('captacao.pessoa.create');
Route::get('/captacao/credenciado', CaptacaoCredenciadoWizard::class)->name('captacao.credenciado');
Route::get('/captacao/sucesso', CaptacaoSucesso::class)->name('captacao.sucesso');
Route::get('/carro/editar-veiculo', EdicaoCarroPublica::class)->name('carro.public.edit');
Route::get('/batismo', FormularioBatismo::class)->name('batismo.publico');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Rota padrão do dashboard do Jetstream
    Route::get('/dashboard', function () {
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
        Route::get('/dashboard', CityDashboard::class)->name('politica.dashboard');
        Route::get('/cidade/{cidade}', CityView::class)->name('politica.cidade.view');
        Route::get('/candidatos', CandidatesManager::class)->name('politica.candidatos');
        Route::get('/cidade/{cidade}/edit', EspelhoManager::class)->name('politica.espelho.edit');
        Route::get('/mapa', \App\Livewire\Politica\InteractiveMap::class)->name('politica.mapa');
    });

    // Rotas do Grupo Secretária
    Route::group(['prefix' => 'secretaria', 'middleware' => 'team.access:Secretaria'], function () {
        Route::get('/gestao-captacoes', GestaoCaptacoes::class)->name('secretaria.gestao-captacoes');
        Route::get('/pessoas', Pessoas::class)->name('secretaria.pessoas');
        Route::get('/pessoas/{pessoa}/print', [PessoaPrintController::class, 'showFichaVoluntario'])->name('secretaria.pessoas.print.ficha');
        Route::get('/secretaria/gestao-credenciados', GestaoCaptacaoCredenciados::class)->name('secretaria.gestao-credenciados');
        // Você pode adicionar outras rotas da secretaria aqui no futuro
    });



});
