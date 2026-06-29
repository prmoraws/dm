@section('title', 'Dashboard do Administrador')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Dashboard do Administrador') }}
        </h2>
    </div>
</x-slot>

<div class="bg-slate-50 dark:bg-gray-900/90 min-h-screen pt-12" x-data="{}" x-init="$watch('darkMode', val => { @this.dispatch('dashboardRefreshed'); })">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white sm:truncate">
                Dashboard do Administrador
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Visão geral do sistema e atividade dos usuários.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $cards = [
                    ['title' => 'Total de Usuários', 'value' => $totalUsers, 'icon' => 'users', 'color' => 'blue'],
                    [
                        'title' => 'Novos Usuários (Mês)',
                        'value' => $newUsersThisMonth,
                        'icon' => 'user-plus',
                        'color' => 'green',
                    ],
                    ['title' => 'Sessões Ativas', 'value' => $activeSessions, 'icon' => 'signal', 'color' => 'amber'],
                    [
                        'title' => 'Total de Times',
                        'value' => $totalTeams,
                        'icon' => 'shield-check',
                        'color' => 'purple',
                    ],
                    // CORREÇÃO: Novos cards de navegação adicionados
                    [
                        'title' => 'Gerenciar Usuários',
                        'icon' => 'user-group',
                        'color' => 'cyan',
                        'button' => ['route' => 'adm.users', 'label' => 'Ver Usuários'],
                    ],
                    [
                        'title' => 'Gerenciar Times',
                        'icon' => 'cog',
                        'color' => 'rose',
                        'button' => ['route' => 'adm.teams', 'label' => 'Ver Times'],
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ $card['title'] }}</h3>
                                @if (isset($card['value']))
                                    <p class="mt-2"><span
                                            class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</span>
                                    </p>
                                @endif
                            </div>
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-{{ $card['color'] }}-500/10">
                                @if ($card['icon'] === 'users')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                                    </svg>
                                @elseif ($card['icon'] === 'user-plus')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                    </svg>
                                @elseif ($card['icon'] === 'signal')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                                    </svg>
                                @elseif ($card['icon'] === 'shield-check')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008h-.008v-.008z" />
                                    </svg>
                                @elseif ($card['icon'] === 'user-group')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3.75 3.75 0 0112 15.75c1.25 0 2.42.494 3.293 1.352m-6.586-8.552a3.75 3.75 0 016.586 0M6 6.878A3.75 3.75 0 0112 3c1.25 0 2.42.494 3.293 1.352m-6.586 8.552a3.75 3.75 0 00-6.586 0M12 12.75a3.75 3.75 0 01-6.586 0M18 15.75a3.75 3.75 0 01-6.586 0" />
                                    </svg>
                                @elseif ($card['icon'] === 'cog')
                                    <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l-1.41-.513M5.106 17.25l-1.41-.513M18.894 6.75l1.41-.513M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto">
                        @if (isset($card['button']))
                            <button wire:click="redirectTo('{{ $card['button']['route'] }}')"
                                class="text-sm font-semibold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:text-{{ $card['color'] }}-800 dark:hover:text-{{ $card['color'] }}-200 transition-colors">
                                {{ $card['button']['label'] }} &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div
                class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Usuários por Time</h3>
                <div class="flex-grow h-80"><canvas id="usersByTeamChart"></canvas></div>
            </div>
            <div
                class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Novos Usuários (Últimos 30 dias)
                </h3>
                <div class="flex-grow h-80"><canvas id="newUsersChart"></canvas></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let usersByTeamChart, newUsersChart;

                const chartDefaultOptions = (isDarkMode, type = 'bar') => ({
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: isDarkMode ? '#9ca3af' : '#4b5563',
                                callback: (value) => Number.isInteger(value) ? value : null
                            }
                        },
                        x: {
                            grid: {
                                display: type === 'line'
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
                                label: 'Usuários',
                                data: @json($chartUsersByTeam['data']),
                                backgroundColor: 'rgba(96, 165, 250, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode)
                    });
                };

                const initNewUsersChart = () => {
                    const ctx = document.getElementById('newUsersChart');
                    if (!ctx) return;
                    if (newUsersChart) newUsersChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    newUsersChart = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($chartNewUsersDaily['labels']),
                            datasets: [{
                                label: 'Novos Usuários',
                                data: @json($chartNewUsersDaily['data']),
                                backgroundColor: 'rgba(52, 211, 153, 0.1)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode, 'line')
                    });
                };

                Livewire.on('dashboardRefreshed', () => {
                    setTimeout(() => {
                        initUsersByTeamChart();
                        initNewUsersChart();
                    }, 10);
                });

                initUsersByTeamChart();
                initNewUsersChart();
            });
        </script>
    @endpush
</div>
