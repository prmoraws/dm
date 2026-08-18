@section('title', 'Política - Dashboard')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Inteligência Política</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Acompanhamento eleitoral, territorial e operacional em uma única base.</p>
            </div>
            <div class="mt-2 grid w-full gap-2 sm:mt-0 sm:flex sm:w-auto">
                <a href="{{ route('politica.eleicoes-2026') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Eleições 2026</a>
                <a href="{{ route('politica.acompanhamento') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Acompanhamento</a>
                <a href="{{ route('politica.historico-acompanhados') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Histórico dos 4</a>
                <a href="{{ route('politica.comparativo-territorial') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Comparativo territorial</a>
                <a href="{{ route('politica.inteligencia-territorial') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-indigo-300 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 dark:border-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-950/70">Inteligência territorial</a>
                <a href="{{ route('politica.painel-executivo') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 dark:bg-white dark:text-gray-950 dark:hover:bg-gray-200">Painel executivo</a>
                <a href="{{ route('politica.cidades') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Espelho por cidade</a>
                <a href="{{ route('politica.mapa') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Mapa eleitoral</a>
                <a href="{{ route('politica.qualidade-dados') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-300 dark:hover:bg-amber-950/50">Qualidade e auditoria</a>
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
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 ring-1 ring-black/5 dark:bg-indigo-950/50 dark:text-indigo-300 dark:ring-white/10">
                                                @if ($item->politico?->foto_url)
                                                    <img src="{{ $item->politico->foto_url }}" alt="Foto oficial de {{ $item->politico->nome_publico }}" class="h-full w-full object-cover">
                                                @else
                                                    {{ mb_strtoupper(mb_substr($item->politico?->nome_publico ?? '?', 0, 1)) }}
                                                @endif
                                            </div>
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
                                        </div>
                                        <div class="shrink-0 text-right">
                                            @if ($ultima && $ultima->isRegistroOficialTse() && (int) $ultima->eleicao?->ano === 2026 && (($ultima->resultados_municipais_count ?? 0) + ($ultima->resultados_zonas_count ?? 0) === 0))
                                                <p class="font-semibold text-indigo-600 dark:text-indigo-400">Registro TSE 2026</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">sem resultado eleitoral</p>
                                            @elseif ($ultima)
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

                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50/70 p-5 shadow-sm dark:border-indigo-900 dark:bg-indigo-950/20">
                        @php
                            $pedidoTse = $tse_sync['ultima_solicitacao'];
                            $importacaoTse = $tse_sync['ultima_importacao'];
                            $statusTse = $pedidoTse?->status;
                            $statusClasses = match ($statusTse) {
                                'concluida' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                'erro' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
                                'executando' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
                                'pendente' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                            };
                        @endphp
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Sincronização TSE 2026</p>
                                <h3 class="mt-1 font-semibold text-gray-900 dark:text-white">Atualização automática + manual</h3>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $tse_sync['scheduler_ativo'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' }}">
                                {{ $tse_sync['scheduler_ativo'] ? 'Scheduler ativo' : 'Aguardando cron' }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between gap-3"><span class="text-gray-500 dark:text-gray-400">Automática</span><strong class="text-gray-900 dark:text-white">{{ $tse_sync['automatico_ativo'] ? 'Ativada' : 'Desativada' }}</strong></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500 dark:text-gray-400">Última importação</span><strong class="text-right text-gray-900 dark:text-white">{{ $importacaoTse?->concluida_em?->format('d/m/Y H:i') ?? '—' }}</strong></div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500 dark:text-gray-400">Última solicitação</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClasses }}">{{ $statusTse ?? 'nenhuma' }}</span>
                            </div>
                        </div>

                        @if (session('politica_tse_sync_message'))
                            <p class="mt-3 rounded-xl bg-white/80 px-3 py-2 text-xs text-gray-700 dark:bg-gray-900/60 dark:text-gray-300">{{ session('politica_tse_sync_message') }}</p>
                        @endif

                        <button
                            type="button"
                            wire:click="solicitarAtualizacaoTse2026"
                            wire:loading.attr="disabled"
                            wire:target="solicitarAtualizacaoTse2026"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-wait disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="solicitarAtualizacaoTse2026">Atualizar TSE agora</span>
                            <span wire:loading wire:target="solicitarAtualizacaoTse2026">Enfileirando...</span>
                        </button>
                        <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">O botão não faz o download dentro da página. Ele cria uma solicitação segura; o scheduler executa em segundo plano, normalmente em até 1 minuto.</p>
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
