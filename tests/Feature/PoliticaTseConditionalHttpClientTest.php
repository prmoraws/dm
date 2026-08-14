<?php

namespace Tests\Feature;

use App\Models\Politica\V2\FonteEstado;
use App\Services\Politica\V2\TseConditionalHttpClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliticaTseConditionalHttpClientTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usa_etag_e_trata_304_sem_processar_json_novamente(): void
    {
        $fonte = FonteEstado::query()->create([
            'chave' => 'tse:teste',
            'fonte' => 'tse',
            'tipo_arquivo' => 'json',
            'url' => 'https://resultados.exemplo.test/arquivo.json',
            'etag' => '"abc123"',
        ]);

        Http::fake([
            'resultados.exemplo.test/*' => Http::response('', 304),
        ]);

        $resultado = app(TseConditionalHttpClient::class)->fetch($fonte);

        Http::assertSent(fn ($request) => $request->hasHeader('If-None-Match', '"abc123"'));
        $this->assertFalse($resultado['changed']);
        $this->assertTrue($resultado['not_modified']);
        $this->assertSame(304, $resultado['status']);
    }
}
