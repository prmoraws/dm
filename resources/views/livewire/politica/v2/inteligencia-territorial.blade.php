@section('title', 'Política - Inteligência Territorial')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Leitura automática por regras</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Inteligência Territorial dos Acompanhados</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sinais estatísticos sobre a evolução municipal. Nenhum número é estimado e nenhuma ausência oficial é convertida em zero.</p>
            </div>
            <div class="grid w-full gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.comparativo-territorial', ['politico' => $selecao['politico'], 'cargo' => $selecao['cargo'], 'base' => $selecao['ano_base'], 'comparada' => $selecao['ano_comparada']]) }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Comparativo territorial</a>
                <a href="{{ route('politica.cidades') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Espelho por cidade</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-[1600px] space-y-5 px-3 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Político</label>
                        <select wire:model.live="politico" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @forelse ($opcoes['politicos'] as $item)
                                <option value="{{ $item['slug'] }}">{{ $item['nome'] }}</option>
                            @empty
                                <option value="">Sem comparação disponível</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cargo</label>
                        <select wire:model.live="cargo" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($opcoes['cargos'] as $cargoOpcao)
                                <option value="{{ $cargoOpcao }}">{{ $cargoOpcao }}</option>
                            @endforeach
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
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sinal</label>
                        <select wire:model.live="sinal" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            @foreach ($sinaisDisponiveis as $chave => $definicao)
                                <option value="{{ $chave }}">{{ $definicao['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            @if (! $disponivel)
                <section class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Ainda não há comparação territorial suficiente para gerar sinais automáticos.</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">São necessárias duas eleições do mesmo cargo com resultados municipais oficiais presentes nos dois pleitos.</p>
                </section>
            @else
                <section class="overflow-hidden rounded-2xl bg-gray-950 p-5 text-white shadow-sm sm:p-6 dark:bg-black">
                    <div class="grid gap-5 lg:grid-cols-[1fr_auto_1fr] lg:items-center">
                        <div class="flex min-w-0 items-center gap-4">
                            @if ($politicoResumo && $politicoResumo['foto_url'])
                                <img src="{{ $politicoResumo['foto_url'] }}" alt="Foto oficial de {{ $politicoResumo['nome'] }}" class="h-16 w-16 shrink-0 rounded-2xl object-cover ring-1 ring-white/15 sm:h-20 sm:w-20">
                            @endif
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-300">{{ $politicoResumo['nome'] ?? 'Acompanhado' }}</p>
                                <h3 class="mt-1 text-2xl font-bold">{{ $base['ano'] }} → {{ $comparada['ano'] }}</h3>
                                <p class="mt-1 text-sm text-gray-300">{{ $base['cargo'] }} · {{ $base['partido'] ?: '—' }} → {{ $comparada['partido'] ?: '—' }}</p>
                            </div>
                        </div>
                        <div class="hidden h-16 w-px bg-white/15 lg:block"></div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:text-right">
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Base</p>
                                <p class="mt-1 text-xl font-black">{{ number_format($base['votos'], 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Comparada</p>
                                <p class="mt-1 text-xl font-black">{{ number_format($comparada['votos'], 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Comparáveis</p>
                                <p class="mt-1 text-xl font-black">{{ $resumo['total'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Espelhos revisados</p>
                                <p class="mt-1 text-xl font-black">{{ $resumo['espelho_revisado'] }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                @if ($cobertura_incompleta)
                    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
                        <strong>Cobertura histórica parcial:</strong> os sinais abaixo usam somente municípios que possuem linha oficial nos dois pleitos. Municípios exclusivos de um dos anos permanecem fora da classificação automática.
                    </section>
                @endif

                <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                    @php
                        $cardsSinais = [
                            ['chave' => 'recuperacao', 'label' => 'Recuperação', 'valor' => $resumo['recuperacao'], 'classe' => 'text-sky-700 dark:text-sky-300'],
                            ['chave' => 'perda_relevante', 'label' => 'Perda relevante', 'valor' => $resumo['perda_relevante'], 'classe' => 'text-rose-700 dark:text-rose-300'],
                            ['chave' => 'concentracao', 'label' => 'Concentração', 'valor' => $resumo['concentracao'], 'classe' => 'text-violet-700 dark:text-violet-300'],
                            ['chave' => 'fortalecimento', 'label' => 'Fortalecimento', 'valor' => $resumo['fortalecimento'], 'classe' => 'text-emerald-700 dark:text-emerald-300'],
                            ['chave' => 'oportunidade_recuperacao', 'label' => 'Oportunidade', 'valor' => $resumo['oportunidade_recuperacao'], 'classe' => 'text-amber-700 dark:text-amber-300'],
                            ['chave' => 'estavel', 'label' => 'Sem sinal forte', 'valor' => $resumo['estavel'], 'classe' => 'text-gray-700 dark:text-gray-300'],
                        ];
                    @endphp
                    @foreach ($cardsSinais as $card)
                        <button type="button" wire:click="$set('sinal', '{{ $card['chave'] }}')" class="rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-700">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                            <p class="mt-2 text-3xl font-black {{ $card['classe'] }}">{{ $card['valor'] }}</p>
                        </button>
                    @endforeach
                </section>

                <section class="grid gap-4 xl:grid-cols-4">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Impacto relevante · P75</p>
                        <p class="mt-2 text-2xl font-black text-gray-950 dark:text-white">{{ number_format($limiares['impacto_p75_votos'], 0, ',', '.') }} votos</p>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Quartil superior da variação absoluta entre municípios comparáveis.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Base relevante · P75</p>
                        <p class="mt-2 text-2xl font-black text-gray-950 dark:text-white">{{ number_format($limiares['base_relevante_p75_votos'], 0, ',', '.') }} votos</p>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Usado para identificar território com base anterior relevante que recuou.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Concentração · P90</p>
                        <p class="mt-2 text-2xl font-black text-gray-950 dark:text-white">{{ number_format($limiares['concentracao_p90_participacao'], 3, ',', '.') }}%</p>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Decil superior da participação municipal na votação atual do candidato.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Histórico de recuperação</p>
                        <p class="mt-2 text-2xl font-black text-gray-950 dark:text-white">
                            @if ($limiares['ano_anterior'])
                                {{ $limiares['ano_anterior'] }} → {{ $base['ano'] }} → {{ $comparada['ano'] }}
                            @else
                                2 pleitos
                            @endif
                        </p>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">“Recuperação” só é usada quando existe um terceiro pleito anterior comparável.</p>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="grid gap-3 border-b border-gray-200 p-4 md:grid-cols-[1fr_220px] dark:border-gray-700">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buscar município</label>
                            <input type="search" wire:model.live.debounce.300ms="busca" placeholder="Ex.: Salvador" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ordenar</label>
                            <select wire:model.live="ordem" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="relevancia_desc">Maior relevância</option>
                                <option value="delta_desc">Maior crescimento</option>
                                <option value="delta_asc">Maior queda</option>
                                <option value="atual_desc">Maior votação atual</option>
                                <option value="nome">Município A–Z</option>
                            </select>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($itens as $item)
                            @php
                                $badge = match ($item['sinal_principal']) {
                                    'recuperacao' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
                                    'perda_relevante' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
                                    'concentracao' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300',
                                    'fortalecimento' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                    'oportunidade_recuperacao' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <article class="p-4 sm:p-5">
                                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,.75fr)]">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ $item['espelho_url'] }}" wire:navigate class="text-lg font-bold text-gray-950 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">{{ $item['nome'] }}</a>
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $badge }}">{{ $item['sinal_label'] }}</span>
                                            <span class="rounded-full bg-gray-950 px-2.5 py-1 text-[10px] font-bold text-white dark:bg-white dark:text-gray-950">score {{ $item['relevancia_score'] }}</span>
                                        </div>
                                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $item['sinal_explicacao'] }}</p>

                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            @foreach ($item['sinais'] as $sinalItem)
                                                @if ($sinalItem !== $item['sinal_principal'])
                                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $sinaisDisponiveis[$sinalItem]['label'] }}</span>
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400">{{ $base['ano'] }}</p>
                                                <p class="mt-1 font-bold text-gray-900 dark:text-white">{{ number_format($item['votos_base'], 0, ',', '.') }}</p>
                                            </div>
                                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400">{{ $comparada['ano'] }}</p>
                                                <p class="mt-1 font-bold text-gray-900 dark:text-white">{{ number_format($item['votos_comparada'], 0, ',', '.') }}</p>
                                            </div>
                                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Δ votos</p>
                                                <p class="mt-1 font-bold {{ $item['delta'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($item['delta'] < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-600 dark:text-gray-300') }}">
                                                    {{ $item['delta'] > 0 ? '+' : '' }}{{ number_format($item['delta'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Δ participação</p>
                                                <p class="mt-1 font-bold text-gray-900 dark:text-white">
                                                    @if ($item['delta_participacao_pp'] !== null)
                                                        {{ $item['delta_participacao_pp'] > 0 ? '+' : '' }}{{ number_format($item['delta_participacao_pp'], 3, ',', '.') }} p.p.
                                                    @else
                                                        —
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <p class="text-[10px] uppercase tracking-wider text-gray-400">Atual / total</p>
                                                <p class="mt-1 font-bold text-gray-900 dark:text-white">
                                                    @if ($item['participacao_comparada'] !== null)
                                                        {{ number_format($item['participacao_comparada'], 3, ',', '.') }}%
                                                    @else
                                                        —
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        @if ($item['recuperacao'])
                                            <div class="mt-3 rounded-xl border border-sky-200 bg-sky-50 p-3 text-xs text-sky-900 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-sky-200">
                                                Série de recuperação: {{ $item['recuperacao']['ano_anterior'] }} = {{ number_format($item['recuperacao']['votos_anterior'], 0, ',', '.') }} votos; {{ $base['ano'] }} = {{ number_format($item['votos_base'], 0, ',', '.') }}; {{ $comparada['ano'] }} = {{ number_format($item['votos_comparada'], 0, ',', '.') }}. Recuperação calculada: {{ number_format($item['recuperacao']['taxa_percentual'], 1, ',', '.') }}% da perda anterior.
                                            </div>
                                        @endif
                                    </div>

                                    <aside class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Espelho operacional</p>
                                                @if ($item['operacional']['revisado'])
                                                    <p class="mt-1 text-sm font-semibold text-emerald-700 dark:text-emerald-300">Revisado em {{ $item['operacional']['revisado_em'] }}</p>
                                                @elseif ($item['operacional']['presente'])
                                                    <p class="mt-1 text-sm font-semibold text-amber-700 dark:text-amber-300">Cadastrado · revisão pendente</p>
                                                @else
                                                    <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">Ainda sem registro operacional</p>
                                                @endif
                                            </div>
                                            <a href="{{ $item['espelho_editar_url'] }}" wire:navigate class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Editar</a>
                                        </div>

                                        @if ($item['operacional']['presente'])
                                            <dl class="mt-4 space-y-2 text-sm">
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-gray-500 dark:text-gray-400">Presidente local</dt>
                                                    <dd class="text-right font-semibold text-gray-900 dark:text-white">{{ $item['operacional']['presidente_local'] ?: '—' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-gray-500 dark:text-gray-400">Indicação bispo</dt>
                                                    <dd class="text-right font-semibold text-gray-900 dark:text-white">{{ $item['operacional']['indicacao_bispo'] ?: '—' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-gray-500 dark:text-gray-400">Filiados REP cadastrados</dt>
                                                    <dd class="text-right font-semibold text-gray-900 dark:text-white">
                                                        @if ($item['operacional']['filiados_republicanos'] !== null)
                                                            {{ number_format($item['operacional']['filiados_republicanos'], 0, ',', '.') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </dd>
                                                </div>
                                            </dl>
                                        @endif

                                        <a href="{{ $item['espelho_url'] }}" wire:navigate class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-gray-950 px-3 py-2.5 text-sm font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Abrir Espelho Inteligente</a>
                                    </aside>
                                </div>
                            </article>
                        @empty
                            <div class="p-8 text-center">
                                <p class="font-semibold text-gray-900 dark:text-white">Nenhum município atende ao filtro atual.</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Troque o sinal ou limpe a busca.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="flex items-center justify-between gap-3 border-t border-gray-200 px-4 py-3 text-sm dark:border-gray-700">
                        <button type="button" wire:click="paginaAnterior" @disabled($paginacao['pagina'] <= 1) class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-200">← Anterior</button>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($paginacao['total'], 0, ',', '.') }} município(s) · página {{ $paginacao['pagina'] }} de {{ $paginacao['paginas'] }}</span>
                        <button type="button" wire:click="proximaPagina({{ $paginacao['paginas'] }})" @disabled($paginacao['pagina'] >= $paginacao['paginas']) class="rounded-lg border border-gray-300 px-3 py-2 font-semibold text-gray-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-200">Próxima →</button>
                    </div>
                </section>

                <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-900/60 dark:bg-indigo-950/25">
                    <h3 class="font-bold text-indigo-950 dark:text-indigo-100">Método transparente — sem IA generativa</h3>
                    <div class="mt-3 grid gap-3 text-sm leading-6 text-indigo-900/90 md:grid-cols-2 dark:text-indigo-200">
                        @foreach ($sinaisDisponiveis as $chave => $definicao)
                            @if ($chave !== 'todos')
                                <p><strong>{{ $definicao['label'] }}:</strong> {{ $definicao['descricao'] }}</p>
                            @endif
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs leading-5 text-indigo-800/80 dark:text-indigo-300">Os percentis são recalculados para cada político, cargo e par de eleições. O score serve apenas para ordenar intensidade relativa dentro do recorte; ele não é probabilidade eleitoral, previsão de voto nem recomendação de campanha.</p>
                </section>
            @endif
        </div>
    </div>
</div>
