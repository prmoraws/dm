<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Painel Executivo de Prioridades</title>
    <style>
        @page { margin: 18px 22px 24px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 9px; line-height: 1.35; }
        h1, h2, h3, p { margin: 0; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 9px; margin-bottom: 10px; }
        .eyebrow { font-size: 8px; text-transform: uppercase; letter-spacing: .08em; color: #4f46e5; font-weight: bold; }
        .title { font-size: 18px; margin-top: 3px; }
        .meta { margin-top: 5px; color: #4b5563; }
        .notice { border: 1px solid #f59e0b; background: #fffbeb; padding: 7px 9px; margin: 9px 0; color: #78350f; }
        .kpis { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin: 0 -5px 10px; }
        .kpis td { width: 20%; border: 1px solid #d1d5db; padding: 7px; vertical-align: top; }
        .kpi-label { color: #6b7280; font-size: 7px; text-transform: uppercase; font-weight: bold; }
        .kpi-value { font-size: 16px; font-weight: bold; margin-top: 2px; }
        .section { margin-top: 12px; page-break-inside: auto; }
        .section-title { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .section-note { color: #6b7280; font-size: 7.5px; margin-bottom: 5px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 4px; text-align: left; font-size: 7px; }
        table.data td { border: 1px solid #e5e7eb; padding: 4px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .positive { color: #047857; font-weight: bold; }
        .negative { color: #be123c; font-weight: bold; }
        .muted { color: #6b7280; }
        .method { margin-top: 12px; border-top: 1px solid #d1d5db; padding-top: 7px; color: #4b5563; }
        .method p { margin-bottom: 3px; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; text-align: center; color: #9ca3af; font-size: 7px; }
    </style>
</head>
<body>
    <div class="footer">Política V2 · relatório descritivo gerado a partir da base local</div>

    <header class="header">
        <p class="eyebrow">Política V2 · Relatório executivo</p>
        <h1 class="title">Painel Executivo de Prioridades</h1>
        <p class="meta">Recorte: <strong>{{ $recorte_label }}</strong> · Gerado em {{ $gerado_em->format('d/m/Y H:i') }}</p>
    </header>

    <div class="notice">
        Este documento consolida resultados oficiais e sinais estatísticos já calculados. Não estima votos, não mede intenção de voto e não converte ausência de registro oficial em zero.
    </div>

    <table class="kpis">
        <tr>
            <td><div class="kpi-label">Com comparação</div><div class="kpi-value">{{ number_format($resumo['com_comparacao'], 0, ',', '.') }}</div></td>
            <td><div class="kpi-label">Municípios únicos</div><div class="kpi-value">{{ number_format($resumo['municipios_unicos'], 0, ',', '.') }}</div></td>
            <td><div class="kpi-label">Variações negativas</div><div class="kpi-value">{{ number_format($resumo['variacoes_negativas_relevantes'], 0, ',', '.') }}</div></td>
            <td><div class="kpi-label">Sinais positivos</div><div class="kpi-value">{{ number_format($resumo['sinais_positivos'], 0, ',', '.') }}</div></td>
            <td><div class="kpi-label">Pendências espelho</div><div class="kpi-value">{{ number_format($resumo['pendencias_espelho'], 0, ',', '.') }}</div></td>
        </tr>
    </table>

    <section class="section">
        <h2 class="section-title">Situação dos acompanhados</h2>
        <p class="section-note">Cada comparação usa os dois pleitos mais recentes com resultado municipal do mesmo cargo.</p>
        <table class="data">
            <thead>
                <tr>
                    <th>Acompanhado</th><th>Status</th><th>Cargo</th><th>Período</th>
                    <th class="num">Votos base</th><th class="num">Votos atual</th><th class="num">Δ votos</th>
                    <th class="num">Comparáveis</th><th class="num">Perda</th><th class="num">Fortalecimento</th><th>Cobertura</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cards as $card)
                    <tr>
                        <td><strong>{{ $card['nome'] }}</strong></td>
                        <td>{{ $card['status'] === 'disponivel' ? 'Comparável' : 'Aguardando' }}</td>
                        <td>{{ $card['cargo'] ?: '—' }}</td>
                        <td>
                            @if ($card['ano_base'])
                                {{ $card['ano_base'] }} → {{ $card['ano_comparada'] }}
                            @elseif ($card['ano_comparada'])
                                Último: {{ $card['ano_comparada'] }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="num">{{ $card['votos_base'] !== null ? number_format($card['votos_base'], 0, ',', '.') : '—' }}</td>
                        <td class="num">{{ $card['votos_comparada'] !== null ? number_format($card['votos_comparada'], 0, ',', '.') : '—' }}</td>
                        <td class="num {{ ($card['delta_total'] ?? 0) > 0 ? 'positive' : (($card['delta_total'] ?? 0) < 0 ? 'negative' : '') }}">
                            @if ($card['delta_total'] !== null)
                                {{ $card['delta_total'] > 0 ? '+' : '' }}{{ number_format($card['delta_total'], 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="num">{{ number_format($card['municipios_comparaveis'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($card['perda_relevante'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($card['fortalecimento'], 0, ',', '.') }}</td>
                        <td>{{ $card['cobertura_incompleta'] ? 'Desigual' : 'Sem alerta' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="center muted">Nenhum acompanhado disponível no recorte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Variações negativas de maior relevância estatística</h2>
        <p class="section-note">Ordenação descritiva pelo score interno de intensidade do recorte.</p>
        <table class="data">
            <thead><tr><th>Município</th><th>Acompanhado</th><th>Cargo / período</th><th>Sinal</th><th class="num">Votos base</th><th class="num">Votos atual</th><th class="num">Δ</th><th class="num">Relevância</th></tr></thead>
            <tbody>
                @forelse ($atencao as $item)
                    <tr>
                        <td><strong>{{ $item['nome'] }}</strong></td>
                        <td>{{ $item['politico_nome'] }}</td>
                        <td>{{ $item['cargo'] }} · {{ $item['ano_base'] }} → {{ $item['ano_comparada'] }}</td>
                        <td>{{ $item['sinal_label'] }}</td>
                        <td class="num">{{ number_format($item['votos_base'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($item['votos_comparada'], 0, ',', '.') }}</td>
                        <td class="num negative">{{ number_format($item['delta'], 0, ',', '.') }}</td>
                        <td class="num">{{ $item['relevancia_score'] }}/100</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="center muted">Nenhuma variação negativa relevante no recorte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Sinais positivos recentes</h2>
        <table class="data">
            <thead><tr><th>Município</th><th>Acompanhado</th><th>Cargo / período</th><th>Sinal</th><th class="num">Votos base</th><th class="num">Votos atual</th><th class="num">Δ</th><th class="num">Relevância</th></tr></thead>
            <tbody>
                @forelse ($positivos as $item)
                    <tr>
                        <td><strong>{{ $item['nome'] }}</strong></td>
                        <td>{{ $item['politico_nome'] }}</td>
                        <td>{{ $item['cargo'] }} · {{ $item['ano_base'] }} → {{ $item['ano_comparada'] }}</td>
                        <td>{{ $item['sinal_label'] }}</td>
                        <td class="num">{{ number_format($item['votos_base'], 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($item['votos_comparada'], 0, ',', '.') }}</td>
                        <td class="num {{ $item['delta'] >= 0 ? 'positive' : '' }}">{{ $item['delta'] > 0 ? '+' : '' }}{{ number_format($item['delta'], 0, ',', '.') }}</td>
                        <td class="num">{{ $item['relevancia_score'] }}/100</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="center muted">Nenhum sinal positivo forte no recorte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Fila de revisão do espelho operacional</h2>
        <table class="data">
            <thead><tr><th>Município</th><th>IBGE</th><th>Situação</th><th class="num">Relevância</th><th class="num">Contextos</th><th>Acompanhados</th></tr></thead>
            <tbody>
                @forelse ($pendenciasEspelho as $item)
                    <tr>
                        <td><strong>{{ $item['nome'] }}</strong></td>
                        <td>{{ $item['ibge_code'] ?: '—' }}</td>
                        <td>{{ $item['status'] === 'sem_registro' ? 'Sem registro operacional' : 'Revisão pendente' }}</td>
                        <td class="num">{{ $item['relevancia_score'] }}/100</td>
                        <td class="num">{{ $item['contextos'] }}</td>
                        <td>{{ implode(', ', $item['politicos']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center muted">Nenhuma pendência operacional associada a sinal forte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="method">
        <h3 class="section-title">Metodologia e rastreabilidade</h3>
        @foreach ($metodologia_linhas as $linha)
            <p><strong>{{ $linha[0] }}:</strong> {{ $linha[1] }}</p>
        @endforeach
    </section>
</body>
</html>
