<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Espelho Inteligente - {{ $cidade['nome'] }} - {{ $candidato['nome'] }}</title>
    <style>
        @page { margin: 24px 28px 30px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 9px; line-height: 1.4; }
        h1, h2, h3, p { margin: 0; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 12px; }
        .eyebrow { color: #4f46e5; text-transform: uppercase; font-size: 8px; font-weight: bold; letter-spacing: .08em; }
        .title { font-size: 19px; margin-top: 3px; }
        .subtitle { color: #4b5563; margin-top: 4px; }
        .candidate { margin-top: 10px; border: 1px solid #d1d5db; padding: 9px; }
        .candidate-name { font-size: 15px; font-weight: bold; }
        .meta { color: #4b5563; margin-top: 3px; }
        .kpis { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin: 10px -5px 0; }
        .kpis td { width: 25%; border: 1px solid #d1d5db; padding: 7px; vertical-align: top; }
        .label { color: #6b7280; font-size: 7px; text-transform: uppercase; font-weight: bold; }
        .value { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .section { margin-top: 13px; page-break-inside: auto; }
        .section-title { font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .note { color: #6b7280; font-size: 7.5px; margin-bottom: 5px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 4px; text-align: left; font-size: 7px; }
        table.data td { border: 1px solid #e5e7eb; padding: 4px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .selected { font-weight: bold; background: #eef2ff; }
        .positive { color: #047857; font-weight: bold; }
        .negative { color: #be123c; font-weight: bold; }
        .muted { color: #6b7280; }
        .notice { border: 1px solid #f59e0b; background: #fffbeb; color: #78350f; padding: 7px 9px; margin-top: 8px; }
        .method { margin-top: 14px; border-top: 1px solid #d1d5db; padding-top: 8px; }
    </style>
</head>
<body>
    <header class="header">
        <div class="eyebrow">Política V2 · Espelho Inteligente</div>
        <h1 class="title">{{ $cidade['nome'] }}</h1>
        <p class="subtitle">Relatório territorial, eleitoral e operacional do candidato selecionado · gerado em {{ $gerado_em->format('d/m/Y H:i') }}</p>

        <div class="candidate">
            <div class="candidate-name">{{ $candidato['nome'] }}</div>
            <div class="meta">
                {{ $candidato['cargo'] ?: 'Cargo não informado' }} · {{ $candidato['partido'] ?: 'Sem partido' }} · nº {{ $candidato['numero'] ?: '—' }} ·
                {{ $candidato['ano'] ?: '—' }} · {{ $candidato['turno'] ?: '—' }}º turno
            </div>
            <div class="meta">
                {{ $favorito['classificacao'] ?? 'Selecionado para este relatório' }}
                @if(isset($favorito['prioridade']))
                    · prioridade interna {{ $favorito['prioridade'] }}
                @endif
            </div>
        </div>

        <table class="kpis">
            <tr>
                <td><div class="label">Votos no município</div><div class="value">{{ $candidato['votos_municipio'] !== null ? number_format($candidato['votos_municipio'], 0, ',', '.') : 'Sem linha oficial' }}</div></td>
                <td><div class="label">% oficial local</div><div class="value">{{ $candidato['percentual_municipio'] !== null ? number_format($candidato['percentual_municipio'], 2, ',', '.') . '%' : '—' }}</div></td>
                <td><div class="label">Posição no município</div><div class="value">{{ $candidato['posicao_municipio'] ?: '—' }}</div></td>
                <td><div class="label">Peso nos votos do candidato</div><div class="value">{{ $candidato['participacao_nos_votos_do_candidato'] !== null ? number_format($candidato['participacao_nos_votos_do_candidato'], 2, ',', '.') . '%' : '—' }}</div></td>
            </tr>
        </table>
    </header>

    @if(!$auditoria['tem_resultado_municipal'])
        <div class="notice"><strong>Atenção:</strong> não há linha oficial de resultado municipal para este candidato neste pleito. O relatório mantém o campo sem dado; não converte a ausência em zero.</div>
    @endif

    <section class="section">
        <h2 class="section-title">Contexto do município e espelho operacional</h2>
        <table class="data">
            <tbody>
                <tr><th>IBGE</th><td>{{ $cidade['ibge_code'] ?: '—' }}</td><th>População</th><td class="num">{{ $cidade['populacao'] !== null ? number_format($cidade['populacao'], 0, ',', '.') : '—' }}</td></tr>
                <tr><th>Cadeiras da Câmara</th><td>{{ $cidade['cadeiras_camara'] ?: '—' }}</td><th>Coordenadas</th><td>{{ $cidade['latitude'] !== null && $cidade['longitude'] !== null ? $cidade['latitude'].', '.$cidade['longitude'] : '—' }}</td></tr>
                <tr><th>Presidente local</th><td>{{ $operacional['presidente_local'] ?? 'Não informado' }}</td><th>Indicação</th><td>{{ $operacional['indicacao_bispo'] ?? 'Não informada' }}</td></tr>
                <tr><th>Filiados Republicanos</th><td>{{ isset($operacional['filiados_republicanos']) ? number_format((int) $operacional['filiados_republicanos'], 0, ',', '.') : '—' }}</td><th>Revisado em</th><td>{{ isset($operacional['revisado_em']) && $operacional['revisado_em'] ? $operacional['revisado_em']->format('d/m/Y H:i') : '—' }}</td></tr>
                <tr><th>Observações</th><td colspan="3">{{ $operacional['observacoes'] ?? 'Sem observações cadastradas.' }}</td></tr>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Favorito, metas e observações internas</h2>
        <table class="data">
            <tbody>
                @if($favorito)
                    <tr><th>Classificação</th><td>{{ $favorito['classificacao'] ?: '—' }}</td><th>Prioridade interna</th><td>{{ $favorito['prioridade'] ?? '—' }}</td></tr>
                    <tr><th>Meta de votos</th><td class="num">{{ $favorito['meta_votos'] !== null ? number_format($favorito['meta_votos'], 0, ',', '.') : '—' }}</td><th>Meta percentual</th><td>{{ $favorito['meta_percentual'] !== null ? number_format($favorito['meta_percentual'], 2, ',', '.') . '%' : '—' }}</td></tr>
                    <tr><th>Observações</th><td colspan="3">{{ $favorito['observacoes'] ?: 'Sem observações específicas para este favorito.' }}</td></tr>
                @else
                    <tr><td colspan="4" class="center muted">O candidato foi selecionado para este relatório, mas ainda não foi marcado como favorito persistente neste espelho.</td></tr>
                @endif
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Desempenho e rastreabilidade da candidatura</h2>
        <table class="data">
            <tbody>
                <tr><th>Votos totais da candidatura</th><td class="num">{{ number_format($candidato['votos_total'], 0, ',', '.') }}</td><th>Situação eleitoral</th><td>{{ $candidato['situacao_eleicao'] ?: '—' }}</td></tr>
                <tr><th>Situação do registro</th><td>{{ $candidato['situacao_registro'] }}</td><th>Origem</th><td>{{ $candidato['origem'] ?: '—' }}</td></tr>
                <tr><th>SQ candidato TSE</th><td>{{ $candidato['tse_sq_candidato'] ?: '—' }}</td><th>Sincronizado em</th><td>{{ $candidato['sincronizado_em'] ? $candidato['sincronizado_em']->format('d/m/Y H:i') : '—' }}</td></tr>
                <tr><th>Eleitores</th><td class="num">{{ $candidato['eleitores'] !== null ? number_format($candidato['eleitores'], 0, ',', '.') : '—' }}</td><th>Comparecimento</th><td class="num">{{ $candidato['comparecimento'] !== null ? number_format($candidato['comparecimento'], 0, ',', '.') : '—' }}</td></tr>
                <tr><th>Abstenções</th><td class="num">{{ $candidato['abstencoes'] !== null ? number_format($candidato['abstencoes'], 0, ',', '.') : '—' }}</td><th>Seções totalizadas</th><td>{{ $candidato['secoes_totalizadas'] !== null ? number_format($candidato['secoes_totalizadas'], 0, ',', '.') : '—' }} / {{ $candidato['secoes_total'] !== null ? number_format($candidato['secoes_total'], 0, ',', '.') : '—' }}</td></tr>
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Histórico do mesmo candidato e cargo neste município</h2>
        <p class="note">Ausência de linha oficial permanece como ausência. Deltas só são calculados quando há votos nos dois pontos comparados.</p>
        <table class="data">
            <thead><tr><th>Ano</th><th>Partido</th><th>Nº</th><th class="num">Votos</th><th class="num">% oficial</th><th class="num">Posição</th><th class="num">Δ votos</th><th class="num">Δ %</th><th>Situação</th><th>Cobertura</th></tr></thead>
            <tbody>
                @forelse($historico as $item)
                    <tr class="{{ $item['atual'] ? 'selected' : '' }}">
                        <td>{{ $item['ano'] ?: '—' }}</td><td>{{ $item['partido'] ?: '—' }}</td><td>{{ $item['numero'] ?: '—' }}</td>
                        <td class="num">{{ $item['votos'] !== null ? number_format($item['votos'], 0, ',', '.') : '—' }}</td>
                        <td class="num">{{ $item['percentual'] !== null ? number_format($item['percentual'], 2, ',', '.') . '%' : '—' }}</td>
                        <td class="num">{{ $item['posicao'] ?: '—' }}</td>
                        <td class="num {{ ($item['delta'] ?? 0) > 0 ? 'positive' : (($item['delta'] ?? 0) < 0 ? 'negative' : '') }}">{{ $item['delta'] !== null ? (($item['delta'] > 0 ? '+' : '').number_format($item['delta'], 0, ',', '.')) : '—' }}</td>
                        <td class="num">{{ $item['delta_percentual'] !== null ? (($item['delta_percentual'] > 0 ? '+' : '').number_format($item['delta_percentual'], 2, ',', '.').'%') : '—' }}</td>
                        <td>{{ $item['situacao_eleicao'] ?: '—' }}</td><td>{{ $item['tem_linha_municipal'] ? 'Com linha' : 'Sem linha oficial' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="center muted">Sem histórico do mesmo cargo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Distribuição por zona eleitoral</h2>
        <table class="data">
            <thead><tr><th>Zona</th><th>Código TSE</th><th class="num">Votos</th><th class="num">% oficial</th><th class="num">Seções</th><th class="num">Totalizadas</th></tr></thead>
            <tbody>
                @forelse($zonas as $item)
                    <tr><td>{{ $item['zona'] ?: '—' }}</td><td>{{ $item['tse_codigo'] ?: '—' }}</td><td class="num">{{ number_format($item['votos'], 0, ',', '.') }}</td><td class="num">{{ $item['percentual'] !== null ? number_format($item['percentual'], 2, ',', '.') . '%' : '—' }}</td><td class="num">{{ $item['secoes_total'] ?? '—' }}</td><td class="num">{{ $item['secoes_totalizadas'] ?? '—' }}</td></tr>
                @empty
                    <tr><td colspan="6" class="center muted">Não há detalhamento por zona para este recorte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Ranking do mesmo cargo e eleição no município</h2>
        <p class="note">Top 20 do recorte; o candidato deste relatório é incluído e destacado mesmo se estiver fora do Top 20.</p>
        <table class="data">
            <thead><tr><th>#</th><th>Candidato</th><th>Partido</th><th>Nº</th><th class="num">Votos</th><th class="num">% oficial</th></tr></thead>
            <tbody>
                @forelse($ranking as $item)
                    <tr class="{{ $item['favorito'] ? 'selected' : '' }}"><td>{{ $item['posicao_lista'] }}</td><td>{{ $item['nome'] }}{{ $item['favorito'] ? ' ★' : '' }}</td><td>{{ $item['partido'] ?: '—' }}</td><td>{{ $item['numero'] ?: '—' }}</td><td class="num">{{ $item['votos'] !== null ? number_format($item['votos'], 0, ',', '.') : '—' }}</td><td class="num">{{ $item['percentual'] !== null ? number_format($item['percentual'], 2, ',', '.') . '%' : '—' }}</td></tr>
                @empty
                    <tr><td colspan="6" class="center muted">Sem ranking disponível.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">Auditoria de integridade do recorte</h2>
        <table class="data">
            <tbody>
                @foreach($auditoria_linhas as $linha)
                    <tr><th>{{ $linha[0] }}</th><td>{{ $linha[1] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="method">
        <h2 class="section-title">Metodologia e rastreabilidade</h2>
        @foreach($metodologia_linhas as $linha)
            <p><strong>{{ $linha[0] }}:</strong> {{ $linha[1] }}</p>
        @endforeach
    </section>
</body>
</html>
