# Etapa 6 — Automação segura da sincronização TSE

## Objetivo

A sincronização de candidaturas oficiais não depende mais de um operador executar manualmente um comando pesado. O painel oferece um botão, mas a requisição web apenas cria uma solicitação persistente. O Laravel Scheduler executa o download/importação fora da navegação do usuário.

## Fluxo

```text
Dashboard / Dados Oficiais
        ↓
Atualizar TSE agora
        ↓
politica_tse_solicitacoes (pendente)
        ↓
Laravel Scheduler (a cada minuto)
        ↓
politica:tse-processar-solicitacoes
        ↓
politica:tse-sincronizar 2026 --somente=candidaturas
        ↓
TSE → banco local → auditoria
```

Além do botão, o scheduler enfileira automaticamente uma atualização diária das candidaturas 2026. O padrão é 03:17 no fuso `America/Bahia`.

## Produção

O servidor precisa executar uma única entrada de cron do Laravel:

```cron
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

O painel mostra `Scheduler ativo` quando o processador registrou heartbeat nos últimos três minutos. Caso contrário mostra `Aguardando cron`.

## Configuração

```env
POLITICA_TSE_QUEUE_ENABLED=true
POLITICA_TSE_AUTO_SYNC_ENABLED=true
POLITICA_TSE_AUTO_SYNC_YEAR=2026
POLITICA_TSE_AUTO_SYNC_UF=BA
POLITICA_TSE_AUTO_SYNC_CRON="17 3 * * *"
POLITICA_TSE_AUTO_SYNC_TIMEZONE=America/Bahia
POLITICA_TSE_MANUAL_COOLDOWN_MINUTES=10
POLITICA_TSE_REQUEST_STALE_MINUTES=30
POLITICA_TSE_PROCESSOR_LOCK_SECONDS=1800
```

## Segurança e carga

- O navegador nunca baixa o ZIP do TSE.
- Solicitações duplicadas pendentes/em execução não são criadas.
- Cliques manuais possuem cooldown.
- Um lock impede dois processadores da fila ao mesmo tempo.
- Solicitações interrompidas são recuperadas automaticamente após o tempo configurado.
- O recorte econômico e os limites de armazenamento da Etapa 5 continuam válidos.
- A apuração ao vivo continua separada e desligada até habilitação explícita.
