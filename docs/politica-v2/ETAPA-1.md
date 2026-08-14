# Política V2 — Etapa 1

## Objetivo

Criar uma fundação escalável para histórico eleitoral, acompanhamento prioritário, espelho territorial e apuração, sem remover ainda as tabelas legadas antes da análise do dump de produção.

## Entregue nesta etapa

- Entidade permanente `politica_politicos` separada de `politica_candidaturas`.
- Partidos, cargos, eleições, filiações e mandatos normalizados.
- Acompanhamentos prioritários configuráveis, com Flávio Bolsonaro no grupo presidencial.
- Zonas, seções e resultados por município/zona/seção.
- Estado de fontes externas com ETag/Last-Modified e controle de falhas.
- Estado consolidado da apuração separado do histórico.
- Histórico de apuração planejado apenas para candidaturas acompanhadas.
- Snapshot reduzido de apuração: top N + políticos prioritários.
- Espelho inteligente municipal com metas/classificação e serviço de resumo agregado.
- Importadores legados alterados para uso de memória controlado e inserts em lotes.
- Correções em rollbacks das migrations legadas do módulo Política.
- Testes PHPUnit adicionados para estrutura V2, prioridades, espelho e HTTP condicional.

## Acompanhamentos iniciais

- Rogéria Santos — Federal BA
- Márcio Marinho — Federal BA
- Jurailton Santos — Estadual BA
- José de Arimatéia — Estadual BA
- Lula — Presidência
- Flávio Bolsonaro — Presidência
- Jerônimo Rodrigues — Governo BA
- ACM Neto — Governo BA

A situação eleitoral não fica fixa nessa lista. O cadastro permanente representa a pessoa política; candidatura, registro e resultado pertencem à eleição e serão sincronizados de fonte oficial.

## Configuração de carga

Valores padrão em `config/politica.php`:

- `POLITICA_TSE_POLL_SECONDS=10`
- `POLITICA_APURACAO_CHECKPOINT_SECONDS=60`
- `POLITICA_SNAPSHOT_TTL_SECONDS=15`
- `POLITICA_TSE_MAX_REQUESTS_PER_CYCLE=50`

Os valores podem ser ajustados sem mudança de código.

## Seed inicial

Depois de executar as migrations V2:

```bash
php artisan db:seed --class="Database\\Seeders\\Politica\\PoliticaV2Seeder"
```

## Testes preparados

```bash
php artisan test --filter=PoliticaV2
php artisan test --filter=PoliticaEspelho
php artisan test --filter=PoliticaTse
```

## Próxima etapa

Após receber o dump do banco de produção:

1. Mapear dependências e volume real das tabelas legadas.
2. Criar migration de conversão V1 → V2 idempotente.
3. Associar candidatos legados às pessoas/candidaturas corretas.
4. Importar histórico TSE com identificadores oficiais.
5. Criar dashboard V2 e espelho profissional.
6. Implementar parser dos arquivos oficiais de apuração e simulador local.
