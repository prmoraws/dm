# Política V2 — Painel Eleições 2026

## Objetivo

Consolidar em uma tela somente leitura os registros oficiais TSE já sincronizados para 2026, sem consultas externas durante a navegação e sem confundir acompanhamento, registro de candidatura e resultado eleitoral.

## Blocos

- Presidência: todos os registros oficiais importados.
- Governo da Bahia: todos os registros oficiais importados.
- Senado: recorte econômico armazenado no banco (partido prioritário).
- REPUBLICANOS: Senado, Deputado Federal e Deputado Estadual da Bahia.
- Preparação para apuração: lê `politica_apuracoes` quando existirem snapshots, sem polling no navegador.

## Regras de apresentação

- Não existe ranking opinativo.
- Candidaturas com código técnico de situação continuam exibindo o valor bruto para auditoria, mas usam a tradução segura definida no model.
- Ausência de resultado não é exibida como `0 votos`.
- Acompanhamento prioritário é um sinal separado do registro oficial TSE.
- A tela usa apenas o MySQL local; nenhuma chamada ao TSE acontece durante o acesso do usuário.

## Cache

O resumo usa cache de 5 minutos na chave `politica:v2:eleicoes-2026:resumo`.
A sincronização TSE invalida o cache do Dashboard e do Painel 2026 ao concluir candidaturas/resultados.

## Rota

`/politica/eleicoes-2026`

Nome: `politica.eleicoes-2026`.
