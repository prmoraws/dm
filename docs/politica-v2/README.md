# Política V2

Módulo de inteligência político-eleitoral do projeto DM, construído sobre Laravel/Livewire e orientado a dados oficiais, rastreabilidade e baixo custo operacional.

> Estado de fechamento local: módulo funcional concluído em 18/08/2026.  
> Baseline validada antes do deploy: **77 testes passando / 505 assertions**.

## Objetivos

O módulo organiza, cruza e apresenta informações eleitorais e operacionais sem misturar suas origens.

Princípios centrais:

- dados eleitorais exatos permanecem em banco relacional;
- fontes oficiais do TSE são preservadas e auditáveis;
- dados operacionais do Espelho não alteram resultados eleitorais;
- ausência de resultado nunca é transformada automaticamente em zero;
- comparações territoriais só ocorrem entre pleitos comparáveis do mesmo cargo;
- o navegador não consulta diretamente endpoints do TSE;
- importações em massa são processadas em streaming/lotes e os arquivos brutos são temporários;
- a apuração ao vivo permanece desligada até habilitação explícita;
- análises territoriais são descritivas e não representam previsão de voto.

## Escopo atual

### Acompanhamentos prioritários

O sistema acompanha prioritariamente:

- Rogéria Santos — Deputada Federal / BA;
- Márcio Marinho — Deputado Federal / BA;
- Jurailton Santos — Deputado Estadual / BA;
- José de Arimatéia — Deputado Estadual / BA;
- Jerônimo Rodrigues — Governo da Bahia;
- ACM Neto — Governo da Bahia;
- Lula — Presidência;
- Flávio Bolsonaro — Presidência.

O histórico eleitoral aprofundado é mantido para:

- Rogéria Santos;
- Márcio Marinho;
- Jurailton Santos;
- José de Arimatéia.

## Principais áreas

| Área | Rota | Finalidade |
|---|---|---|
| Dashboard | `/politica/dashboard` | visão geral da Política V2 |
| Acompanhamento | `/politica/acompanhamento` | políticos prioritários |
| Eleições 2026 | `/politica/eleicoes-2026` | registros oficiais e preparação da apuração |
| Espelho por cidades | `/politica/cidades` | entrada territorial por município |
| Espelho Inteligente | `/politica/espelho/{cidade}` | operação + eleitoral + favorito + relatório municipal |
| Editar Espelho | `/politica/espelho/{cidade}/editar` | dados operacionais internos |
| Mapa Eleitoral | `/politica/mapa` | leitura territorial cartográfica |
| Histórico | `/politica/historico-acompanhados` | linha do tempo dos quatro históricos especiais |
| Comparativo Territorial | `/politica/comparativo-territorial` | comparação de dois pleitos do mesmo cargo |
| Inteligência Territorial | `/politica/inteligencia-territorial` | sinais territoriais determinísticos |
| Painel Executivo | `/politica/painel-executivo` | consolidação executiva dos acompanhados |
| Qualidade dos Dados | `/politica/qualidade-dados` | auditoria e consistência da base |

As páginas principais definem `@section('title', ...)`, conforme o layout do projeto (`MW | ...`).

## Espelho Inteligente e relatório por cidade

O Espelho reúne duas camadas independentes:

### Operacional

Armazenado em `politica_espelho_operacional`, incluindo, quando disponível:

- presidente local;
- indicação;
- filiados Republicanos;
- observações;
- data da última revisão.

### Inteligência/favorito

Armazenado em `politica_espelho_inteligencia`.

O usuário pode selecionar uma candidatura no contexto da cidade e classificá-la como favorita. O relatório robusto por cidade usa o candidato favorito/selecionado e pode ser exportado em PDF ou Excel.

O relatório municipal reúne, conforme a cobertura disponível:

- dados territoriais e IBGE;
- contexto operacional;
- candidato, partido, número, cargo e eleição;
- votos no município e posição no recorte;
- peso do município no resultado conhecido;
- metas e observações internas;
- histórico do mesmo político/cargo naquela cidade;
- resultados por zona;
- ranking do cargo;
- auditoria de consistência e metodologia.

Quando não existe linha oficial de resultado para a candidatura, o relatório exibe ausência de dado, não `0 votos`.

## Importação oficial TSE

O recorte econômico da base evita crescimento desnecessário no servidor.

### Legislativo/local

Por padrão são importados registros do partido prioritário (`REPUBLICANOS`) para:

- Vereador;
- Prefeito;
- Deputado Estadual;
- Deputado Federal;
- Senador.

As candidaturas prioritárias são preservadas mesmo quando exigem tratamento excepcional.

### Executivo principal

Para:

- Governador;
- Presidente;

o escopo pode incluir todos os candidatos necessários ao acompanhamento.

### Histórico especial

Os quatro históricos especiais podem ser sincronizados independentemente do filtro partidário para preservar a trajetória eleitoral.

## Comandos principais

### Auditoria

```bash
php artisan politica:auditar-dados
php artisan politica:auditar-dados --sem-cache
php artisan politica:auditar-dados --sem-cache --falhar-em-critico
```

A auditoria verifica, entre outros pontos:

- soma municipal versus total conhecido da candidatura;
- soma zonal versus resultado municipal;
- conflitos de favoritos;
- vínculos territoriais;
- fotos locais ausentes;
- cobertura dos Espelhos;
- integridade de identificadores oficiais;
- saúde de importações/solicitações TSE.

### Fotos oficiais

```bash
php artisan politica:fotos-oficiais 2026 --forcar
php artisan politica:fotos-oficiais 2026 --slug=lula --forcar
```

As fotos são artefatos locais gerados e **não devem ser versionadas no Git**.

### Histórico oficial

Consulte a ajuda antes de uma importação em produção:

```bash
php artisan politica:historico-oficial --help
```

Os downloads históricos devem ser tratados como temporários; a base persistida é relacional.

### Sincronização TSE

```bash
php artisan politica:tse-sincronizar --help
php artisan politica:tse-solicitar-atualizacao --help
php artisan politica:tse-processar-solicitacoes --help
```

### Apuração 2026

```bash
php artisan politica:apuracao-status
php artisan politica:apuracao-coletar --help
```

O coletor só deve ser ativado quando a operação de 2026 estiver preparada e validada.

### Reconciliação territorial

```bash
php artisan politica:reconciliar-territorio-operacional
php artisan politica:reconciliar-territorio-operacional --aplicar
```

O primeiro comando é dry-run. O modo `--aplicar` move apenas vínculos seguros e não sobrescreve conflitos.

## Configuração

A configuração central fica em:

```text
config/politica.php
```

Variáveis relevantes podem incluir, conforme a versão implantada:

```dotenv
POLITICA_PARTIDO_PRIORITARIO=REPUBLICANOS

POLITICA_APURACAO_LIVE_ENABLED=false
POLITICA_APURACAO_SCHEDULE_ENABLED=false

POLITICA_QUALIDADE_CACHE_MINUTES=10
POLITICA_QUALIDADE_ESPELHO_REVISAO_DIAS=120
POLITICA_QUALIDADE_LIMITE_ITENS=30
```

Na primeira implantação em produção, mantenha a apuração automática desligada:

```dotenv
POLITICA_APURACAO_LIVE_ENABLED=false
POLITICA_APURACAO_SCHEDULE_ENABLED=false
```

Nunca versione `.env`, credenciais, senhas, chaves ou dumps do banco.

## Estrutura de dados

As tabelas V2 usam o prefixo `politica_`.

Grupos principais:

- `politica_politicos`, `politica_partidos`, `politica_cargos`;
- `politica_eleicoes`, `politica_candidaturas`;
- `politica_filiacoes`, `politica_mandatos`, `politica_acompanhamentos`;
- `politica_resultados_municipais`, `politica_resultados_zonas`;
- `politica_espelho_operacional`, `politica_espelho_inteligencia`;
- `politica_fontes_estado`, tabelas de controle/importação TSE;
- tabelas de apuração/checkpoints;
- fila persistente de solicitações TSE.

Os registros territoriais auxiliares do legado permanecem preservados quando necessários para rastreabilidade. Isso não significa que devam ser usados como município oficial.

## Inteligência territorial

O cálculo é determinístico e usa somente municípios cobertos pelos pleitos comparados.

Sinais existentes incluem:

- recuperação;
- perda relevante;
- concentração;
- fortalecimento;
- oportunidade de recuperação;
- estabilidade.

Os limiares são calculados no próprio recorte por percentis. O score serve apenas para ordenar intensidade relativa dentro do conjunto analisado; **não é probabilidade, previsão de voto ou recomendação de persuasão**.

## Exportações

O módulo possui exportações executivas em PDF e Excel:

- Painel Executivo;
- relatório robusto do Espelho por cidade/candidato favorito;
- Centro de Qualidade e Auditoria.

Os arquivos exportados reutilizam os mesmos serviços da interface para evitar divergência entre tela e relatório.

## Testes

Suíte principal:

```bash
php artisan test --filter=Politica
```

Validações adicionais antes de commit/deploy:

```bash
git diff --check
npm run build
php artisan optimize:clear
php artisan politica:auditar-dados --sem-cache
```

Baseline de fechamento do módulo:

```text
Tests: 77 passed (505 assertions)
Críticos de auditoria: 0
Municípios oficiais BA: 417
Espelhos operacionais: 417
```

O alerta de `espelho.revisao_vencida` representa revisão operacional pendente e não deve ser eliminado atualizando datas em massa sem conferência humana.

## Arquivos que não devem ir para o Git

Exemplos:

```text
.env
historico-*.txt
public/images/politica/oficiais/*.jpg
storage/framework/testing/
storage/app/politica/tse/
ZIPs/CSVs brutos baixados do TSE
dumps SQL
```

## Deploy em produção

A implantação deve ser incremental. **Não substitua o banco de produção por um dump local.**

Fluxo recomendado:

1. confirmar commit/branch de produção;
2. garantir árvore Git limpa;
3. gerar backup do banco;
4. gerar backup do código atual;
5. atualizar o código pelo Git;
6. instalar dependências de produção sem alterar versões inesperadamente;
7. executar migrations;
8. limpar caches;
9. compilar/publicar assets conforme a estratégia do servidor;
10. validar as rotas principais;
11. executar a auditoria da Política V2;
12. somente depois executar sincronizações oficiais necessárias.

Comandos de validação após o deploy:

```bash
php artisan migrate:status
php artisan optimize:clear
php artisan politica:auditar-dados --sem-cache
php artisan politica:apuracao-status
```

A implantação inicial deve terminar com:

- aplicação respondendo normalmente;
- migrations concluídas;
- nenhum crítico na auditoria;
- apuração automática ainda desabilitada;
- exportações PDF/Excel testadas;
- Espelho de uma cidade validado manualmente;
- backup anterior ao deploy preservado.

## Documentação por etapa

Os detalhes de implementação permanecem em `docs/politica-v2/`:

- Etapa 5 — TSE e Eleições 2026;
- Etapa 6 — apuração, automação e histórico oficial;
- Etapa 7 — histórico dos acompanhados, fotos e filiações;
- Etapa 8 — comparativo territorial;
- Etapa 9 — inteligência territorial;
- Etapa 10 — painel executivo;
- Etapa 11 — relatórios executivos;
- Etapa 11B — títulos e relatório robusto por cidade;
- Etapa 12 — qualidade e auditoria.

## Estado do módulo

O desenvolvimento funcional da Política V2 está encerrado para a primeira implantação.

Evoluções futuras não bloqueantes:

- ativação operacional da apuração ao vivo de 2026;
- revisão humana progressiva dos Espelhos;
- novas fontes documentais/RAG;
- enriquecimentos adicionais de perfis e fontes oficiais.

Antes de qualquer nova evolução, preserve a regra: **qualidade, rastreabilidade e consistência vêm antes de volume de dados ou novas telas**.
