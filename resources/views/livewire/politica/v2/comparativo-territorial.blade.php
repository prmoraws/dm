@section('title', 'Política - Comparativo Territorial')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Histórico territorial oficial</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Comparativo Territorial dos Acompanhados</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Compare duas eleições do mesmo cargo, município por município, sem transformar ausência de linha oficial em zero.</p>
            </div>
            <div class="grid w-full gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.historico-acompanhados') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Histórico dos 4</a>
                <a href="{{ route('politica.inteligencia-territorial') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300">Inteligência</a>
                <a href="{{ route('politica.mapa') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Mapa eleitoral</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-5 px-3 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Político</label>
                        <select wire:model.live="politico" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @forelse ($opcoes['politicos'] as $item)
                                <option value="{{ $item['slug'] }}">{{ $item['nome'] }}</option>
                            @empty
                                <option value="">Sem histórico comparável</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cargo</label>
                        <select wire:model.live="cargo" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @forelse ($opcoes['cargos'] as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @empty
                                <option value="">Sem cargo comparável</option>
                            @endforelse
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Eleição base</label>
                        <select wire:model.live="anoBase" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($opcoes['anos'] as $anoOpcao)
                                <option value="{{ $anoOpcao }}">{{ $anoOpcao }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Eleição comparada</label>
                        <select wire:model.live="anoComparada" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($opcoes['anos'] as $anoOpcao)
                                <option value="{{ $anoOpcao }}">{{ $anoOpcao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            @if (! $disponivel)
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Ainda não há duas eleições municipais comparáveis para este recorte.</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">O comparativo só é habilitado quando o mesmo político possui resultados municipais oficiais em pelo menos dois pleitos do mesmo cargo.</p>
                </section>
            @else
                <section class="overflow-hidden rounded-2xl bg-gray-950 p-5 text-white shadow-sm sm:p-6 dark:bg-black">
                    <div class="grid gap-5 lg:grid-cols-[1fr_auto_1fr] lg:items-center">
                        <div class="flex min-w-0 items-center gap-4">
                            @if ($politicoResumo['foto_url'])
                                <img src="{{ $politicoResumo['foto_url'] }}" alt="Foto oficial de {{ $politicoResumo['nome'] }}" class="h-16 w-16 shrink-0 rounded-2xl object-cover ring-1 ring-white/15 sm:h-20 sm:w-20">
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">{{ $politicoResumo['nome'] }}</p>
                                <h3 class="mt-1 text-2xl font-bold">{{ $base['ano'] }} · {{ $base['partido'] ?: '—' }}</h3>
                                <p class="mt-1 text-sm text-gray-300">{{ $base['cargo'] }} · Nº {{ $base['numero'] ?: '—' }}</p>
                                <p class="mt-3 text-3xl font-black">{{ number_format($base['votos'], 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">votos oficiais · {{ $base['municipios'] }} municípios com linha no arquivo</p>
                            </div>
                        </div>

                        <div class="hidden h-16 w-px bg-white/15 lg:block"></div>

                        <div class="lg:text-right">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">Comparada</p>
                            <h3 class="mt-1 text-2xl font-bold">{{ $comparada['ano'] }} · {{ $comparada['partido'] ?: '—' }}</h3>
                            <p class="mt-1 text-sm text-gray-300">{{ $comparada['cargo'] }} · Nº {{ $comparada['numero'] ?: '—' }}</p>
                            <p class="mt-3 text-3xl font-black">{{ number_format($comparada['votos'], 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">votos oficiais · {{ $comparada['municipios'] }} municípios com linha no arquivo</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Variação total</p>
                            <p class="mt-1 text-xl font-bold {{ $metricas['delta_total'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                {{ $metricas['delta_total'] >= 0 ? '+' : '' }}{{ number_format($metricas['delta_total'], 0, ',', '.') }} votos
                            </p>
                            @if ($metricas['delta_total_percentual'] !== null)
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ $metricas['delta_total_percentual'] >= 0 ? '+' : '' }}{{ number_format($metricas['delta_total_percentual'], 1, ',', '.') }}% sobre a eleição base
                                </p>
                            @endif
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Municípios comparáveis</p>
                            <p class="mt-1 text-xl font-bold">{{ number_format($metricas['comparaveis'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-gray-400">com linha oficial nas duas eleições</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-xs uppercase tracking-wider text-gray-400">Saldo nos comparáveis</p>
                            <p class="mt-1 text-xl font-bold {{ $metricas['saldo_comparavel'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                {{ $metricas['saldo_comparavel'] >= 0 ? '+' : '' }}{{ number_format($metricas['saldo_comparavel'], 0, ',', '.') }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400">não inclui municípios sem par oficial</p>
                        </div>
                    </div>
                </section>

                @if ($cobertura_incompleta)
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-950 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/20 dark:text-amber-100">
                        <p class="font-semibold">Cobertura territorial diferente entre os pleitos</p>
                        <p class="mt-1 leading-6">
                            {{ $metricas['somente_base'] }} município(s) possuem linha somente em {{ $base['ano'] }} e {{ $metricas['somente_comparada'] }} somente em {{ $comparada['ano'] }}.
                            O sistema <strong>não transforma ausência de registro em zero voto</strong>; esses municípios ficam como “sem comparação”.
                        </p>
                    </section>
                @endif

                <section class="grid gap-3 sm:grid-cols-3 xl:grid-cols-6">
                    @php
                        $cards = [
                            ['label' => 'Cresceram', 'value' => $metricas['crescimento'], 'class' => 'text-emerald-600 dark:text-emerald-400'],
                            ['label' => 'Caíram', 'value' => $metricas['queda'], 'class' => 'text-rose-600 dark:text-rose-400'],
                            ['label' => 'Estáveis', 'value' => $metricas['estaveis'], 'class' => 'text-gray-900 dark:text-white'],
                            ['label' => 'Só na base', 'value' => $metricas['somente_base'], 'class' => 'text-amber-600 dark:text-amber-400'],
                            ['label' => 'Só na comparada', 'value' => $metricas['somente_comparada'], 'class' => 'text-amber-600 dark:text-amber-400'],
                            ['label' => 'No mapa', 'value' => $metricas['mapa'], 'class' => 'text-indigo-600 dark:text-indigo-400'],
                        ];
                    @endphp
                    @foreach ($cards as $card)
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <p class="text-2xl font-black {{ $card['class'] }}">{{ number_format($card['value'], 0, ',', '.') }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        </div>
                    @endforeach
                </section>

                <section class="grid gap-4 xl:grid-cols-2">
                    <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm dark:border-emerald-900/60 dark:bg-gray-800">
                        <div class="border-b border-emerald-100 bg-emerald-50 px-4 py-3 dark:border-emerald-900/50 dark:bg-emerald-950/20">
                            <h3 class="font-semibold text-emerald-900 dark:text-emerald-200">Maiores ganhos municipais</h3>
                            <p class="mt-1 text-xs text-emerald-700/80 dark:text-emerald-300/70">Somente municípios com registro oficial nas duas eleições.</p>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($ganhos as $item)
                                <div class="flex items-center justify-between gap-3 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $item['nome'] }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ number_format($item['votos_base'], 0, ',', '.') }} → {{ number_format($item['votos_comparada'], 0, ',', '.') }}</p>
                                    </div>
                                    <p class="shrink-0 font-bold text-emerald-600 dark:text-emerald-400">+{{ number_format($item['delta'], 0, ',', '.') }}</p>
                                </div>
                            @empty
                                <p class="p-5 text-sm text-gray-500 dark:text-gray-400">Nenhum ganho comparável.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-rose-200 bg-white shadow-sm dark:border-rose-900/60 dark:bg-gray-800">
                        <div class="border-b border-rose-100 bg-rose-50 px-4 py-3 dark:border-rose-900/50 dark:bg-rose-950/20">
                            <h3 class="font-semibold text-rose-900 dark:text-rose-200">Maiores quedas municipais</h3>
                            <p class="mt-1 text-xs text-rose-700/80 dark:text-rose-300/70">Ordenadas pela diferença absoluta de votos.</p>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($quedas as $item)
                                <div class="flex items-center justify-between gap-3 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $item['nome'] }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ number_format($item['votos_base'], 0, ',', '.') }} → {{ number_format($item['votos_comparada'], 0, ',', '.') }}</p>
                                    </div>
                                    <p class="shrink-0 font-bold {{ $item['delta'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $item['delta'] > 0 ? '+' : '' }}{{ number_format($item['delta'], 0, ',', '.') }}
                                    </p>
                                </div>
                            @empty
                                <p class="p-5 text-sm text-gray-500 dark:text-gray-400">Nenhuma queda comparável.</p>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Mapa da variação municipal</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Verde = crescimento, vermelho = queda, cinza = sem comparação oficial.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <a href="{{ $base['mapa_url'] }}" wire:navigate class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Mapa {{ $base['ano'] }}</a>
                            <a href="{{ $comparada['mapa_url'] }}" wire:navigate class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">Mapa {{ $comparada['ano'] }}</a>
                        </div>
                    </div>
                    <div
                        wire:ignore
                        wire:key="comparativo-map-{{ $base['id'] }}-{{ $comparada['id'] }}"
                        x-data
                        x-init="(() => { const boot = () => window.PoliticaComparativoMapLoader ? window.PoliticaComparativoMapLoader.mount($el, @js($mapaData), @js(['base' => $base['ano'], 'comparada' => $comparada['ano']])) : setTimeout(boot, 40); boot(); })()"
                        class="politica-comparativo-map relative w-full bg-gray-100 dark:bg-gray-950"
                        style="height:62vh; min-height:400px"
                        aria-label="Mapa comparativo territorial da Bahia">
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400">Carregando mapa comparativo…</div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="grid gap-3 border-b border-gray-200 p-4 md:grid-cols-[1fr_220px_auto] md:items-end dark:border-gray-700">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buscar município</label>
                            <input wire:model.live.debounce.350ms="busca" type="search" placeholder="Ex.: Salvador, Feira de Santana..." class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ordenar</label>
                            <select wire:model.live="ordem" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="delta_desc">Maior crescimento</option>
                                <option value="delta_asc">Maior queda</option>
                                <option value="atual_desc">Mais votos em {{ $comparada['ano'] }}</option>
                                <option value="base_desc">Mais votos em {{ $base['ano'] }}</option>
                                <option value="nome">Nome do município</option>
                            </select>
                        </div>
                        <p class="pb-2 text-xs text-gray-500 dark:text-gray-400">{{ number_format($paginacao['total'], 0, ',', '.') }} município(s)</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-900/60 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Município</th>
                                    <th class="px-4 py-3 text-right">{{ $base['ano'] }}</th>
                                    <th class="px-4 py-3 text-right">{{ $comparada['ano'] }}</th>
                                    <th class="px-4 py-3 text-right">Δ votos</th>
                                    <th class="px-4 py-3 text-right">Δ %</th>
                                    <th class="px-4 py-3 text-right">Δ participação</th>
                                    <th class="px-4 py-3">Cobertura</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($tabela as $item)
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3">
                                            <div class="min-w-[180px]">
                                                @if ($item['url'])
                                                    <a href="{{ $item['url'] }}" wire:navigate class="font-semibold text-gray-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">{{ $item['nome'] }}</a>
                                                @else
                                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $item['nome'] }}</span>
                                                @endif
                                                @if ($item['ibge_code'])
                                                    <p class="mt-0.5 text-[10px] text-gray-400">IBGE {{ $item['ibge_code'] }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-200">
                                            {{ $item['votos_base'] === null ? '—' : number_format($item['votos_base'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-200">
                                            {{ $item['votos_comparada'] === null ? '—' : number_format($item['votos_comparada'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold">
                                            @if ($item['delta'] === null)
                                                <span class="text-gray-400">—</span>
                                            @elseif ($item['delta'] > 0)
                                                <span class="text-emerald-600 dark:text-emerald-400">+{{ number_format($item['delta'], 0, ',', '.') }}</span>
                                            @elseif ($item['delta'] < 0)
                                                <span class="text-rose-600 dark:text-rose-400">{{ number_format($item['delta'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-gray-500">0</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">
                                            @if ($item['delta_percentual'] !== null)
                                                {{ $item['delta_percentual'] > 0 ? '+' : '' }}{{ number_format($item['delta_percentual'], 1, ',', '.') }}%
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">
                                            @if ($item['delta_participacao_pp'] !== null)
                                                {{ $item['delta_participacao_pp'] > 0 ? '+' : '' }}{{ number_format($item['delta_participacao_pp'], 3, ',', '.') }} p.p.
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($item['cobertura'] === 'ambas')
                                                <span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">comparável</span>
                                            @elseif ($item['cobertura'] === 'somente_base')
                                                <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">só {{ $base['ano'] }}</span>
                                            @else
                                                <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">só {{ $comparada['ano'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum município encontrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between gap-3 border-t border-gray-200 px-4 py-3 text-sm dark:border-gray-700">
                        <button type="button" wire:click="paginaAnterior" @disabled($paginacao['pagina'] <= 1) class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-200">← Anterior</button>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Página {{ $paginacao['pagina'] }} de {{ $paginacao['paginas'] }}</span>
                        <button type="button" wire:click="proximaPagina({{ $paginacao['paginas'] }})" @disabled($paginacao['pagina'] >= $paginacao['paginas']) class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-200">Próxima →</button>
                    </div>
                </section>

                <p class="px-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                    Fonte eleitoral: resultados municipais oficiais já importados do TSE para a Política V2. A variação municipal só é calculada quando há uma linha oficial para o mesmo município nos dois pleitos. Ausência de linha nunca é convertida automaticamente em zero.
                </p>
            @endif
        </div>
    </div>

    @assets
        <style>
            .politica-comparativo-map { height: 62vh; min-height: 400px; }
            .politica-comparativo-map .leaflet-container { font-family: inherit; }
            @media (max-width: 640px) { .politica-comparativo-map { height: 56vh; min-height: 350px; } }
            @media (min-width: 1024px) { .politica-comparativo-map { height: 68vh; } }
            .dark .politica-comparativo-map .leaflet-tile-pane { filter: grayscale(0.75) invert(0.92) hue-rotate(180deg) brightness(0.72) contrast(0.92); }
            .dark .politica-comparativo-map .leaflet-popup-content-wrapper,
            .dark .politica-comparativo-map .leaflet-popup-tip { background: #111827; color: #f9fafb; }
        </style>
    @endassets

    @assets
        <script>
            window.PoliticaComparativoMapLoader = window.PoliticaComparativoMapLoader || {
                leafletPromise: null,
                loadLeaflet() {
                    if (window.L) return Promise.resolve(window.L);
                    if (window.PoliticaMapLoader?.loadLeaflet) return window.PoliticaMapLoader.loadLeaflet();
                    if (this.leafletPromise) return this.leafletPromise;
                    this.leafletPromise = new Promise((resolve, reject) => {
                        if (!document.querySelector('link[data-politica-leaflet]')) {
                            const css = document.createElement('link');
                            css.rel = 'stylesheet';
                            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                            css.dataset.politicaLeaflet = '1';
                            document.head.appendChild(css);
                        }
                        const existing = document.querySelector('script[data-politica-leaflet]');
                        if (existing) {
                            existing.addEventListener('load', () => resolve(window.L), { once: true });
                            existing.addEventListener('error', reject, { once: true });
                            return;
                        }
                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.dataset.politicaLeaflet = '1';
                        script.onload = () => resolve(window.L);
                        script.onerror = () => reject(new Error('Não foi possível carregar o Leaflet.'));
                        document.head.appendChild(script);
                    });
                    return this.leafletPromise;
                },
                escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
                },
                formatNumber(value) {
                    return new Intl.NumberFormat('pt-BR').format(Number(value || 0));
                },
                async mount(element, cidades, meta) {
                    try {
                        const L = await this.loadLeaflet();
                        if (!element.isConnected) return;
                        if (element.__comparativoMap) {
                            try { element.__comparativoMap.remove(); } catch (_) {}
                        }
                        element.innerHTML = '';
                        const map = L.map(element, { zoomControl: true, preferCanvas: true, minZoom: 5, maxZoom: 14 });
                        element.__comparativoMap = map;
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19,
                        }).addTo(map);

                        const bounds = [];
                        cidades.forEach((cidade) => {
                            const lat = Number(cidade.lat), lng = Number(cidade.lng);
                            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                            bounds.push([lat, lng]);
                            const delta = cidade.delta === null || cidade.delta === undefined ? null : Number(cidade.delta);
                            const intensidade = Number(cidade.intensidade || 0);
                            let cor = '#9ca3af';
                            if (delta !== null && delta > 0) cor = '#059669';
                            if (delta !== null && delta < 0) cor = '#e11d48';
                            if (delta === 0) cor = '#64748b';
                            const radius = delta === null ? 3 : 4 + Math.sqrt(intensidade) * 13;
                            const marker = L.circleMarker([lat, lng], {
                                radius, weight: 1.4, color: cor, fillColor: cor,
                                fillOpacity: delta === null ? 0.32 : 0.42 + intensidade * 0.4,
                            }).addTo(map);

                            const base = cidade.votos_base === null ? 'sem linha' : this.formatNumber(cidade.votos_base);
                            const atual = cidade.votos_comparada === null ? 'sem linha' : this.formatNumber(cidade.votos_comparada);
                            const deltaTexto = delta === null ? 'sem comparação' : `${delta > 0 ? '+' : ''}${this.formatNumber(delta)}`;
                            const url = cidade.url ? `<a href="${this.escapeHtml(cidade.url)}" style="display:inline-block;margin-top:8px;font-weight:600;color:#4f46e5">Abrir Espelho →</a>` : '';
                            marker.bindPopup(`
                                <div style="min-width:210px">
                                    <div style="font-weight:700;font-size:14px;margin-bottom:7px">${this.escapeHtml(cidade.nome)}</div>
                                    <div><strong>${meta.base}:</strong> ${base}</div>
                                    <div><strong>${meta.comparada}:</strong> ${atual}</div>
                                    <div><strong>Variação:</strong> ${deltaTexto}</div>
                                    ${url}
                                </div>
                            `);
                        });
                        if (bounds.length) map.fitBounds(bounds, { padding: [18, 18], maxZoom: 8 });
                        else map.setView([-12.6, -41.5], 6);
                        setTimeout(() => map.invalidateSize(false), 100);
                    } catch (error) {
                        console.error(error);
                        if (element.isConnected) element.innerHTML = '<div style="display:flex;height:100%;align-items:center;justify-content:center;padding:24px;text-align:center;color:#b91c1c">Não foi possível carregar o mapa comparativo.</div>';
                    }
                }
            };
        </script>
    @endassets
</div>
