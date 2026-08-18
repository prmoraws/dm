# Política V2 — Etapa 6: coletor de apuração 2026

## Objetivo

Preparar a aplicação para consumir a divulgação oficial de resultados do TSE com baixo consumo de rede, CPU e banco, sem fazer requisições externas a partir dos navegadores.

Fluxo:

```text
TSE CDN
  ↓
EA14 (acompanhamento Brasil)
  ↓ detecta mudança em BR/BA
EA20 somente dos cargos necessários
  ↓
politica_apuracoes + politica_apuracao_candidaturas
  ↓
checkpoint apenas dos acompanhados
  ↓
snapshot JSON pequeno
  ↓
Painel 2026
```

## Segurança por padrão

A coleta real nasce desligada:

```env
POLITICA_APURACAO_LIVE_ENABLED=false
POLITICA_APURACAO_SCHEDULE_ENABLED=false
```

Enquanto `LIVE_ENABLED=false`, `politica:apuracao-coletar` interrompe antes de qualquer HTTP.

O comando abaixo é sempre seguro e não acessa a internet:

```bash
php artisan politica:apuracao-status --ano=2026 --turno=1 --uf=BA
```

Ele mostra o código da eleição, URLs que seriam utilizadas, intervalo e limites.

## Consumo

Por ciclo são permitidas no máximo 6 requisições lógicas:

1. EA14 Brasil;
2. EA20 Presidente/BR;
3. EA20 Governador/BA;
4. EA20 Senador/BA;
5. EA20 Deputado Federal/BA;
6. EA20 Deputado Estadual/BA.

O navegador não acessa o TSE.

O intervalo local mínimo foi definido em 60 segundos. O TSE informou em 2026 que a cadência recomendada ainda será validada nos simulados; a documentação operacional de 2024 recomendava intervalo não inferior a 60 segundos. Quando o TSE publicar orientação definitiva para 2026, a variável poderá ser revista sem alteração de código.

```env
POLITICA_TSE_POLL_SECONDS=60
POLITICA_TSE_MAX_REQUESTS_PER_CYCLE=6
```

Mesmo se uma variável tentar configurar período menor, a aplicação mantém o piso de 60 segundos.

## HTTP condicional

Cada fonte mantém `ETag` e `Last-Modified` em `politica_fontes_estado`. O cliente envia `If-None-Match` e `If-Modified-Since`; HTTP 304 não é reprocessado.

O TSE confirmou para 2026 suporte da CDN a ETag/Last-Modified e respostas 304. Requisições 304 ainda contam para o rate limit, por isso a aplicação também mantém cooldown local e usa EA14 para decidir quando vale consultar EA20.

## EA14 como sinalizador

O EA14 acompanha seções/eleitorado de Brasil e UFs. A aplicação guarda uma assinatura local dos sinais BR e BA. Quando um deles muda, abre uma janela curta de rechecagem dos EA20 correspondentes.

A janela existe porque o TSE alerta que EA14/EA15 e EA20 são gerados em paralelo e podem chegar à CDN em momentos ligeiramente diferentes.

```env
POLITICA_APURACAO_RECHECK_GRACE_SECONDS=180
```

## Endpoints

Os nomes seguem a documentação TSE:

```text
EA14:
br-e<ELEICAO>-ab.json

EA20 Brasil/UF:
<br|uf>-c<CCCC>-e<ELEICAO>-u.json
```

O código da eleição não é hardcoded. Ele vem de `politica_eleicoes.tse_eleicao_codigo`, preenchido pela sincronização oficial de candidaturas.

Códigos de cargo usados pela documentação:

```text
0001 Presidente
0003 Governador
0005 Senador
0006 Deputado Federal
0007 Deputado Estadual
```

## Persistência enxuta

A aplicação não cria candidaturas a partir da apuração. O EA20 só é vinculado a `politica_candidaturas` que já vieram da base oficial TSE.

Isso mantém o recorte econômico:

- Presidente: todos importados;
- Governador BA: todos importados;
- Senado/Deputados BA: apenas o recorte já existente no banco, principalmente REPUBLICANOS e acompanhados.

A cada atualização são gravados:

- estado consolidado em `politica_apuracoes`;
- votos atuais em `politica_apuracao_candidaturas`;
- histórico somente de políticos acompanhados e respeitando checkpoint;
- snapshot JSON Top N + todos os acompanhados.

Não há histórico completo de todos os candidatos a cada minuto.

## Simulação/fixture local

Um arquivo EA20 local pode ser analisado sem internet e sem gravar:

```bash
php artisan politica:apuracao-coletar \
  --arquivo=/caminho/arquivo.json
```

Para persistir um fixture é obrigatório pedir explicitamente:

```bash
php artisan politica:apuracao-coletar \
  --ano=2026 \
  --turno=1 \
  --arquivo=/caminho/arquivo.json \
  --cargo="Presidente" \
  --abrangencia=BR \
  --gravar-fixture
```

Não utilizar `--gravar-fixture` com dados fictícios em produção.

## Agendamento

O scheduler também nasce desligado. Quando os simulados oficiais estiverem disponíveis e o parser for validado com os JSONs 2026 reais:

```env
POLITICA_APURACAO_LIVE_ENABLED=true
POLITICA_APURACAO_SCHEDULE_ENABLED=true
```

`routes/console.php` agenda uma execução por minuto com `withoutOverlapping()`. O serviço possui um segundo lock e cooldown, criando proteção em camadas.

## Variáveis

```env
POLITICA_APURACAO_LIVE_ENABLED=false
POLITICA_APURACAO_SCHEDULE_ENABLED=false
POLITICA_APURACAO_BASE_URL=https://resultados.tse.jus.br
POLITICA_APURACAO_AMBIENTE=oficial
POLITICA_APURACAO_CICLO=ele2026
POLITICA_TSE_POLL_SECONDS=60
POLITICA_APURACAO_LOCK_SECONDS=55
POLITICA_APURACAO_RECHECK_GRACE_SECONDS=180
POLITICA_TSE_MAX_REQUESTS_PER_CYCLE=6
POLITICA_APURACAO_CONNECT_TIMEOUT=3
POLITICA_APURACAO_REQUEST_TIMEOUT=8
POLITICA_APURACAO_CHECKPOINT_SECONDS=60
POLITICA_APURACAO_SNAPSHOT_TOP=50
POLITICA_SNAPSHOT_DISK=local
POLITICA_SNAPSHOT_PATH=politica/apuracao
```

## Antes de habilitar ao vivo

1. Participar/testar com os simulados TSE previstos para setembro de 2026.
2. Confirmar o código de eleição/pleito pelo `ele-c.json` oficial.
3. Validar EA14 e EA20 reais de 2026 contra os parsers.
4. Rever a cadência publicada pelo TSE.
5. Executar toda a suíte `php artisan test --filter=Politica`.
6. Manter apenas um coletor por ambiente.

## Fontes técnicas oficiais

- TSE — Informações técnicas sobre a divulgação de resultados 2026: `https://www.tse.jus.br/eleicoes/informacoes-tecnicas-sobre-a-divulgacao-de-resultados`
- TSE — EA14, EA15 e EA20 publicados na mesma página.
- TSE — Instruções de download 2024, utilizadas apenas para a regra operacional de intervalo mínimo e nomenclatura, enquanto o TSE orienta conferir os documentos vigentes de 2026.
