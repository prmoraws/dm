@section('title', 'Política - Qualidade e Auditoria')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Integridade antes da análise</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Centro de Qualidade e Auditoria dos Dados</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verificações locais, rastreáveis e sem correção automática de resultados.</p>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.qualidade-dados.pdf', ['nivel' => $filtros['nivel'], 'grupo' => $filtros['grupo'], 'busca' => $filtros['busca']]) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Exportar PDF</a>
                <a href="{{ route('politica.qualidade-dados.excel', ['nivel' => $filtros['nivel'], 'grupo' => $filtros['grupo'], 'busca' => $filtros['busca']]) }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">Exportar Excel</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-5 px-3 sm:px-6 lg:px-8">
            @if (session('politica_qualidade_ok'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">{{ session('politica_qualidade_ok') }}</div>
            @endif

            <section class="overflow-hidden rounded-2xl bg-gray-950 p-5 text-white shadow-sm sm:p-6 dark:bg-black">
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Integridade antes da análise</p>
                        <h3 class="mt-2 text-2xl font-black sm:text-3xl">Auditoria automática da Política V2</h3>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-300">O centro compara totais, cobertura municipal, zonas, identidade, Espelho e integrações. Nenhum achado altera o banco automaticamente.</p>
                    </div>
                    @php
                        $statusLabel = match ($status) { 'critico' => 'Crítico', 'atencao' => 'Atenção', default => 'OK' };
                        $statusClass = match ($status) { 'critico' => 'bg-rose-500/20 text-rose-200 ring-rose-400/30', 'atencao' => 'bg-amber-500/20 text-amber-200 ring-amber-400/30', default => 'bg-emerald-500/20 text-emerald-200 ring-emerald-400/30' };
                    @endphp
                    <div class="text-left lg:text-right">
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Status geral</p>
                        <span class="mt-2 inline-flex rounded-full px-3 py-1.5 text-sm font-bold ring-1 {{ $statusClass }}">{{ $statusLabel }}</span>
                        <p class="mt-2 text-xs text-gray-400">Snapshot {{ $gerado_em->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                @php
                    $cards = [
                        ['label' => 'Críticos', 'value' => $contagens['critico'], 'hint' => 'Inconsistência objetiva', 'class' => 'text-rose-600 dark:text-rose-400'],
                        ['label' => 'Alertas', 'value' => $contagens['alerta'], 'hint' => 'Requer conferência', 'class' => 'text-amber-600 dark:text-amber-400'],
                        ['label' => 'Candidaturas', 'value' => $metricas['candidaturas'], 'hint' => 'Auditadas no snapshot', 'class' => 'text-gray-950 dark:text-white'],
                        ['label' => 'Municípios oficiais', 'value' => $metricas['municipios_oficiais'], 'hint' => 'Com código IBGE', 'class' => 'text-gray-950 dark:text-white'],
                        ['label' => 'Resultados municipais', 'value' => $metricas['resultados_municipais'], 'hint' => 'Linhas relacionais', 'class' => 'text-gray-950 dark:text-white'],
                        ['label' => 'Espelhos', 'value' => $metricas['espelhos_operacionais'], 'hint' => 'Registros operacionais', 'class' => 'text-gray-950 dark:text-white'],
                    ];
                @endphp
                @foreach ($cards as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-black {{ $card['class'] }}">{{ number_format($card['value'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_260px_1fr_auto] xl:items-end">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nível</label>
                        <select wire:model.live="nivel" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($niveisDisponiveis as $chave => $label)
                                <option value="{{ $chave }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Grupo</label>
                        <select wire:model.live="grupo" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($gruposDisponiveis as $chave => $label)
                                <option value="{{ $chave }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buscar achado/contexto</label>
                        <input wire:model.live.debounce.300ms="busca" type="search" placeholder="Ex.: Lula, Salvador, soma municipal, foto..." class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    </div>
                    <button wire:click="atualizarAuditoria" type="button" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 xl:w-auto">Recalcular auditoria</button>
                </div>
            </section>

            <section class="grid gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"><p class="text-xs text-gray-500 dark:text-gray-400">Achados no filtro</p><p class="mt-1 text-2xl font-black text-gray-950 dark:text-white">{{ $contagensFiltradas['total'] }}</p></div>
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/60 dark:bg-rose-950/20"><p class="text-xs text-rose-600 dark:text-rose-300">Críticos no filtro</p><p class="mt-1 text-2xl font-black text-rose-700 dark:text-rose-200">{{ $contagensFiltradas['critico'] }}</p></div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/20"><p class="text-xs text-amber-600 dark:text-amber-300">Alertas no filtro</p><p class="mt-1 text-2xl font-black text-amber-700 dark:text-amber-200">{{ $contagensFiltradas['alerta'] }}</p></div>
                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-900/60 dark:bg-sky-950/20"><p class="text-xs text-sky-600 dark:text-sky-300">Informativos no filtro</p><p class="mt-1 text-2xl font-black text-sky-700 dark:text-sky-200">{{ $contagensFiltradas['info'] }}</p></div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h3 class="font-bold text-gray-950 dark:text-white">Achados rastreáveis</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Crítico = inconsistência objetiva. Alerta = cobertura, atualização ou relacionamento que precisa ser conferido. Informativo = condição conhecida/preservada.</p>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($achadosFiltrados as $item)
                        @php
                            $badge = match ($item['nivel']) {
                                'critico' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
                                'alerta' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                                default => 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
                            };
                        @endphp
                        <article class="p-5">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $badge }}">{{ $item['nivel'] }}</span>
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ $gruposDisponiveis[$item['grupo']] ?? $item['grupo'] }}</span>
                                        <code class="rounded bg-gray-100 px-2 py-0.5 text-[10px] text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $item['codigo'] }}</code>
                                    </div>
                                    <h4 class="mt-2 font-bold text-gray-950 dark:text-white">{{ $item['titulo'] }}</h4>
                                    @if ($item['contexto'])
                                        <p class="mt-1 break-words text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item['contexto'] }}</p>
                                    @endif
                                    <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $item['descricao'] }}</p>
                                </div>
                                @if ($item['url'])
                                    <a href="{{ $item['url'] }}" wire:navigate class="inline-flex shrink-0 items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Abrir contexto →</a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum achado para os filtros atuais.</div>
                    @endforelse
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Resultado eleitoral</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Candidaturas c/ município</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['candidaturas_com_resultado_municipal'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Totais divergentes</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['candidaturas_total_municipal_divergente'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Zona × município divergente</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['contextos_zona_municipio_divergentes'] ?? 0, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Território e Espelho</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Sem código TSE</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['municipios_sem_tse_codigo'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Sem coordenadas</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['municipios_sem_coordenadas'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Espelhos revisão vencida</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['espelhos_revisao_vencida'] ?? 0, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Integrações e identidade</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Fontes com erro</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['fontes_com_erro'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Importações com erro</span><strong class="text-gray-950 dark:text-white">{{ number_format($metricas['importacoes_com_erro'] ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between gap-4"><span class="text-gray-500 dark:text-gray-400">Acompanhados sem foto</span><strong class="text-gray-950 dark:text-white">{{ number_format(($metricas['acompanhados_sem_foto'] ?? 0) + ($metricas['acompanhados_foto_local_quebrada'] ?? 0), 0, ',', '.') }}</strong></div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-900/60 dark:bg-indigo-950/25">
                <h3 class="font-bold text-indigo-950 dark:text-indigo-100">Metodologia e segurança</h3>
                <div class="mt-3 grid gap-3 text-sm leading-6 text-indigo-900/90 md:grid-cols-2 dark:text-indigo-200">
                    @foreach ($metodologia_linhas as $linha)
                        <p><strong>{{ $linha[0] }}:</strong> {{ $linha[1] }}</p>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</div>
