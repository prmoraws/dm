# Etapa 7 — Histórico dos Acompanhados

## Objetivo

Criar uma visão comparativa e responsiva do histórico oficial ampliado dos quatro políticos do recorte especial:

- Márcio Marinho;
- Rogéria Santos;
- José de Arimateia;
- Jurailton Santos.

A página não amplia o banco para outros candidatos e não duplica resultados. Ela consulta candidaturas, resultados municipais/zonais e mandatos institucionais já existentes.

## Rota

`/politica/historico-acompanhados`

## Filtros

- político;
- cargo;
- ano eleitoral.

Os filtros são refletidos na URL pelo Livewire.

## Evolução eleitoral

Cada eleição mostra, quando disponível:

- cargo, partido, número e situação;
- votos oficiais;
- maior votação municipal;
- quantidade de municípios/zonas cobertos;
- variação absoluta e percentual em relação à eleição anterior do mesmo cargo.

A variação nunca compara cargos diferentes. Uma candidatura de prefeito, por exemplo, não é usada como base para medir crescimento de uma candidatura a deputado.

Candidaturas de 2026 sem resultado continuam exibidas como registro oficial, sem converter ausência de apuração em `0 votos`.

## Mandatos

Mandatos/cargos públicos permanecem em bloco separado das eleições e usam as fontes institucionais já cadastradas. O sistema não infere posse a partir do resultado eleitoral.

## Desempenho

A maior votação municipal é obtida em consulta agregada por candidatura; a página não carrega todos os resultados municipais na memória. O recorte contém somente quatro políticos, mantendo o custo previsível.

Não há migration nesta etapa.
