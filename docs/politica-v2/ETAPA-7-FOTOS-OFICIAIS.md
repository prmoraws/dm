# Etapa 7 — Fotos oficiais dos acompanhados

Os oito acompanhamentos prioritários usam fotos oficiais do conjunto `Candidatos - 2026` do Portal de Dados Abertos do TSE.

- Presidência: arquivo BR.
- Bahia: arquivo BA.
- A vinculação é feita por `SQ_CANDIDATO`, nunca por nome ou número isolado.
- Somente as oito fotos necessárias são extraídas.
- Os ZIPs temporários são apagados após o processamento.
- A foto final fica em `public/images/politica/oficiais/<slug>.jpg`.
- `politica_politicos.foto_url` aponta para o arquivo local.
- A origem/licença/SQ ficam registrados em `politica_politicos.links.foto_oficial`.

Comando manual:

```bash
php artisan politica:fotos-oficiais 2026 --forcar
```

Após uma sincronização automática de candidaturas 2026, o processador também tenta atualizar as fotos. Falha de foto não invalida a atualização eleitoral principal.
