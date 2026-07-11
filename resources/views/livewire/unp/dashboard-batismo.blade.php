<div>
    @section('title', 'Dashboard Batismos')

    <x-slot name="header">
        <div class="flex items-center space-x-3 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
            </svg>
            <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                Consolidado de Batismos
            </h2>
        </div>
    </x-slot>

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Cards de Indicadores do Estado -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Ano -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-b-4 border-blue-600">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batizados
                        Estado (Ano)</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $totalAno }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total acumulado neste ano</p>
                </div>

                <!-- Card Mês -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-b-4 border-teal-500">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batizados
                        Estado (Mês)</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $totalMes }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total acumulado no mês vigente</p>
                </div>

                <!-- Card Dia -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border-b-4 border-amber-500">
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batizados
                        Estado (Dia)</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-gray-100 mt-2">{{ $totalDia }}</p>
                    <p class="text-xs text-gray-400 mt-1">Registrados na data de hoje</p>
                </div>
            </div>

            <!-- Seção de Filtro por Bloco -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Batismos no Mês Atual por Bloco</h3>
                <div class="w-full md:w-1/3 mb-6">
                    <input type="text" wire:model.live.debounce.300ms="searchBloco"
                        class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Filtrar Bloco...">
                </div>

                <div class="flex flex-wrap gap-3">
                    @forelse($blocosMapeados as $bloco)
                        <div class="bg-gray-100 dark:bg-gray-700 px-4 py-3 rounded-lg flex items-center space-x-3">
                            <span
                                class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $bloco->nome }}:</span>
                            <span
                                class="bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $bloco->total_mes_atual }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">Nenhum bloco localizado.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tabela por Presídio -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Quantidade de Batismos no Mês por
                    Presídio</h3>

                <div class="overflow-x-auto rounded-lg shadow border border-gray-200 dark:border-gray-700">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-bold">
                            <tr>
                                <th class="py-3 px-6 text-left">Unidade Prisional / Presídio</th>
                                <th class="py-3 px-6 text-center">Batizados (Mês Atual)</th>
                            </tr>
                        </thead>
                        <tbody
                            class="text-gray-600 dark:text-gray-300 text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($presidiosTabela as $presidio)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="py-3 px-6 text-left font-medium">{{ $presidio->nome }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold {{ $presidio->total_mes_atual > 0 ? 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">
                                            {{ $presidio->total_mes_atual }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 px-6 text-center text-gray-500">Nenhum presídio
                                        cadastrado no sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
