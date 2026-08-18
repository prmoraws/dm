# Política V2 — Etapa 11: Relatórios Executivos

## Objetivo

Permitir a exportação do Painel Executivo da Etapa 10 sem recalcular critérios em outra camada e sem introduzir novas fontes ou estimativas.

## Exportações

- PDF A4 paisagem, pronto para leitura e apresentação;
- Excel `.xlsx` com seis abas: Resumo, Acompanhados, Variações negativas, Sinais positivos, Pendências espelho e Metodologia;
- o filtro do acompanhado selecionado no painel é preservado nas duas exportações.

## Rastreabilidade

Os relatórios reutilizam `PainelExecutivoService` e, portanto, mantêm exatamente as mesmas regras:

- somente pleitos do mesmo cargo;
- ausência oficial não é zero;
- score de relevância é descritivo e não representa previsão, pesquisa ou probabilidade eleitoral;
- pendências do espelho são tarefas de qualidade de dados internos;
- nenhuma exportação baixa dados externos ou grava novos resultados eleitorais.

## Dependências

A implementação reutiliza dependências já existentes no projeto:

- `barryvdh/laravel-dompdf` para PDF;
- `maatwebsite/excel` para XLSX.

Não há migration nem novo pacote Composer.
