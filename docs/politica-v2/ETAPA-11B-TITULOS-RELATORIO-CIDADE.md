# Política V2 — Etapa 11B: títulos e relatório robusto por cidade

## Títulos das páginas

O layout principal usa `MW | @yield('title')`. Por isso as telas da Política agora também definem explicitamente `@section('title', '...')` no próprio Blade, inclusive títulos dinâmicos para perfil e espelho municipal.

`#[Title]` permanece nos componentes Livewire por compatibilidade, mas não é mais a única fonte do título exibido pelo layout atual.

## Relatório do Espelho Inteligente

Rota base: `/politica/espelho/{cidade}`.

O espelho ganhou um bloco **Relatório robusto por cidade**. O usuário escolhe uma candidatura do recorte atual e pode:

- marcar a candidatura como favorito persistente no `politica_espelho_inteligencia`;
- exportar PDF;
- exportar Excel `.xlsx`.

Quando um favorito ou acompanhamento já existe no recorte, ele é selecionado automaticamente. Ao marcar um novo favorito no mesmo município, eleição e cargo, outros registros com classificação `favorito` daquele mesmo recorte voltam para `acompanhamento`; registros históricos não são apagados.

## Conteúdo do relatório

O relatório é centrado no **município + candidatura selecionada** e contém:

1. identificação do município, IBGE, população e coordenadas;
2. espelho operacional interno;
3. candidato, partido, número, cargo, eleição, situação e rastreabilidade TSE;
4. votos e percentual no município;
5. participação do município no total de votos do candidato;
6. favorito, prioridade, metas e observações internas;
7. histórico do mesmo político e do mesmo cargo naquele município;
8. distribuição por zona eleitoral, quando disponível;
9. ranking do mesmo cargo/eleição no município;
10. auditoria de integridade do recorte;
11. metodologia e regras de cobertura.

## Ausência de dados

Ausência de linha municipal **não vira zero** no relatório. Ela aparece como `Sem linha oficial` e não gera delta histórico.

A conferência `soma dos municípios = votos_total` também não é aplicada automaticamente a candidaturas de abrangência `BR`, porque a base local pode possuir somente um recorte territorial do resultado nacional.

## Exportações

PDF:

`/politica/espelho/{cidade}/relatorio/pdf?candidatura={id}`

Excel:

`/politica/espelho/{cidade}/relatorio/excel?candidatura={id}`

O Excel contém as abas:

- Resumo;
- Espelho operacional;
- Favorito e metas;
- Histórico na cidade;
- Zonas;
- Ranking do recorte;
- Auditoria;
- Metodologia.

Não há migration nova.
