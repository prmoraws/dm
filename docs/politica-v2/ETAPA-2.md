# Política V2 — Etapa 2

## Objetivo

Migrar a base útil da Política V1 para a arquitetura V2 sem apagar a V1, preservando rastreabilidade e evitando duplicação em reexecuções.

## Alterações de esquema

A migration `2026_08_13_220000_prepare_politica_v2_legacy_import.php`:

- adiciona `cidade_id`, `origem`, `legacy_candidato_id` e `origem_chave` em `politica_candidaturas`;
- cria `politica_espelho_operacional`, separando dados operacionais internos de dados eleitorais públicos/derivados;
- cria `politica_migracoes_dados`, que registra status e estatísticas das migrações de dados.

## Migração V1 → V2

Comando:

```bash
php artisan politica:v2-migrar-legado --escopo=prioritarios --dry-run
php artisan politica:v2-migrar-legado --escopo=prioritarios
```

O escopo `prioritarios` migra os quatro candidatos da base histórica de 2022 mapeados no `config/politica.php`.

Depois de validar os prioritários, o restante pode ser migrado de forma idempotente:

```bash
php artisan politica:v2-migrar-legado --escopo=todos --dry-run
php artisan politica:v2-migrar-legado --escopo=todos
```

### Regras importantes

- Linhas repetidas na V1 para a mesma candidatura/seção são **somadas** antes de chegar à V2.
- Zona e seção são extraídas do formato legado `Zona: N / Seção: N`.
- Candidaturas municipais recebem `cidade_id` quando o legado comprova que o candidato possui votos em apenas um município.
- Candidaturas estaduais/federais não recebem `cidade_id`.
- Percentuais e posições não são inventados: permanecem `NULL` quando a V1 não possui universo completo para cálculo oficial.
- Dados antigos de prefeito existentes em `politica_espelhos` são preservados somente como `dados_publicos_legados`; não são tratados como fonte oficial atual.
- A V1 permanece intacta durante esta etapa.

## Validação contábil

Após a migração:

```bash
php artisan politica:v2-validar-migracao --escopo=prioritarios
```

A validação exige igualdade entre:

1. soma dos votos da V1;
2. `politica_candidaturas.votos_total`;
3. soma de `politica_resultados_secoes`;
4. soma de `politica_resultados_municipais`.

Somente depois dessa validação a migração deve ser considerada aprovada.

## Espelho Inteligente

O `EspelhoInteligenteService` agora:

- permite filtrar por eleição e cargo;
- separa dados operacionais de dados eleitorais;
- informa origem/qualidade do recorte;
- avisa quando há dados legados ou percentuais oficiais incompletos;
- calcula concentração territorial dos votos de uma candidatura.

A interface visual será construída sobre esta camada, sem consultar os arquivos legados diretamente.
