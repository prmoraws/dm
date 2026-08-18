# Política V2 — Etapa 9: Inteligência Territorial Automática

## Objetivo

Transformar o comparativo territorial oficial em uma leitura estratégica reproduzível, sem IA generativa, sem estimativa de votos e sem inferir zero quando um município não possui linha oficial em um dos pleitos.

Rota: `/politica/inteligencia-territorial`.

## Escopo

A tela usa somente políticos do histórico especial que possuam pelo menos duas eleições do mesmo cargo com resultados municipais. O par de eleições é normalizado pelo mesmo serviço da Etapa 8.

Municípios classificados precisam existir nos dois pleitos. Municípios presentes em apenas um arquivo continuam visíveis no Comparativo Territorial, mas ficam fora da classificação automática.

## Sinais

- **Recuperação**: exige um terceiro pleito anterior do mesmo cargo no mesmo município. A votação caiu entre o pleito anterior e a eleição-base e, no pleito comparado, recompôs pelo menos 50% da perda.
- **Perda relevante**: votos e participação caíram; a variação absoluta está no quartil superior (P75) das diferenças do próprio recorte.
- **Concentração**: o município está no decil superior (P90) da participação na votação atual do candidato e ganhou participação em relação à eleição-base.
- **Fortalecimento**: votos e participação cresceram; a variação absoluta está no quartil superior (P75) do próprio recorte.
- **Oportunidade de recuperação**: a votação caiu em município cuja base anterior estava no quartil superior (P75) de votos da eleição-base.
- **Sem sinal forte**: não atingiu nenhum critério acima.

Um município pode possuir mais de um sinal. O painel escolhe um sinal principal apenas para apresentação, preservando todos os sinais calculados.

## Score de relevância

O score de 0 a 100 serve exclusivamente para ordenar municípios dentro do recorte atual:

- 70%: intensidade da variação absoluta de votos em relação ao maior delta absoluto do recorte;
- 30%: intensidade da mudança de participação em relação à maior mudança de participação do recorte;
- recuperação confirmada recebe bônus de até 10 pontos, limitado a 100.

O score **não é probabilidade eleitoral, previsão de votos ou recomendação de campanha**.

## Espelho Inteligente

A classificação calculada é cruzada apenas para exibição com `politica_espelho_operacional`:

- existência do espelho;
- revisão do espelho;
- presidente local;
- indicação do bispo;
- quantidade cadastrada de filiados Republicanos.

Nenhum dado operacional é sobrescrito automaticamente. A tela oferece links para abrir e editar o Espelho Inteligente da cidade.

## Performance

O máximo esperado no recorte da Bahia é aproximadamente 417 municípios. Os cálculos são feitos em memória depois de duas consultas de resultados municipais e uma consulta de espelhos operacionais. Não há polling, nova tabela, migration ou chamada ao TSE durante a navegação.
