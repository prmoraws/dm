<div>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Inteligência Política</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Acompanhamento eleitoral, territorial e operacional em uma única base.</p>
            </div>
            <div class="mt-2 grid w-full gap-2 sm:mt-0 sm:flex sm:w-auto">
                <a href="{{ route('politica.acompanhamento') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Acompanhamento</a>
                <a href="{{ route('politica.cidades') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Espelho por cidade</a>
                <a href="{{ route('politica.mapa') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Mapa eleitoral</a>
                <a href="{{ route('politica.dados-oficiais') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Dados oficiais TSE</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                @php
                    $cards = [
                        ['label' => 'Municípios', 'value' => $metricas['cidades'], 'hint' => 'Base territorial'],
                        ['label' => 'Prioritários', 'value' => $metricas['acompanhamentos'], 'hint' => 'Acompanhamento ativo'],
                        ['label' => 'Com histórico', 'value' => $metricas['com_historico'], 'hint' => 'Já possuem candidatura'],
                        ['label' => 'Eleições', 'value' => $metricas['eleicoes'], 'hint' => 'Recortes disponíveis'],
                        ['label' => 'Candidaturas', 'value' => $metricas['candidaturas'], 'hint' => 'Base V2'],
                    ];
                @endphp
                @foreach ($cards as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    @forelse ($grupos as $grupo => $itens)
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $grupo }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($itens) }} acompanhamento(s) prioritário(s)</p>
                                </div>
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">Prioridade</span>
                            </div>

                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($itens as $item)
                                    @php $ultima = $item->politico?->candidaturas?->first(); @endphp
                                    <a href="{{ route('politica.politicos.show', $item->politico) }}" wire:navigate class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $item->politico?->nome_publico }}</p>
                                            @if ($ultima)
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $ultima->cargo?->nome }} · {{ $ultima->eleicao?->ano }}
                                                    @if ($ultima->partido?->sigla) · {{ $ultima->partido->sigla }} @endif
                                                </p>
                                            @else
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aguardando histórico/sincronização oficial.</p>
                                            @endif
                                        </div>
                                        <div class="shrink-0 text-right">
                                            @if ($ultima)
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($ultima->votos_total, 0, ',', '.') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">votos no recorte</p>
                                            @else
                                                <span class="text-sm text-indigo-600 dark:text-indigo-400">Abrir perfil →</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Nenhum acompanhamento prioritário cadastrado.</div>
                    @endforelse
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Saúde das integrações</h3>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Fontes registradas</span><strong class="text-gray-900 dark:text-white">{{ $fontes['total'] }}</strong></div>
                            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Saudáveis</span><strong class="text-emerald-600 dark:text-emerald-400">{{ $fontes['saudaveis'] }}</strong></div>
                            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Com erro</span><strong class="text-rose-600 dark:text-rose-400">{{ $fontes['com_erro'] }}</strong></div>
                        </div>
                        @if ($fontes['total'] === 0)
                            <p class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 dark:bg-amber-950/30 dark:text-amber-300">As fontes oficiais serão conectadas nas próximas etapas. Nenhum status externo é inventado aqui.</p>
                        @endif
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Migração do legado</h3>
                        @if ($ultima_migracao)
                            <div class="mt-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $ultima_migracao['status'] === 'concluida' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">{{ $ultima_migracao['status'] }}</span>
                                <p class="mt-3 break-words text-sm text-gray-700 dark:text-gray-300">{{ $ultima_migracao['chave'] }}</p>
                                @if ($ultima_migracao['concluida_em'])
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Concluída em {{ \Illuminate\Support\Carbon::parse($ultima_migracao['concluida_em'])->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        @else
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Nenhuma migração registrada.</p>
                        @endif
                    </div>

                    <div class="rounded-2xl bg-gray-950 p-5 text-white shadow-sm dark:bg-black">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-300">Arquitetura</p>
                        <h3 class="mt-2 font-semibold">Dados oficiais ≠ espelho operacional</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-300">Resultados eleitorais e informações internas permanecem separados, mantendo origem e rastreabilidade.</p>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</div>
