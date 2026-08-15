# Política V2 — Etapa 5: Base Oficial TSE (escopo econômico)

## Objetivo

Substituir progressivamente o recorte legado por dados oficiais do Portal de Dados Abertos do TSE, mantendo o MySQL pequeno e sem transformar cada acesso ao sistema em consulta externa.

## Escopo permanente do banco

No `--escopo=espelho`:

- REPUBLICANOS + acompanhamentos prioritários:
  - Vereador
  - Prefeito
  - Deputado Estadual
  - Deputado Federal
  - Senador
- Todos os candidatos:
  - Governador
  - Presidente

Para Presidente, `votos_total` continua nacional; o detalhamento territorial salvo permanece restrito à Bahia.

Resultados permanentes ficam em município e zona. A Etapa 5 não importa votação completa por seção.

## Proteção de armazenamento

O módulo mede `DATA_LENGTH + INDEX_LENGTH` das tabelas `politica_*` em MySQL/MariaDB.

Padrões:

- aviso: 500 MB
- bloqueio: 900 MB

Configuração:

```env
POLITICA_TSE_STORAGE_WARNING_MB=500
POLITICA_TSE_STORAGE_HARD_LIMIT_MB=900
POLITICA_TSE_CLEANUP_AFTER_SUCCESS=true
```

O bloqueio pode ser ultrapassado apenas conscientemente com `--ignorar-limite`.

Para consultar o tamanho real:

```bash
php artisan politica:tamanho
php artisan politica:tamanho --top=25
```

## Downloads temporários

Os ZIPs oficiais continuam fora de `public/`, em `storage/app/private/politica/tse` por padrão.

- dry-run: mantém o ZIP para a execução real seguinte;
- importação concluída com sucesso: remove o ZIP automaticamente;
- `--manter-arquivos`: preserva o ZIP deliberadamente;
- arquivo informado manualmente por `--arquivo-*`: nunca é apagado pelo sistema.

Para limpar downloads antigos sem tocar no MySQL:

```bash
php artisan politica:tse-limpar-cache
php artisan politica:tse-limpar-cache --ano=2022
```

## Princípios

- Download/importação via Artisan/CLI.
- Navegador consulta somente o banco local.
- ZIP é lido em streaming, sem extração completa para disco.
- ETag/Last-Modified continuam registrados quando a origem os fornece.
- Toda importação real é auditada em `politica_tse_importacoes`.
- Importação de resultados substitui de forma idempotente os agregados das candidaturas afetadas.

## Comando principal

```bash
php artisan politica:tse-sincronizar 2022 --uf=BA --escopo=espelho --dry-run
php artisan politica:tse-sincronizar 2022 --uf=BA --escopo=espelho
```

Para 2024, antes de gravar:

```bash
php artisan politica:tse-sincronizar 2024 \
  --uf=BA \
  --escopo=espelho \
  --somente=candidaturas \
  --dry-run
```

O resumo deve selecionar apenas vereadores/prefeitos do REPUBLICANOS, além de qualquer acompanhamento prioritário reconhecido.

## Interface

Rota: `/politica/dados-oficiais`

Além da cobertura e auditoria, passa a mostrar:

- tamanho das tabelas `politica_*`;
- tamanho do banco completo;
- faixa de aviso;
- limite de bloqueio;
- regra resumida do escopo econômico.

A tela não executa importação pesada.

## Sequência recomendada

1. 2022 já validado contra os prioritários.
2. Aplicar o recorte econômico antes de 2024.
3. Fazer dry-run de candidaturas 2024 e conferir o recorte.
4. Importar candidaturas 2024.
5. Fazer dry-run e importar resultados 2024.
6. Sincronizar candidaturas 2026 no mesmo recorte.
7. Seções eleitorais, caso necessárias, somente sob demanda/prioritários em etapa futura.
