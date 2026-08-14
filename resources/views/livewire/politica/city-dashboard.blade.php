<div>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Espelho Inteligente — Bahia</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecione um município para cruzar contexto territorial, operacional e eleitoral.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Base territorial</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Politica\Cidade::count() }} municípios</h1>
                </div>
                <div class="w-full sm:max-w-sm">
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Pesquisar município..." class="w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
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
                                    <td class="px-5 py-4 text-right"><a href="{{ route('politica.espelho.inteligente', $cidade) }}" wire:navigate class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Abrir espelho →</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Nenhum município encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">{{ $cidades->links() }}</div>
            </div>
        </div>
    </div>
</div>
