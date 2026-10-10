<?php

namespace Tests\Feature;

use App\Support\Unp\PaginadorAnexo;
use Dompdf\Dompdf;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AnexosImpressaoTest extends TestCase
{
    private function pessoas(int $quantidade, int $inicio = 1): array
    {
        $pessoas = [];
        for ($i = 0; $i < $quantidade; $i++) {
            $numero = $inicio + $i;
            $pessoas[] = (object) ['id' => $numero, 'nome' => 'Pessoa ' . str_pad((string) $numero, 4, '0', STR_PAD_LEFT) . ' da Silva', 'cpf' => '529.982.247-25'];
        }
        return $pessoas;
    }

    private function anexo(array $listas): object
    {
        return (object) ['titulo' => 'LISTA DE CONVIDADOS', 'listas_formatadas' => $listas];
    }

    public static function limites(): array
    {
        return [[0, [0]], [1, [1]], [30, [30]], [31, [30, 1]], [60, [30, 30]], [61, [30, 30, 1]]];
    }

    #[DataProvider('limites')]
    public function test_limite_de_trinta_sem_perder_nomes(int $quantidade, array $esperado): void
    {
        $paginas = PaginadorAnexo::paginas(['CONVIDADOS' => collect($this->pessoas($quantidade))]);
        $this->assertSame($esperado, array_column($paginas, 'quantidade'));
        $ids = [];
        foreach ($paginas as $pagina) {
            foreach ($pagina['secoes'] as $secao) {
                foreach ($secao['convidados'] as $pessoa) {
                    $ids[] = $pessoa->id;
                }
            }
        }
        $this->assertSame($quantidade ? range(1, $quantidade) : [], $ids);
    }

    public function test_conta_categorias_juntas_e_identifica_continuacao(): void
    {
        $paginas = PaginadorAnexo::paginas([
            'PASTORES/ESPOSAS' => collect($this->pessoas(5)),
            'VOLUNTÁRIOS DA ORGANIZAÇÃO' => collect($this->pessoas(40, 6)),
        ]);
        $this->assertSame([30, 15], array_column($paginas, 'quantidade'));
        $this->assertSame([5, 25], array_map(fn ($s) => count($s['convidados']), $paginas[0]['secoes']));
        $this->assertFalse($paginas[0]['secoes'][1]['continuacao']);
        $this->assertTrue($paginas[1]['secoes'][0]['continuacao']);
    }

    public function test_categoria_nova_na_pagina_seguinte_nao_e_continuacao(): void
    {
        $paginas = PaginadorAnexo::paginas(['CONVIDADOS' => $this->pessoas(30), 'ORGANIZAÇÃO' => $this->pessoas(1, 31)]);
        $this->assertFalse($paginas[1]['secoes'][0]['continuacao']);
    }

    public function test_ids_excluidos_nao_criam_linhas_ou_paginas_vazias(): void
    {
        $paginas = PaginadorAnexo::paginas(['CONVIDADOS' => array_merge([null, null], $this->pessoas(30))]);
        $this->assertSame([30], array_column($paginas, 'quantidade'));
    }

    public function test_html_para_impressao_repete_cabecalho_rodape_e_preserva_nomes(): void
    {
        $html = view('livewire.unp.oficios.pdf.anexos-pdf', ['selectedAnexo' => $this->anexo(['CONVIDADOS' => collect($this->pessoas(61))])])->render();
        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new \DOMXPath($dom);
        $this->assertSame(3, $xpath->query('//section[@data-anexo-page]')->length);
        $this->assertSame(61, $xpath->query('//p[@class="anexo-print-name"]')->length);
        $this->assertSame(3, $xpath->query('//header[@class="anexo-print-header"]')->length);
        $this->assertSame(3, $xpath->query('//footer[@class="anexo-print-footer"]')->length);
        $this->assertStringContainsString('Página 3 de 3', $html);
        $this->assertStringContainsString('PESSOA 0061 DA SILVA', $html);
        $this->assertStringContainsString('(continuação)', $html);
    }

    public function test_nome_longo_e_preservado_integralmente(): void
    {
        $pessoa = (object) ['nome' => str_repeat('Nome Extenso ', 20), 'cpf' => '529.982.247-25'];
        $html = view('livewire.unp.oficios.partials.anexo-print-pages', ['selectedAnexo' => $this->anexo(['CONVIDADOS' => [$pessoa]])])->render();
        $this->assertStringContainsString(mb_strtoupper($pessoa->nome), $html);
    }

    #[DataProvider('limites')]
    public function test_renderizacao_a4_tem_paginas_esperadas_sem_folha_extra(int $quantidade, array $esperado): void
    {
        $html = view('livewire.unp.oficios.pdf.anexos-pdf', ['selectedAnexo' => $this->anexo(['CONVIDADOS' => collect($this->pessoas($quantidade))])])->render();
        // A imagem local da aplicação não é acessada pelo teste.
        $html = preg_replace('/<img[^>]+>/i', '<span style="display:inline-block;width:40mm;height:14mm">Logo UNP</span>', $html);
        $pdf = new Dompdf(['isRemoteEnabled' => false, 'isJavascriptEnabled' => false, 'defaultMediaType' => 'print']);
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->render();
        $this->assertSame(count($esperado), $pdf->getCanvas()->get_page_count());
    }
}
