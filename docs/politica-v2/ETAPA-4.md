# Política V2 — Etapa 4

## Objetivo

Corrigir pontos funcionais remanescentes da V1 e amadurecer a experiência da Política V2 para desktop/mobile, tema escuro e navegação operacional.

## Correções funcionais

### Mapa interativo

O mapa legado dependia de `window.L`, porém Leaflet não era carregado pelo `app.js`, `package.json` nem pelo layout. A rota principal agora aponta para `App\Livewire\Politica\V2\MapaInterativo`.

O novo mapa:

- carrega Leaflet sob demanda somente na página do mapa;
- usa `preferCanvas` e `circleMarker`, evitando centenas de ícones DOM;
- exibe os municípios com coordenadas válidas;
- permite filtrar eleição, cargo e candidatura;
- dimensiona os pontos pela força relativa dos votos quando há candidatura selecionada;
- abre o Espelho Inteligente do município pelo popup;
- adapta a camada cartográfica ao tema escuro por CSS;
- usa altura responsiva em celular e desktop;
- limita o seletor de candidaturas por configuração para não gerar HTML excessivo.

Configuração:

```env
POLITICA_MAPA_MAX_CANDIDATURAS=150
```

### Editor operacional

O botão `Editar operacional` da V2 apontava para `EspelhoManager`, que ainda gravava em `politica_espelhos` (V1). A rota agora usa `EspelhoOperacionalEdit` e grava diretamente em `politica_espelho_operacional`.

Os dados eleitorais nunca são alterados pelo editor operacional.

## Regra do Espelho Inteligente

Por padrão:

- Vereador: partido prioritário;
- Deputado Estadual: partido prioritário;
- Deputado Federal: partido prioritário;
- Senador: partido prioritário;
- Governador: todos os candidatos;
- Presidente: todos os candidatos;
- demais cargos executivos: todos os candidatos.

O partido prioritário padrão é `REPUBLICANOS`, configurável por ambiente:

```env
POLITICA_PARTIDO_PRIORITARIO=REPUBLICANOS
POLITICA_ESPELHO_RANKING_POR_PAGINA=25
```

Nos cargos legislativos o usuário ainda pode alternar para `Todos os partidos`. O ranking é paginado, portanto o Espelho não corta os candidatos do partido prioritário em um Top 50.

## Responsividade e tema escuro

Foram revisadas as principais telas da V2:

- Dashboard;
- Acompanhamento Prioritário;
- Lista de municípios;
- Espelho Inteligente;
- Perfil político;
- Editor operacional;
- Mapa eleitoral.

A lista de municípios e o ranking do Espelho usam cards em telas pequenas e tabela em desktop. Controles usam largura total no celular e voltam ao layout horizontal em breakpoints maiores.

## Legado

As rotas V1 não foram apagadas. Elas foram removidas da navegação principal e movidas para `/politica/legado/...`, mantendo acesso para auditoria/transição sem confundir o fluxo V2.

## Testes

A Etapa 4 adiciona `PoliticaV2ExperienciaTest`, cobrindo:

- filtro Republicanos em cargo legislativo;
- exibição de todos os candidatos para Presidente;
- possibilidade de alternar legislativo para todos os partidos;
- gravação do editor na tabela V2;
- renderização do mapa V2 sem depender do componente legado.

Também amplia `PoliticaV2RotasTest` para verificar mapa e editor V2.
