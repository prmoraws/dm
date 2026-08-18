@section('title', 'Política - Painel Executivo')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Síntese dos acompanhados históricos</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Painel Executivo de Prioridades</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consolidação descritiva de resultados oficiais, sinais territoriais e qualidade do espelho operacional.</p>
            </div>
            <div class="grid w-full gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.painel-executivo.pdf', ['politico' => $politico]) }}" class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">Exportar PDF</a>
                <a href="{{ route('politica.painel-executivo.excel', ['politico' => $politico]) }}" class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">Exportar Excel</a>
                <a href="{{ route('politica.inteligencia-territorial') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Inteligência territorial</a>
                <a href="{{ route('politica.dados-oficiais') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Dados oficiais</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-2xl bg-gray-950 p-5 text-white shadow-sm sm:p-6 dark:bg-black">
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Resumo executivo</p>
                        <h3 class="mt-2 text-2xl font-black sm:text-3xl">Prioridades de análise territorial e revisão de dados</h3>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-300">O painel ordena variações estatísticas já calculadas nas etapas anteriores. Ele não estima votos, não mede intenção de voto e não transforma ausência de dado oficial em zero.</p>
                    </div>
                    <div class="w-full lg:w-72">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-400">Acompanhado</label>
                        <select wire:model.live="politico" class="mt-1.5 w-full rounded-xl border-gray-700 bg-gray-900 text-sm text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="todos">Todos os 4 históricos</option>
                            @foreach ($opcoesPoliticos as $opcao)
                                <option value="{{ $opcao['slug'] }}">{{ $opcao['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @php
                    $metricCards = [
                        ['label' => 'Com comparação', 'value' => $resumo['com_comparacao'], 'hint' => 'Mesmo cargo · 2+ pleitos'],
                        ['label' => 'Municípios únicos', 'value' => $resumo['municipios_unicos'], 'hint' => 'Com dado nos dois pleitos'],
                        ['label' => 'Variações negativas', 'value' => $resumo['variacoes_negativas_relevantes'], 'hint' => 'Contextos estatisticamente relevantes'],
                        ['label' => 'Sinais positivos', 'value' => $resumo['sinais_positivos'], 'hint' => 'Recuperação, força ou concentração'],
                        ['label' => 'Pendências espelho', 'value' => $resumo['pendencias_espelho'], 'hint' => 'Revisão operacional necessária'],
                    ];
                @endphp
                @foreach ($metricCards as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-black text-gray-950 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            @if ($resumo['alertas_cobertura'] > 0 || $resumo['aguardando_comparacao'] > 0)
                <section class="grid gap-3 lg:grid-cols-2">
                    @if ($resumo['alertas_cobertura'] > 0)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                            <strong>{{ $resumo['alertas_cobertura'] }} comparação(ões) com cobertura histórica desigual.</strong>
                            <p class="mt-1 leading-6">Municípios sem linha em um dos pleitos permanecem fora dos deltas e das classificações automáticas.</p>
                        </div>
                    @endif
                    @if ($resumo['aguardando_comparacao'] > 0)
                        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-sky-200">
                            <strong>{{ $resumo['aguardando_comparacao'] }} acompanhado(s) ainda sem dois pleitos comparáveis do mesmo cargo.</strong>
                            <p class="mt-1 leading-6">O painel exibe o último resultado disponível, mas não produz variação territorial artificial.</p>
                        </div>
                    @endif
                </section>
            @endif

            <section>
                <div class="mb-3 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Situação dos acompanhados</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">O par padrão usa os dois pleitos mais recentes com resultado municipal do mesmo cargo.</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($resumo['contextos_analisados'], 0, ',', '.') }} contexto(s) político × município analisado(s)</p>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    @forelse ($cards as $card)
                        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-indigo-100 text-lg font-black text-indigo-700 ring-1 ring-black/5 dark:bg-indigo-950/60 dark:text-indigo-300 dark:ring-white/10">
                                        @if ($card['foto_url'])
                                            <img src="{{ $card['foto_url'] }}" alt="Foto oficial de {{ $card['nome'] }}" class="h-full w-full object-cover">
                                        @else
                                            {{ mb_strtoupper(mb_substr($card['nome'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="truncate text-lg font-bold text-gray-950 dark:text-white">{{ $card['nome'] }}</h4>
                                        @if ($card['status'] === 'disponivel')
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $card['cargo'] }} · {{ $card['ano_base'] }} → {{ $card['ano_comparada'] }}</p>
                                        @else
                                            <p class="mt-1 text-sm font-semibold text-sky-700 dark:text-sky-300">Aguardando segundo pleito comparável do mesmo cargo</p>
                                            @if ($card['ano_comparada'])
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Último resultado municipal: {{ $card['cargo'] ?: 'cargo não informado' }} · {{ $card['ano_comparada'] }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ $card['perfil_url'] }}" wire:navigate class="shrink-0 text-sm font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Perfil →</a>
                            </div>

                            @if ($card['status'] === 'disponivel')
                                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Votos atuais</p>
                                        <p class="mt-1 font-black text-gray-950 dark:text-white">{{ number_format($card['votos_comparada'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Δ total</p>
                                        <p class="mt-1 font-black {{ $card['delta_total'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($card['delta_total'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-300') }}">
                                            {{ $card['delta_total'] > 0 ? '+' : '' }}{{ number_format($card['delta_total'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Comparáveis</p>
                                        <p class="mt-1 font-black text-gray-950 dark:text-white">{{ number_format($card['municipios_comparaveis'], 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Espelhos revisados</p>
                                        <p class="mt-1 font-black text-gray-950 dark:text-white">{{ number_format($card['espelho_revisado'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                    <span class="rounded-full bg-rose-50 px-2.5 py-1 font-semibold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">Perda relevante {{ $card['perda_relevante'] }}</span>
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Recuperação possível {{ $card['oportunidade_recuperacao'] }}</span>
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Fortalecimento {{ $card['fortalecimento'] }}</span>
                                    <span class="rounded-full bg-sky-50 px-2.5 py-1 font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">Recuperação {{ $card['recuperacao'] }}</span>
                                    @if ($card['cobertura_incompleta'])
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">Cobertura desigual</span>
                                    @endif
                                </div>

                                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                    <a href="{{ $card['inteligencia_url'] }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">Abrir inteligência territorial</a>
                                    <a href="{{ $card['comparativo_url'] }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Abrir comparativo</a>
                                </div>
                            @else
                                <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">
                                    @if ($card['votos_comparada'] !== null)
                                        Último resultado armazenado: <strong>{{ number_format($card['votos_comparada'], 0, ',', '.') }} votos</strong>. Sem uma segunda eleição do mesmo cargo, o painel não calcula crescimento, queda ou prioridade territorial.
                                    @else
                                        Ainda não há resultado municipal suficiente para compor a síntese executiva.
                                    @endif
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Nenhum acompanhado disponível para o filtro.</div>
                    @endforelse
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-2">
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h3 class="font-bold text-gray-950 dark:text-white">Variações negativas de maior relevância estatística</h3>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Ordenação descritiva pelo score da Etapa 9; não representa previsão nem recomendação de abordagem política.</p>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($atencao as $item)
                            <a href="{{ $item['inteligencia_url'] }}" wire:navigate class="block px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-gray-950 dark:text-white">{{ $item['nome'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item['politico_nome'] }} · {{ $item['cargo'] }} · {{ $item['ano_base'] }} → {{ $item['ano_comparada'] }}</p>
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $item['sinal_explicacao'] }}</p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">{{ $item['relevancia_score'] }}/100</span>
                                        <p class="mt-2 text-sm font-black text-rose-600 dark:text-rose-400">{{ number_format($item['delta'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma variação negativa relevante no recorte atual.</div>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h3 class="font-bold text-gray-950 dark:text-white">Sinais positivos recentes de maior relevância</h3>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Recuperação, fortalecimento ou concentração detectados pelas regras transparentes da Etapa 9.</p>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($positivos as $item)
                            <a href="{{ $item['inteligencia_url'] }}" wire:navigate class="block px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-gray-950 dark:text-white">{{ $item['nome'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item['politico_nome'] }} · {{ $item['cargo'] }} · {{ $item['ano_base'] }} → {{ $item['ano_comparada'] }}</p>
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $item['sinal_explicacao'] }}</p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $item['relevancia_score'] }}/100</span>
                                        <p class="mt-2 text-sm font-black {{ $item['delta'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ $item['delta'] > 0 ? '+' : '' }}{{ number_format($item['delta'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum sinal positivo forte no recorte atual.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                    <div>
                        <h3 class="font-bold text-gray-950 dark:text-white">Fila de revisão do espelho operacional</h3>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Municípios com algum sinal estatístico forte e espelho ainda não revisado. Esta fila prioriza qualidade e atualização do dado interno.</p>
                    </div>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">{{ number_format($resumo['pendencias_espelho'], 0, ',', '.') }} pendência(s)</span>
                </div>

                <div class="grid gap-px bg-gray-100 sm:grid-cols-2 xl:grid-cols-3 dark:bg-gray-700">
                    @forelse ($pendenciasEspelho as $item)
                        <article class="bg-white p-5 dark:bg-gray-800">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="truncate font-bold text-gray-950 dark:text-white">{{ $item['nome'] }}</h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item['contextos'] }} contexto(s) · relevância máx. {{ $item['relevancia_score'] }}/100</p>
                                </div>
                                @if ($item['status'] === 'sem_registro')
                                    <span class="shrink-0 rounded-full bg-gray-100 px-2 py-1 text-[10px] font-bold uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">sem registro</span>
                                @else
                                    <span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold uppercase text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">revisão pendente</span>
                                @endif
                            </div>
                            <p class="mt-3 text-xs leading-5 text-gray-600 dark:text-gray-300">Contextos: {{ implode(', ', $item['politicos']) }}</p>
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ $item['espelho_url'] }}" wire:navigate class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-2 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Ver espelho</a>
                                <a href="{{ $item['espelho_editar_url'] }}" wire:navigate class="inline-flex items-center justify-center rounded-lg bg-gray-950 px-2 py-2 text-xs font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Revisar dados</a>
                            </div>
                        </article>
                    @empty
                        <div class="bg-white p-8 text-center text-sm text-gray-500 sm:col-span-2 xl:col-span-3 dark:bg-gray-800 dark:text-gray-400">Nenhuma pendência operacional associada a sinal forte no recorte atual.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-900/60 dark:bg-indigo-950/25">
                <h3 class="font-bold text-indigo-950 dark:text-indigo-100">Critério executivo e rastreabilidade</h3>
                <div class="mt-3 grid gap-3 text-sm leading-6 text-indigo-900/90 md:grid-cols-2 dark:text-indigo-200">
                    <p><strong>Comparação:</strong> somente eleições do mesmo político e do mesmo cargo, usando os dois pleitos mais recentes com resultado municipal disponível.</p>
                    <p><strong>Prioridade de análise:</strong> ordena a intensidade estatística já calculada; não é probabilidade de vitória, previsão eleitoral ou orientação de persuasão.</p>
                    <p><strong>Cobertura histórica:</strong> ausência de linha oficial em um município não vira zero. O município sai do delta até existir dado comparável.</p>
                    <p><strong>Espelho operacional:</strong> a fila é uma pendência de qualidade de dados internos e nunca altera automaticamente resultados oficiais.</p>
                </div>
                <p class="mt-3 text-xs text-indigo-800/80 dark:text-indigo-300">Gerado em {{ $gerado_em->format('d/m/Y H:i') }} a partir do estado atual da base local.</p>
            </section>
        </div>
    </div>
</div>
