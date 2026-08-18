<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Política - Qualidade e Auditoria dos Dados</title>
    <style>
        @page { margin: 18px 20px 24px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 8px; line-height: 1.35; }
        h1, h2, p { margin: 0; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 8px; margin-bottom: 9px; }
        .eyebrow { font-size: 7px; text-transform: uppercase; color: #4f46e5; font-weight: bold; }
        h1 { margin-top: 3px; font-size: 17px; }
        .meta { margin-top: 4px; color: #4b5563; }
        .kpis { width: 100%; border-collapse: separate; border-spacing: 4px 0; margin: 0 -4px 9px; }
        .kpis td { border: 1px solid #d1d5db; padding: 6px; }
        .label { font-size: 6.5px; color: #6b7280; text-transform: uppercase; font-weight: bold; }
        .value { margin-top: 2px; font-size: 14px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { border: 1px solid #d1d5db; background: #f3f4f6; padding: 4px; text-align: left; font-size: 6.5px; }
        table.data td { border: 1px solid #e5e7eb; padding: 4px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        .critical { color: #be123c; font-weight: bold; }
        .warning { color: #b45309; font-weight: bold; }
        .info { color: #0369a1; font-weight: bold; }
        .section { margin-top: 10px; }
        .section-title { font-size: 11px; font-weight: bold; margin-bottom: 4px; }
        .method { margin-top: 10px; border-top: 1px solid #d1d5db; padding-top: 6px; }
        .method p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">Política V2 · qualidade e rastreabilidade</div>
        <h1>Centro de Qualidade e Auditoria dos Dados</h1>
        <p class="meta">Status: {{ strtoupper($status) }} · Gerado em {{ $gerado_em->format('d/m/Y H:i') }} · Filtro: {{ $filtros['nivel'] }} / {{ $filtros['grupo'] }}{{ $filtros['busca'] ? ' / busca: '.$filtros['busca'] : '' }}</p>
    </div>

    <table class="kpis"><tr>
        <td><div class="label">Críticos</div><div class="value">{{ $contagens['critico'] }}</div></td>
        <td><div class="label">Alertas</div><div class="value">{{ $contagens['alerta'] }}</div></td>
        <td><div class="label">Candidaturas</div><div class="value">{{ number_format($metricas['candidaturas'], 0, ',', '.') }}</div></td>
        <td><div class="label">Municípios oficiais</div><div class="value">{{ number_format($metricas['municipios_oficiais'], 0, ',', '.') }}</div></td>
        <td><div class="label">Espelhos</div><div class="value">{{ number_format($metricas['espelhos_operacionais'], 0, ',', '.') }}</div></td>
    </tr></table>

    <div class="section">
        <div class="section-title">Achados do recorte exportado</div>
        <table class="data">
            <thead><tr><th>Nível</th><th>Grupo</th><th>Código</th><th>Achado</th><th>Contexto</th><th>Descrição</th></tr></thead>
            <tbody>
                @forelse ($achadosFiltrados as $item)
                    <tr>
                        <td class="{{ $item['nivel'] === 'critico' ? 'critical' : ($item['nivel'] === 'alerta' ? 'warning' : 'info') }}">{{ strtoupper($item['nivel']) }}</td>
                        <td>{{ $gruposDisponiveis[$item['grupo']] ?? $item['grupo'] }}</td>
                        <td>{{ $item['codigo'] }}</td>
                        <td><strong>{{ $item['titulo'] }}</strong></td>
                        <td>{{ $item['contexto'] }}</td>
                        <td>{{ $item['descricao'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Nenhum achado para os filtros exportados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="method">
        <div class="section-title">Metodologia</div>
        @foreach ($metodologia_linhas as $linha)
            <p><strong>{{ $linha[0] }}:</strong> {{ $linha[1] }}</p>
        @endforeach
    </div>
</body>
</html>
