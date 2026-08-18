@section('title', 'Política - Histórico dos Acompanhados')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Histórico dos Acompanhados</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Linha do tempo eleitoral e institucional dos quatro históricos especiais, baseada nos dados oficiais já importados.</p>
            </div>
            <div class="grid w-full gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.acompanhamento') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Acompanhamento</a>
                <a href="{{ route('politica.comparativo-territorial') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 dark:border-indigo-900 dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-950/70">Comparativo territorial</a>
                <a href="{{ route('politica.dados-oficiais') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Dados oficiais TSE</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-2xl bg-gray-950 p-5 text-white shadow-sm sm:p-6 dark:bg-black">
                <div class="grid gap-5 lg:grid-cols-[1.35fr_.65fr] lg:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Histórico oficial especial</p>
                        <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Histórico dos Acompanhados: eleições, votos, situação e mandatos</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-300">O recorte desta página é deliberadamente limitado a Márcio Marinho, Rogéria Santos, José de Arimateia e Jurailton Santos. Eleições e mandatos continuam separados: resultado eleitoral não é convertido automaticamente em posse.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-gray-300">
                        <p class="font-semibold text-white">Leitura da evolução</p>
                        <p class="mt-2 leading-6">A variação percentual só compara eleições consecutivas do <strong class="text-white">mesmo cargo</strong>. Barras de votos são escala visual dentro de cada político e não significam comparação direta entre cargos diferentes.</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Político</label>
                    <select wire:model.live="politico" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Todos os quatro</option>
                        @foreach ($opcoes['politicos'] as $opcao)
                            <option value="{{ $opcao['slug'] }}">{{ $opcao['nome'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cargo</label>
                    <select wire:model.live="cargo" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Todos os cargos</option>
                        @foreach ($opcoes['cargos'] as $opcaoCargo)
                            <option value="{{ $opcaoCargo }}">{{ $opcaoCargo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ano eleitoral</label>
                    <select wire:model.live="ano" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Todos os anos</option>
                        @foreach ($opcoes['anos'] as $opcaoAno)
                            <option value="{{ $opcaoAno }}">{{ $opcaoAno }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" wire:click="limparFiltros" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">Limpar filtros</button>
                </div>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @php
                    $metricCards = [
                        ['label' => 'Políticos', 'value' => $metricas['politicos'], 'hint' => 'No filtro atual'],
                        ['label' => 'Eleições', 'value' => $metricas['eleicoes'], 'hint' => 'Candidaturas oficiais'],
                        ['label' => 'Com resultado', 'value' => $metricas['com_resultado'], 'hint' => 'Votação/situação disponível'],
                        ['label' => 'Eleitos', 'value' => $metricas['eleitos'], 'hint' => 'Resultado oficial'],
                        ['label' => 'Mandatos/cargos', 'value' => $metricas['mandatos'], 'hint' => 'Fonte institucional'],
                    ];
                @endphp
                @foreach ($metricCards as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            <div class="space-y-6">
                @forelse ($cards as $pessoa)
                    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 bg-gray-50/80 p-4 sm:p-5 dark:border-gray-700 dark:bg-gray-900/40">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-lg font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                                        @if ($pessoa['foto_url'])
                                            <img src="{{ $pessoa['foto_url'] }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            {{ mb_strtoupper(mb_substr($pessoa['nome'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h2 class="truncate text-lg font-bold text-gray-950 dark:text-white">{{ $pessoa['nome'] }}</h2>
                                            @if ($pessoa['grupo'])
                                                <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">{{ $pessoa['grupo'] }}</span>
                                            @endif
                                        </div>
                                        @if ($pessoa['resumo']['mandato_atual'])
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                Em exercício: {{ $pessoa['resumo']['mandato_atual']['cargo'] }}
                                                @if ($pessoa['resumo']['mandato_atual']['partido'])
                                                    · {{ $pessoa['resumo']['mandato_atual']['partido'] }}
                                                @endif
                                                · {{ $pessoa['resumo']['mandato_atual']['periodo'] }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $pessoa['resumo']['eleicoes'] }} eleição(ões) no filtro atual.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap lg:justify-end">
                                    <div class="rounded-xl bg-white px-3 py-2 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                                        <p class="text-lg font-bold text-gray-950 dark:text-white">{{ $pessoa['resumo']['eleicoes'] }}</p>
                                        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">eleições</p>
                                    </div>
                                    <div class="rounded-xl bg-white px-3 py-2 text-center shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $pessoa['resumo']['eleitos'] }}</p>
                                        <p class="text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400">eleito</p>
                                    </div>
                                    <a href="{{ route('politica.politicos.show', $pessoa['slug']) }}" wire:navigate class="col-span-2 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 sm:col-auto">Abrir perfil →</a>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-0 xl:grid-cols-[1.55fr_.45fr]">
                            <div class="p-4 sm:p-5 xl:border-r xl:border-gray-200 dark:xl:border-gray-700">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Evolução eleitoral</p>
                                        <h3 class="mt-1 font-semibold text-gray-950 dark:text-white">Votos e situação por eleição</h3>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Δ compara apenas o mesmo cargo</p>
                                </div>

                                <div class="mt-4 space-y-3">
                                    @forelse ($pessoa['eleicoes'] as $eleicao)
                                        @php
                                            $situacaoUpper = mb_strtoupper((string) $eleicao['situacao']);
                                            $situacaoClass = $eleicao['eleito']
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'
                                                : (str_contains($situacaoUpper, 'NÃO ELEITO') || str_contains($situacaoUpper, 'NAO ELEITO') || str_contains($situacaoUpper, 'INDEFER')
                                                    ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300'
                                                    : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300');
                                        @endphp
                                        <article class="rounded-2xl border border-gray-200 p-4 dark:border-gray-700">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-lg font-bold text-gray-950 dark:text-white">{{ $eleicao['ano'] ?: '—' }}</span>
                                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $situacaoClass }}">{{ $eleicao['situacao'] }}</span>
                                                        @if ($eleicao['registro_oficial'])
                                                            <span class="rounded-full bg-indigo-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">TSE</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $eleicao['cargo'] }}
                                                        @if ($eleicao['partido'])
                                                            · {{ $eleicao['partido'] }}
                                                        @endif
                                                        @if ($eleicao['numero'])
                                                            · Nº {{ $eleicao['numero'] }}
                                                        @endif
                                                    </p>
                                                    @if ($eleicao['melhor_municipio'])
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                            Maior votação municipal:
                                                            <strong class="font-semibold text-gray-700 dark:text-gray-300">{{ $eleicao['melhor_municipio'] }}</strong>
                                                            · {{ number_format($eleicao['melhor_municipio_votos'], 0, ',', '.') }} votos
                                                            @if ($eleicao['melhor_municipio_percentual'] !== null)
                                                                · {{ number_format($eleicao['melhor_municipio_percentual'], 1, ',', '.') }}% do total
                                                            @endif
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="shrink-0 text-left sm:text-right">
                                                    @if ($eleicao['votos'] !== null)
                                                        <p class="text-xl font-bold text-gray-950 dark:text-white">{{ number_format($eleicao['votos'], 0, ',', '.') }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">votos</p>
                                                        @if ($eleicao['delta'] !== null)
                                                            <p class="mt-1 text-xs font-semibold {{ $eleicao['delta'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                                {{ $eleicao['delta'] >= 0 ? '+' : '' }}{{ number_format($eleicao['delta'], 0, ',', '.') }}
                                                                @if ($eleicao['delta_percentual'] !== null)
                                                                    ({{ $eleicao['delta_percentual'] >= 0 ? '+' : '' }}{{ number_format($eleicao['delta_percentual'], 1, ',', '.') }}%)
                                                                @endif
                                                                <span class="font-normal text-gray-400">vs. {{ $eleicao['delta_ano_base'] }}</span>
                                                            </p>
                                                        @endif
                                                    @else
                                                        <p class="font-semibold text-indigo-600 dark:text-indigo-400">Sem votos oficiais</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">registro/candidatura</p>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($eleicao['votos'] !== null)
                                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700" title="Escala relativa à maior votação deste político no filtro atual">
                                                    <div class="h-full rounded-full bg-indigo-500 dark:bg-indigo-400" style="width: {{ $eleicao['barra_percentual'] }}%"></div>
                                                </div>
                                            @endif
                                        </article>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhuma eleição encontrada para os filtros atuais.</div>
                                    @endforelse
                                </div>
                            </div>

                            <aside class="p-4 sm:p-5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Mandatos e cargos</p>
                                <h3 class="mt-1 font-semibold text-gray-950 dark:text-white">Fontes institucionais</h3>
                                <div class="mt-4 space-y-3">
                                    @forelse ($pessoa['mandatos'] as $mandato)
                                        <article class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $mandato['tipo'] === 'cargo_publico' ? 'cargo público' : 'mandato' }}</span>
                                                @if ($mandato['fonte_oficial'])
                                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">fonte oficial</span>
                                                @endif
                                            </div>
                                            <h4 class="mt-2 font-semibold text-gray-900 dark:text-white">
                                                {{ $mandato['cargo'] }}
                                                @if ($mandato['partido'])
                                                    · {{ $mandato['partido'] }}
                                                @endif
                                            </h4>
                                            <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $mandato['periodo'] }}</p>
                                            @if ($mandato['detalhes'])
                                                <p class="mt-2 text-xs leading-5 text-gray-600 dark:text-gray-300">{{ $mandato['detalhes'] }}</p>
                                            @endif
                                            <div class="mt-2 flex items-center justify-between gap-2 text-xs">
                                                <span class="text-gray-500 dark:text-gray-400">{{ $mandato['fonte'] }}</span>
                                                @if ($mandato['fonte_url'])
                                                    <a href="{{ $mandato['fonte_url'] }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Fonte ↗</a>
                                                @endif
                                            </div>
                                        </article>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhum mandato/cargo institucional no filtro atual.</div>
                                    @endforelse
                                </div>
                            </aside>
                        </div>
                    </section>
                @empty
                    <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-gray-800">
                        <p class="font-semibold text-gray-900 dark:text-white">Nenhum histórico encontrado</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Altere ou limpe os filtros para visualizar os acompanhados especiais.</p>
                    </section>
                @endforelse
            </div>
        </div>
    </div>
</div>
