# Política V2 — Etapa 10: Painel Executivo de Prioridades

## Objetivo

Consolidar em uma única tela a leitura dos quatro históricos especiais, reutilizando exclusivamente resultados oficiais e as regras transparentes da Inteligência Territorial.

Rota: `/politica/painel-executivo`.

## Regras

- cada acompanhado usa os dois pleitos mais recentes com resultado municipal **do mesmo cargo**;
- se houver apenas um pleito do cargo, o painel mostra o último resultado e marca `aguardando`, sem criar delta;
- municípios sem linha oficial em um dos anos continuam fora da comparação e nunca recebem zero artificial;
- a ordenação executiva usa o `relevancia_score` da Etapa 9 e não representa probabilidade, previsão eleitoral ou recomendação de persuasão;
- a fila do espelho é operacional: prioriza revisão/qualidade de dados internos em municípios que também possuem sinal estatístico forte;
- uma cidade aparece apenas uma vez na fila operacional, mesmo quando há sinais para mais de um acompanhado.

## Blocos

1. resumo executivo;
2. situação individual dos acompanhados;
3. variações negativas de maior relevância estatística;
4. sinais positivos recentes;
5. fila de revisão do espelho operacional;
6. alertas de cobertura e metodologia.

## Carga

Não há nova tabela nem migration. O painel reutiliza no máximo os resultados municipais já existentes (aprox. 417 municípios por contexto na Bahia) e não baixa arquivos externos.
