# Etapa 7 — Filiações partidárias oficiais

Filiação partidária passa a ser exibida separadamente de eleição e mandato.

## José de Arimateia

Fontes institucionais usadas:

- ALBA: histórico de filiações partidárias;
- Dados Abertos TSE: candidatura de 2004 pelo PSL, que confirma o vínculo eleitoral naquele ano.

Linha consolidada:

- PMDB: 1997–2001;
- PFL: 2001–2003;
- PSL: 2004–2007;
- PRB: 2007–2019;
- Republicanos: 2019–atual.

A referência secundária que aponta período `sem partido` em 2003–2004 não é gravada como fato oficial enquanto não houver fonte primária suficiente para delimitar esse intervalo.

O banco armazena anos e texto de período sem inventar datas exatas de filiação.

## Sincronização

```bash
php artisan migrate
php artisan politica:historico-oficial --somente=filiacoes
```

A sincronização é idempotente e, nesta etapa, só há filiações configuradas para José de Arimateia. A estrutura fica pronta para os demais acompanhados quando suas fontes institucionais forem auditadas.
