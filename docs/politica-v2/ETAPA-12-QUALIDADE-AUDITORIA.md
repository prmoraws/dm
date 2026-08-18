# Política V2 — Etapa 12: Centro de Qualidade e Auditoria

## Objetivo

Auditar a consistência da base antes de decisões analíticas ou exportações. A etapa não corrige resultados automaticamente e não baixa arquivos externos.

Rota: `/politica/qualidade-dados`.

## Verificações

- quantidade oficial de municípios da Bahia pelo código IBGE;
- municípios sem código TSE ou coordenadas;
- soma dos resultados municipais versus total da candidatura, sem comparar escopo nacional BR com recorte parcial;
- soma das zonas versus resultado municipal;
- candidatura municipal com resultado em outra cidade;
- candidaturas oficiais TSE sem partido ou sem identificador rastreável;
- duplicidade de identidade pública de políticos;
- acompanhados ativos sem foto ou com arquivo local ausente;
- municípios oficiais sem Espelho Operacional e espelhos sem revisão recente;
- múltiplos favoritos no mesmo município/eleição/cargo;
- Espelho ligado a eleição/cidade incompatível com a candidatura;
- fontes/importações TSE com erro e solicitações possivelmente travadas.

## Níveis

- `critico`: inconsistência objetiva (ex.: soma maior que o total, relacionamento incompatível);
- `alerta`: cobertura parcial, desatualização ou dado que precisa de conferência;
- `info`: condição conhecida e preservada, como registros auxiliares do legado.

## Carga

O snapshot usa agregações SQL e cache curto (`politica.qualidade.cache_minutes`) para não repetir varreduras a cada renderização Livewire. O botão **Recalcular auditoria** invalida apenas esse cache.

## Exportação

PDF e Excel respeitam os filtros de nível, grupo e busca visíveis na tela.

## CLI

```bash
php artisan politica:auditar-dados
php artisan politica:auditar-dados --sem-cache
php artisan politica:auditar-dados --sem-cache --falhar-em-critico
```

O comando é somente leitura. A opção `--falhar-em-critico` é útil em validações de deploy/CI.
