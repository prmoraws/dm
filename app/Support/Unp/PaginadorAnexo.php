<?php

namespace App\Support\Unp;

class PaginadorAnexo
{
    public static function paginas(iterable $listas): array
    {
        $nomes = [];
        foreach ($listas as $titulo => $convidados) {
            foreach ($convidados as $convidado) {
                if ($convidado) {
                    $nomes[] = ['categoria' => $titulo, 'convidado' => $convidado];
                }
            }
        }
        $paginas = [];
        $categoriasAnteriores = [];
        foreach (array_chunk($nomes, 30) as $numero => $itens) {
            $secoes = [];
            foreach ($itens as $item) {
                $titulo = $item['categoria'];
                if (!isset($secoes[$titulo])) {
                    $secoes[$titulo] = [
                        'titulo' => $titulo,
                        'continuacao' => isset($categoriasAnteriores[$titulo]),
                        'convidados' => [],
                    ];
                }
                $secoes[$titulo]['convidados'][] = $item['convidado'];
            }
            foreach ($secoes as $titulo => $secao) {
                $categoriasAnteriores[$titulo] = true;
            }
            $paginas[] = ['numero' => $numero + 1, 'quantidade' => count($itens), 'secoes' => array_values($secoes)];
        }
        return $paginas ?: [['numero' => 1, 'quantidade' => 0, 'secoes' => []]];
    }
}
