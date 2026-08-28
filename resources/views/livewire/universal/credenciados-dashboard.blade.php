@section('title', 'Dashboard de Credenciados')

<div class="min-h-screen bg-slate-50 px-3 py-6 text-slate-900 dark:bg-gray-950 dark:text-slate-100 sm:px-6 lg:px-8">
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold">Dashboard de Credenciados</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Visão gerencial das pessoas e credenciais prisionais</p>
            </div>
            <a href="{{ route('universal.credenciados') }}"
               class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                Abrir gestão de credenciados
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold">Filtros do painel</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">O período considera a data de cadastro da pessoa.</p>
                </div>
                <button type="button" wire:click="limparFiltros" class="text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    Limpar filtros
                </button>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <label class="space-y-1 text-sm">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">Data inicial</span>
                    <input type="date" wire:model.live="dataInicio" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">Data final</span>
                    <input type="date" wire:model.live="dataFim" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                </label>
                @if(auth()->user()->bloco_id == 21)
                    <label class="space-y-1 text-sm">
                        <span class="font-semibold text-slate-600 dark:text-slate-300">Bloco</span>
                        <select wire:model.live="blocoId" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                            <option value="">Todos</option>
                            @foreach($blocosDisponiveis as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                        </select>
                    </label>
                @endif
                <label class="space-y-1 text-sm">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">Região</span>
                    <select wire:model.live="regiaoId" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                        <option value="">Todas</option>
                        @foreach($regioesDisponiveis as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                    </select>
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">Igreja</span>
                    <select wire:model.live="igrejaId" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                        <option value="">Todas</option>
                        @foreach($igrejasDisponiveis as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                    </select>
                </label>
                <label class="space-y-1 text-sm">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">Presídio</span>
                    <select wire:model.live="presidioId" class="w-full rounded-xl border-slate-300 bg-white dark:border-gray-700 dark:bg-gray-950">
                        <option value="">Todos</option>
                        @foreach($presidiosDisponiveis as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                    </select>
                </label>
            </div>
        </section>

        @php
            $filtros = array_filter([
                'inicio' => $dataInicio,
                'fim' => $dataFim,
                'bloco' => auth()->user()->bloco_id == 21 ? $blocoId : null,
                'regiao' => $regiaoId,
                'igreja' => $igrejaId,
                'presidio' => $presidioId,
            ], fn ($valor) => $valor !== null && $valor !== '');
            $cards = [
                ['status' => '', 'rotulo' => 'Total de credenciados', 'valor' => $indicadores['total'], 'cor' => 'from-blue-700 to-indigo-800', 'texto' => 'text-white'],
                ['status' => 'com_credencial', 'rotulo' => 'Com credencial', 'valor' => $indicadores['com_credencial'], 'cor' => 'from-emerald-500 to-emerald-700', 'texto' => 'text-white'],
                ['status' => 'sem_credencial', 'rotulo' => 'Sem credencial', 'valor' => $indicadores['sem_credencial'], 'cor' => 'from-slate-500 to-slate-700', 'texto' => 'text-white'],
                ['status' => 'validas', 'rotulo' => 'Credencial válida', 'valor' => $indicadores['validas'], 'cor' => 'from-cyan-500 to-blue-600', 'texto' => 'text-white'],
                ['status' => 'vencendo', 'rotulo' => 'Vence em até 30 dias', 'valor' => $indicadores['vencendo'], 'cor' => 'from-amber-400 to-orange-500', 'texto' => 'text-white'],
                ['status' => 'vencidas', 'rotulo' => 'Credencial vencida', 'valor' => $indicadores['vencidas'], 'cor' => 'from-red-600 to-red-800', 'texto' => 'text-white'],
                ['status' => 'sem_validade', 'rotulo' => 'Sem validade informada', 'valor' => $indicadores['sem_validade'], 'cor' => 'from-violet-500 to-purple-700', 'texto' => 'text-white'],
                ['status' => 'unidade_nao_faz', 'rotulo' => 'Unidade não emite', 'valor' => $indicadores['unidade_nao_faz'], 'cor' => 'from-fuchsia-500 to-pink-700', 'texto' => 'text-white'],
            ];
        @endphp

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($cards as $card)
                <a href="{{ route('universal.credenciados', array_merge($filtros, ['credencial_status' => $card['status']])) }}"
                   class="group rounded-2xl bg-gradient-to-br {{ $card['cor'] }} p-5 {{ $card['texto'] }} shadow-md transition hover:-translate-y-0.5 hover:shadow-xl">
                    <p class="text-sm font-semibold text-white/85">{{ $card['rotulo'] }}</p>
                    <div class="mt-2 flex items-end justify-between">
                        <strong class="text-4xl font-black">{{ number_format($card['valor'], 0, ',', '.') }}</strong>
                        <span class="text-xs font-bold text-white/80 group-hover:text-white">Ver lista →</span>
                    </div>
                </a>
            @endforeach
        </section>

        <section class="grid gap-5 xl:grid-cols-2">
            <article class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm dark:border-red-950 dark:bg-gray-900">
                <header class="flex items-start justify-between gap-4 bg-red-50 p-5 dark:bg-red-950/40">
                    <div>
                        <h3 class="font-black text-red-800 dark:text-red-300">Credenciais vencidas</h3>
                        <p class="mt-1 text-xs text-red-700/80 dark:text-red-300/80">Exige regularização ou confirmação da situação junto à unidade.</p>
                    </div>
                    <span class="rounded-full bg-red-700 px-3 py-1 text-sm font-black text-white">{{ $alertas['vencidas_total'] }}</span>
                </header>
                <div class="max-h-96 overflow-auto">
                    @forelse($alertas['vencidas'] as $credencial)
                        <a href="{{ route('universal.credenciados', array_merge($filtros, ['presidio' => $credencial->presidio_id, 'credencial_status' => 'vencidas'])) }}"
                           class="flex items-center justify-between gap-4 border-t border-red-100 px-5 py-3 transition hover:bg-red-50 dark:border-red-950/60 dark:hover:bg-red-950/20">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold">{{ $credencial->credenciado->nome ?? 'Credenciado removido' }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $credencial->presidio->nome ?? 'Presídio não informado' }}</p>
                            </div>
                            <time class="shrink-0 text-sm font-black text-red-700 dark:text-red-400">{{ $credencial->data_vencimento?->format('d/m/Y') }}</time>
                        </a>
                    @empty
                        <div class="p-8 text-center text-sm font-semibold text-emerald-700 dark:text-emerald-400">Nenhuma credencial vencida nos filtros atuais.</div>
                    @endforelse
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm dark:border-amber-950 dark:bg-gray-900">
                <header class="flex items-start justify-between gap-4 bg-amber-50 p-5 dark:bg-amber-950/40">
                    <div>
                        <h3 class="font-black text-amber-900 dark:text-amber-300">Vencem ainda neste mês</h3>
                        <p class="mt-1 text-xs text-amber-800/80 dark:text-amber-300/80">Da data de hoje até o último dia do mês corrente.</p>
                    </div>
                    <span class="rounded-full bg-amber-500 px-3 py-1 text-sm font-black text-slate-950">{{ $alertas['vencendo_mes_total'] }}</span>
                </header>
                <div class="max-h-96 overflow-auto">
                    @forelse($alertas['vencendo_mes'] as $credencial)
                        <a href="{{ route('universal.credenciados', array_merge($filtros, ['presidio' => $credencial->presidio_id, 'credencial_status' => 'vencendo'])) }}"
                           class="flex items-center justify-between gap-4 border-t border-amber-100 px-5 py-3 transition hover:bg-amber-50 dark:border-amber-950/60 dark:hover:bg-amber-950/20">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold">{{ $credencial->credenciado->nome ?? 'Credenciado removido' }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $credencial->presidio->nome ?? 'Presídio não informado' }}</p>
                            </div>
                            <time class="shrink-0 text-sm font-black text-amber-700 dark:text-amber-400">{{ $credencial->data_vencimento?->format('d/m/Y') }}</time>
                        </a>
                    @empty
                        <div class="p-8 text-center text-sm font-semibold text-slate-500">Nenhuma credencial vence no restante deste mês.</div>
                    @endforelse
                </div>
            </article>
        </section>

        @php($tabelas = [
            ['titulo' => 'Por bloco', 'dados' => $blocos],
            ['titulo' => 'Por região', 'dados' => $regioes],
            ['titulo' => 'Por igreja', 'dados' => $igrejas],
            ['titulo' => 'Por função / cargo', 'dados' => $cargos],
            ['titulo' => 'Por categoria', 'dados' => $categorias],
            ['titulo' => 'Por presídio', 'dados' => $presidios],
        ])
        <section class="grid gap-5 lg:grid-cols-2">
            @foreach($tabelas as $tabela)
                <article x-data="{ aberto: {{ $loop->index < 2 ? 'true' : 'false' }} }" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <button type="button" @click="aberto = !aberto" class="flex w-full items-center justify-between p-5 text-left">
                        <span class="font-bold">{{ $tabela['titulo'] }}</span>
                        <span class="text-xs font-semibold text-slate-500" x-text="aberto ? 'Recolher' : 'Exibir'"></span>
                    </button>
                    <div x-show="aberto" class="border-t border-slate-200 dark:border-gray-800">
                        <div class="max-h-80 overflow-auto">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 bg-slate-100 text-left text-xs uppercase text-slate-500 dark:bg-gray-800 dark:text-slate-300">
                                    <tr><th class="px-5 py-3">Nome</th><th class="px-5 py-3 text-right">Quantidade</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($tabela['dados'] as $linha)
                                        <tr class="border-t border-slate-100 dark:border-gray-800">
                                            <td class="px-5 py-3">{{ $linha->nome }}</td>
                                            <td class="px-5 py-3 text-right font-bold">{{ number_format($linha->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="p-8 text-center text-slate-500">Nenhum resultado no período.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5"><h3 class="font-bold">Cadastros mais recentes</h3></div>
            <div class="overflow-x-auto border-t border-slate-200 dark:border-gray-800">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 text-left text-xs uppercase text-slate-500 dark:bg-gray-800 dark:text-slate-300">
                        <tr><th class="px-5 py-3">Nome</th><th class="px-5 py-3">Bloco</th><th class="px-5 py-3">Igreja</th><th class="px-5 py-3">Cadastro</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentes as $credenciado)
                            <tr class="border-t border-slate-100 dark:border-gray-800">
                                <td class="px-5 py-3 font-semibold">{{ $credenciado->nome }}</td>
                                <td class="px-5 py-3">{{ $credenciado->bloco->nome ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $credenciado->igreja->nome ?? '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $credenciado->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-slate-500">Nenhum cadastro encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div wire:loading.flex class="fixed inset-0 z-[100] items-center justify-center bg-slate-950/35 backdrop-blur-sm">
        <div class="rounded-2xl bg-white px-6 py-4 font-bold text-slate-700 shadow-xl dark:bg-gray-900 dark:text-white">Atualizando painel…</div>
    </div>
</div>
