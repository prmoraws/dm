# Etapa 8 — Comparativo Territorial dos Acompanhados

## Objetivo

Comparar duas eleições do mesmo cargo para os quatro políticos com histórico especial, usando exclusivamente os resultados municipais oficiais já persistidos em `politica_resultados_municipais`.

## Regras de integridade

- O mesmo político deve ser comparado consigo mesmo.
- As duas eleições devem ser do mesmo cargo.
- A diferença municipal só é calculada quando existe uma linha oficial para o município nos dois pleitos.
- Município ausente em uma das eleições não é convertido em zero voto.
- A variação total da candidatura usa `politica_candidaturas.votos_total`.
- O saldo municipal comparável é exibido separadamente e não é confundido com a variação total quando a cobertura territorial difere.

Essa regra é especialmente importante para bases históricas antigas, nas quais a cobertura dos arquivos pode ser diferente entre os anos.

## Interface

Rota:

```text
/politica/comparativo-territorial
```

A tela oferece:

- político;
- cargo;
- eleição base;
- eleição comparada;
- votos totais dos dois pleitos;
- variação absoluta e percentual;
- quantidade de municípios comparáveis;
- maiores crescimentos e quedas;
- participação do município na votação total do candidato e variação em pontos percentuais;
- mapa com crescimento, queda e municípios sem par oficial;
- tabela pesquisável e paginada.

## Carga

Não há nova tabela nem nova importação. O serviço lê no máximo os resultados municipais das duas candidaturas selecionadas. Para Bahia, isso representa poucas centenas de linhas por candidatura.

O mapa usa as coordenadas já existentes em `politica_cidades` e não consulta o TSE pelo navegador.
