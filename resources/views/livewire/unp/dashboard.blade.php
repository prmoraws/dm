@section('title', 'Dashboard UNP')

<div class="bg-slate-50 dark:bg-gray-900/90 min-h-screen pt-28 pb-12" x-data="{}" x-init="$watch('darkMode', val => { @this.dispatch('dashboardRefreshed'); })">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Cabeçalho da Página e Botões --}}
        <div class="mb-8 md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white sm:truncate">
                    Dashboard UNP
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Visão geral das atividades do UNP.</p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <button wire:click="refreshDashboard" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <svg wire:loading wire:target="refreshDashboard" class="animate-spin -ml-1 mr-2 h-5 w-5"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="refreshDashboard" class="-ml-1 mr-2 h-5 w-5"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd" />
                    </svg>
                    Atualizar
                </button>
            </div>
        </div>

        {{-- Mensagem de Feedback --}}
        @if ($message)
            <div x-data="{ show: true }" x-show="show" x-transition.leave.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="mb-6 rounded-md p-4 bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300">
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
        @endif

        {{-- Cards Dinâmicos --}}
        @php
            $cards = [
                [
                    'title' => 'Instrutores',
                    'value' => $instrutoresCount,
                    'label' => 'Ativos',
                    'route' => 'instrutores',
                    'color' => 'blue',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Presídios',
                    'value' => $presidiosCount,
                    'label' => 'Atendidos',
                    'route' => 'presidios',
                    'color' => 'purple',
                    'icon' => 'presidio',
                ],
                [
                    'title' => 'Formaturas',
                    'value' => $formaturasCount,
                    'label' => 'Realizadas',
                    'route' => 'formaturas',
                    'color' => 'green',
                    'icon' => 'formatura',
                ],
                [
                    'title' => 'Grupos',
                    'value' => $gruposCount,
                    'label' => 'Ativos',
                    'route' => 'grupos',
                    'color' => 'amber',
                    'icon' => 'groups',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 hover:shadow-2xl transition-all duration-300 p-6">
                    {{-- Conteúdo do Card --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $card['title'] }}</h3>
                            <p class="mt-2"><span
                                    class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</span>
                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-1">{{ $card['label'] }}</span>
                            </p>
                        </div>
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-{{ $card['color'] }}-500/10">
                            {{-- Ícones --}}
                            @if ($card['icon'] === 'users')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                                </svg>
                            @elseif($card['icon'] === 'presidio')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.707 4.586L4.586 7.707m11.707-3.121l-3.121 3.121M12 21a9 9 0 110-18 9 9 0 010 18z" />
                                </svg>
                            @elseif($card['icon'] === 'formatura')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222 4 2.222V20" />
                                </svg>
                            @elseif($card['icon'] === 'groups')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3.75 3.75 0 0112 15.75c1.25 0 2.42.494 3.293 1.352m-6.586-8.552a3.75 3.75 0 016.586 0M6 6.878A3.75 3.75 0 0112 3c1.25 0 2.42.494 3.293 1.352m-6.586 8.552a3.75 3.75 0 00-6.586 0M12 12.75a3.75 3.75 0 01-6.586 0M18 15.75a3.75 3.75 0 01-6.586 0" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4"><button wire:click="redirectTo('{{ $card['route'] }}')"
                            class="text-sm font-semibold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:text-{{ $card['color'] }}-800">Ver
                            {{ $card['title'] }} &rarr;</button></div>
                </div>
            @endforeach
        </div>

        {{-- Seção de Cursos e Gráficos --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-5 gap-8">
            <div
                class="lg:col-span-2 rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Cursos Ativos com Alerta</h3>
                <div class="max-h-96 overflow-y-auto pr-2">
                    <ul class="space-y-3">
                        @forelse ($cursosAtivos as $curso)
                            <li class="p-3 rounded-lg {{ $loop->even ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="truncate">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $curso['nome'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $curso['presidio'] }}
                                        </p>
                                    </div>
                                    <div class="text-right ml-2 flex-shrink-0">
                                        @if ($curso['alert_color'])
                                            <span
                                                class="h-3 w-3 rounded-full bg-{{ $curso['alert_color'] }}-500 inline-block"
                                                title="Próximo do fim"></span>
                                        @endif
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $curso['fim'] }}</p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum curso ativo.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div
                class="lg:col-span-3 rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Cursos por Presídio</h3>
                <div class="flex-grow"><canvas id="cursosPorPresidioChart"></canvas></div>
            </div>
        </div>

        <div class="mt-8">
            <div class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg ring-1 ring-black/5 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Formaturas por Mês</h3>
                <div class="h-72"><canvas id="formaturasPorMesChart"></canvas></div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let cursosChart = null;
                let formaturasChart = null;
                const chartDefaultOptions = (isDarkMode, indexAxis = 'x') => ({
                    indexAxis: indexAxis,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: isDarkMode ? '#9ca3af' : '#4b5563'
                            }
                        },
                        x: {
                            grid: {
                                display: indexAxis === 'y'
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

                const initCursosChart = () => {
                    const ctx = document.getElementById('cursosPorPresidioChart');
                    if (!ctx) return;
                    if (cursosChart) cursosChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    cursosChart = new window.Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($chartCursosPorPresidio['labels']),
                            datasets: [{
                                label: 'Cursos',
                                data: @json($chartCursosPorPresidio['data']),
                                backgroundColor: isDarkMode ? 'rgba(168, 85, 247, 0.6)' :
                                    'rgba(168, 85, 247, 0.5)',
                                borderColor: 'rgba(168, 85, 247, 1)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode, 'y')
                    });
                };
                const initFormaturasChart = () => {
                    const ctx = document.getElementById('formaturasPorMesChart');
                    if (!ctx) return;
                    if (formaturasChart) formaturasChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    formaturasChart = new window.Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($chartFormaturasPorMes['labels']),
                            datasets: [{
                                label: 'Formaturas',
                                data: @json($chartFormaturasPorMes['data']),
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode, 'x')
                    });
                };
                Livewire.on('dashboardRefreshed', () => {
                    setTimeout(() => {
                        initCursosChart();
                        initFormaturasChart();
                    }, 10);
                });
                initCursosChart();
                initFormaturasChart();
            });
        </script>
    @endpush
</div>
