# Política V2 — Histórico oficial especial

Esta evolução cria uma exceção deliberada ao recorte econômico do módulo.

## Quem recebe histórico completo

Somente:

- Márcio Marinho
- Rogéria Santos
- José de Arimateia
- Jurailton Santos

O restante da base continua com o recorte econômico já definido.

## Fontes

- Eleições, candidaturas, partidos, números, situações e votos: Dados Abertos do TSE.
- Mandatos/cargos institucionais de Márcio Marinho, José de Arimateia e Jurailton Santos: Assembleia Legislativa da Bahia.
- Mandatos/cargos institucionais de Rogéria Santos: Câmara dos Deputados.

O sistema não transforma resultado eleitoral em posse automaticamente. Mandato/cargo só é gravado quando uma fonte institucional oficial descreve o período.

## Comando

Dry-run de candidaturas históricas:

```bash
php artisan politica:historico-oficial --dry-run --somente=candidaturas
```

Sincronização institucional de mandatos/cargos:

```bash
php artisan politica:historico-oficial --somente=mandatos
```

Sincronização das candidaturas TSE:

```bash
php artisan politica:historico-oficial --somente=candidaturas
```

Depois, com as candidaturas persistidas, validar os resultados:

```bash
php artisan politica:historico-oficial --somente=resultados --dry-run
```

E então gravar os resultados:

```bash
php artisan politica:historico-oficial --somente=resultados
```

É possível limitar anos:

```bash
php artisan politica:historico-oficial --anos=2002,2006,2008,2010
```

## Armazenamento

Embora os arquivos brutos do TSE possam ser grandes durante o processamento, somente as quatro pessoas são selecionadas no escopo `historico-especial`. Os ZIPs são removidos após cada processamento, salvo com `--manter-arquivos`.

Os resultados permanentes continuam usando município e zona; o comando não cria novo histórico por seção.

## Interface

Os quatro perfis exibem uma seção `Histórico oficial especial` que combina:

- candidaturas oficiais do TSE;
- votos e situação eleitoral quando disponíveis;
- mandatos/cargos de fontes legislativas oficiais;
- links para a fonte institucional.

Eleições e mandatos permanecem conceitos separados para evitar inferências incorretas de posse.
