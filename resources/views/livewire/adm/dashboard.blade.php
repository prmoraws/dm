@section('title', 'Dashboard do Administrador')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Visão Geral do Sistema') }}
        </h2>
    </div>
</x-slot>

<div class="bg-slate-50 dark:bg-gray-900/90 min-h-screen pt-8" x-data="{}" x-init="$watch('darkMode', val => { @this.dispatch('dashboardRefreshed'); })">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <!-- Cartões de Métricas -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            @php
                $cards = [
                    [
                        'title' => 'Usuários',
                        'value' => $totalUsers,
                        'icon' => 'users',
                        'color' => 'blue',
                        'route' => 'adm.users',
                    ],
                    [
                        'title' => 'Sessões Ativas',
                        'value' => $activeSessions,
                        'icon' => 'signal',
                        'color' => 'green',
                        'route' => null,
                    ],
                    [
                        'title' => 'Times',
                        'value' => $totalTeams,
                        'icon' => 'shield-check',
                        'color' => 'purple',
                        'route' => 'adm.teams',
                    ],
                    [
                        'title' => 'Blocos',
                        'value' => $totalBlocos,
                        'icon' => 'cube',
                        'color' => 'amber',
                        'route' => null,
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-md p-6 flex flex-col justify-between transition-transform transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $card['title'] }}</h3>
                            <p class="mt-2"><span
                                    class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</span>
                            </p>
                        </div>
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30">
                            <!-- Ícones Dinâmicos SVG simplificados -->
                            @if ($card['icon'] === 'users')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 1.857a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            @elseif ($card['icon'] === 'signal')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M5.5 17.5L3 20m0 0l2.5 2.5M3 20h18M13 3l-4 9h5l-4 9" />
                                </svg>
                            @elseif ($card['icon'] === 'shield-check')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008h-.008v-.008z" />
                                </svg>
                            @elseif ($card['icon'] === 'cube')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    @if ($card['route'])
                        <div class="mt-4">
                            <button wire:click="redirectTo('{{ $card['route'] }}')"
                                class="text-sm font-semibold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:underline">
                                Gerenciar &rarr;
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-md p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Usuários por Time (Acessos)</h3>
                <div class="flex-grow h-72"><canvas id="usersByTeamChart"></canvas></div>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-md p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Usuários por Bloco</h3>
                <div class="flex-grow h-72"><canvas id="usersByBlocoChart"></canvas></div>
            </div>
        </div>

        <!-- Tabela de Últimos Usuários -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Últimos Usuários Cadastrados</h3>
                <button wire:click="redirectTo('adm.users')"
                    class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver todos</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">Nome</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Time</th>
                            <th class="py-3 px-4 text-left">Bloco</th>
                            <th class="py-3 px-4 text-left">Data de Criação</th>
                        </tr>
                    </thead>
                    <tbody
                        class="text-sm text-gray-700 dark:text-gray-300 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($recentUsers as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-4 flex items-center gap-3">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $user->profile_photo_url }}"
                                        alt="">
                                    <span class="font-medium">{{ $user->name }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $user->currentTeam->name ?? 'Sem Time' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                        {{ optional($user->bloco)->nome ?? 'Nenhum' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">Nenhum usuário recente.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let usersByTeamChart, usersByBlocoChart;

                const chartDefaultOptions = (isDarkMode) => ({
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                color: isDarkMode ? '#9ca3af' : '#4b5563',
                                callback: (val) => Number.isInteger(val) ? val : null
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: isDarkMode ? '#9ca3af' : '#4b5563'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                });

                const initUsersByTeamChart = () => {
                    const ctx = document.getElementById('usersByTeamChart');
                    if (!ctx) return;
                    if (usersByTeamChart) usersByTeamChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    usersByTeamChart = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($chartUsersByTeam['labels']),
                            datasets: [{
                                data: @json($chartUsersByTeam['data']),
                                backgroundColor: 'rgba(168, 85, 247, 0.5)', // Roxo
                                borderColor: 'rgba(147, 51, 234, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode)
                    });
                };

                const initUsersByBlocoChart = () => {
                    const ctx = document.getElementById('usersByBlocoChart');
                    if (!ctx) return;
                    if (usersByBlocoChart) usersByBlocoChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    usersByBlocoChart = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($chartUsersByBloco['labels']),
                            datasets: [{
                                data: @json($chartUsersByBloco['data']),
                                backgroundColor: 'rgba(245, 158, 11, 0.5)', // Âmbar
                                borderColor: 'rgba(217, 119, 6, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode)
                    });
                };

                Livewire.on('dashboardRefreshed', () => {
                    setTimeout(() => {
                        initUsersByTeamChart();
                        initUsersByBlocoChart();
                    }, 10);
                });

                initUsersByTeamChart();
                initUsersByBlocoChart();
            });
        </script>
    @endpush
</div>
