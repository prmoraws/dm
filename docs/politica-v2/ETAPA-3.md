# Política V2 — Etapa 3

## Objetivo

Disponibilizar a primeira interface profissional da Política V2 sem remover as telas legadas e sem depender de consultas externas em tempo real.

## Entregas

- Dashboard V2 em `/politica/dashboard`.
- Acompanhamento prioritário em `/politica/acompanhamento`.
- Perfil individual por `slug` em `/politica/politicos/{politico}`.
- Lista territorial em `/politica/cidades`.
- Espelho Inteligente em `/politica/espelho/{cidade}`.
- Rotas V1 preservadas durante a transição.
- Cache de 5 minutos para as métricas globais do dashboard.
- Ranking do Espelho limitado a no máximo 50 registros por recorte para evitar telas pesadas em eleições proporcionais.
- Perfil territorial limitado aos 20 municípios com maior votação.
- Cache do dashboard invalidado após a migração V1 → V2.

## Princípios

1. Pessoa política não é candidatura.
2. Ausência de dados oficiais é exibida como ausência, sem inferência.
3. Informações operacionais internas permanecem separadas dos resultados eleitorais.
4. Dados legados continuam identificados pela origem até confirmação por fonte oficial.
5. Nenhuma tela desta etapa faz polling ou consulta ao TSE.

## Testes adicionados

- `PoliticaDashboardV2Test`
- `PoliticaV2TelasTest`
- `PoliticaV2RotasTest`
