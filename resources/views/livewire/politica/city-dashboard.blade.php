@section('title', 'Política - Espelho Inteligente - Bahia')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Base territorial</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">Espelho Inteligente — Bahia</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecione um município para cruzar contexto territorial, operacional e eleitoral.</p>
            </div>
            <a href="{{ route('politica.mapa') }}" wire:navigate
                class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 sm:w-auto">Abrir mapa eleitoral</a>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-3 sm:px-6 lg:px-8">
            <section class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Municípios cadastrados</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCidades, 0, ',', '.') }}</h1>
                </div>
                <div class="w-full sm:max-w-sm">
                    <label for="cidade-search" class="sr-only">Pesquisar município</label>
                    <input id="cidade-search" wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar município..."
                        class="w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-700">
                    @forelse ($cidades as $cidade)
                        <article class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words font-semibold text-gray-900 dark:text-white">{{ $cidade->nome }}</h3>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">IBGE {{ $cidade->ibge_code ?: 'não informado' }}</p>
                                </div>
                                <a href="{{ route('politica.espelho.inteligente', $cidade) }}" wire:navigate
                                    class="shrink-0 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">Abrir</a>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                @if ($cidade->espelho_operacional_exists)
                                    <span class="rounded-full bg-sky-50 px-2.5 py-1 font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">Operacional disponível</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-500 dark:bg-gray-700 dark:text-gray-300">Operacional não revisado</span>
                                @endif
                                @if ($cidade->resultados_eleitorais_exists)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Com resultados V2</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-500 dark:bg-gray-700 dark:text-gray-300">Sem resultados V2</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum município encontrado.</div>
                    @endforelse
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="px-5 py-3">Município</th>
                                <th class="px-5 py-3">IBGE</th>
                                <th class="px-5 py-3">Operacional</th>
                                <th class="px-5 py-3">Eleitoral V2</th>
                                <th class="px-5 py-3 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($cidades as $cidade)
                                <tr class="text-sm transition hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-5 py-4 font-semibold text-gray-900 dark:text-white">{{ $cidade->nome }}</td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ $cidade->ibge_code ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        @if ($cidade->espelho_operacional_exists)
                                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">Disponível</span>
                                        @else
                                            <span class="text-xs text-gray-400">Não revisado</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($cidade->resultados_eleitorais_exists)
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Com resultados</span>
                                        @else
                                            <span class="text-xs text-gray-400">Sem resultados V2</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right"><a href="{{ route('politica.espelho.inteligente', $cidade) }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Abrir espelho →</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum município encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">{{ $cidades->links() }}</div>
            </section>
        </div>
    </div>
</div>
