<?php

namespace Tests\Feature;

use App\Models\Politica\V2\Candidatura;
use App\Models\Politica\V2\Cargo;
use App\Models\Politica\V2\Eleicao;
use App\Models\Politica\V2\Politico;
use App\Services\Politica\V2\PoliticaFotosOficiaisService;
use Database\Seeders\Politica\PoliticaV2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use ZipArchive;

class PoliticaFotosOficiaisTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function foto_oficial_e_vinculada_por_sq_candidato_e_gravada_localmente(): void
    {
        $this->seed(PoliticaV2Seeder::class);

        $politico = Politico::query()->where('slug', 'lula')->firstOrFail();
        $cargo = Cargo::query()->where('nome', 'Presidente')->firstOrFail();
        $eleicao = Eleicao::query()->create([
            'ano' => 2026, 'turno' => 1, 'tipo' => 'geral', 'descricao' => 'Eleições 2026', 'uf' => 'BR',
        ]);
        $candidatura = Candidatura::query()->create([
            'politico_id' => $politico->id,
            'eleicao_id' => $eleicao->id,
            'cargo_id' => $cargo->id,
            'tse_sq_candidato' => '280002542548',
            'numero_urna' => '13',
            'nome_urna' => 'LULA',
            'uf' => 'BR',
            'origem' => 'tse_dados_abertos',
        ]);

        $zipPath = storage_path('framework/testing/fotos-oficiais.zip');
        @mkdir(dirname($zipPath), 0775, true);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('F280002542548_div.jpg', "\xFF\xD8\xFF".str_repeat('x', 512));
        $zip->close();

        $foto = public_path('images/politica/oficiais/lula.jpg');
        @unlink($foto);

        $resultado = app(PoliticaFotosOficiaisService::class)->importarArquivo($zipPath, 2026, 'BR');

        $this->assertSame(1, $resultado['atualizadas']);
        $this->assertFileExists($foto);
        $politico->refresh();
        $this->assertStringStartsWith('/images/politica/oficiais/lula.jpg?v=', $politico->foto_url);
        $this->assertSame($politico->foto_url, $candidatura->fresh()->foto_url);
        $this->assertSame('280002542548', $politico->links['foto_oficial']['sq_candidato']);
        $this->assertSame('TSE - Dados Abertos', $politico->links['foto_oficial']['fonte']);
        $this->assertSame(hash('sha256', "\xFF\xD8\xFF".str_repeat('x', 512)), $politico->links['foto_oficial']['sha256']);
        $this->assertSame('lula', $resultado['itens'][0]['slug']);
        $this->assertSame('atualizada', $resultado['itens'][0]['status']);

        @unlink($foto);
        @unlink($zipPath);
    }
}
