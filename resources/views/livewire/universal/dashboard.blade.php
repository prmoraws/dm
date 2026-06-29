@section('title', 'Dashboard Universal')

<div class="bg-slate-50 dark:bg-gray-900/90 min-h-screen pt-28 pb-10 sm:pt-28 sm:pb-12" x-data="{}"
    x-init="$watch('darkMode', val => {
        @this.dispatch('dashboardRefreshed');
    })">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white sm:truncate">
                    Dashboard Universal
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Visão geral e consolidada dos dados do
                    sistema.</p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <button wire:click="refreshDashboard" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                    <svg wire:loading wire:target="refreshDashboard" class="animate-spin -ml-1 mr-2 h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="refreshDashboard" xmlns="http://www.w3.org/2000/svg"
                        class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd" />
                    </svg>
                    Atualizar
                </button>
                <button wire:click="exportData" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                    {{-- ... ícone de loading ... --}}
                    <span>Exportar Dados</span>
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
                    'title' => 'Pessoas',
                    'value' => $pessoasCount,
                    'label' => 'CADASTROS',
                    'route' => 'pessoas',
                    'color' => 'blue',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Pastores',
                    'value' => $pastoresCount,
                    'label' => 'CADASTROS',
                    'route' => 'pastores',
                    'color' => 'sky',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Igrejas',
                    'value' => $igrejasCount,
                    'label' => 'CADASTROS',
                    'route' => 'igrejas',
                    'color' => 'purple',
                    'icon' => 'church',
                ],
                [
                    'title' => 'Regiões',
                    'value' => $regioesCount,
                    'label' => 'CADASTROS',
                    'route' => 'regiaos',
                    'color' => 'amber',
                    'icon' => 'map',
                ],
                [
                    'title' => 'Blocos',
                    'value' => $blocosCount,
                    'label' => 'CADASTROS',
                    'route' => 'blocos',
                    'color' => 'rose',
                    'icon' => 'blocks',
                ],
                [
                    'title' => 'Banners',
                    'value' => $bannersCount,
                    'label' => 'CADASTROS',
                    'route' => 'banners',
                    'color' => 'indigo',
                    'icon' => 'banner',
                ],
                [
                    'title' => 'Categorias',
                    'value' => $categoriasCount,
                    'label' => 'CADASTROS',
                    'route' => 'categorias',
                    'color' => 'emerald',
                    'icon' => 'category',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 hover:shadow-2xl dark:hover:shadow-gray-900 transition-all duration-300 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $card['title'] }}</h3>
                            <p class="mt-2">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value']) }}</span>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-1">{{ $card['label'] }}</span>
                            </p>
                        </div>
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-{{ $card['color'] }}-500/10">
                            {{-- Ícones --}}
                            @if ($card['icon'] === 'users')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                                </svg>
                            @elseif($card['icon'] === 'church')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            @elseif($card['icon'] === 'map')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            @elseif($card['icon'] === 'blocks')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125A1.125 1.125 0 003 5.625v12.75c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            @elseif($card['icon'] === 'banner')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 3.75l-4.5 4.5m0 0l-4.5 4.5M11.25 8.25h6.75a2.25 2.25 0 012.25 2.25v6.75a2.25 2.25 0 01-2.25 2.25H3.75a2.25 2.25 0 01-2.25-2.25V3.75a2.25 2.25 0 012.25-2.25h6.75" />
                                </svg>
                            @elseif($card['icon'] === 'category')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 9.75h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M21 4.5h-18a2.25 2.25 0 00-2.25 2.25v10.5a2.25 2.25 0 002.25 2.25h18a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <button wire:click="redirectTo({{ Js::from($card['route']) }})"
                            class="text-sm font-semibold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:text-{{ $card['color'] }}-800 dark:hover:text-{{ $card['color'] }}-200 transition-colors">
                            Ver {{ $card['title'] }} &rarr;
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Pessoas por Bloco --}}
            <div class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Pessoas por Bloco
                </h3>

                <div class="max-h-96 overflow-y-auto">
                    <ul class="space-y-2">
                        @foreach ($pessoasPorBloco as $item)
                            <li class="flex justify-between text-gray-700 dark:text-gray-200">
                                <span>{{ $item['bloco'] }}</span>
                                <span class="font-bold">{{ $item['total_pessoas'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Pessoas por Igreja --}}
            <div class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    Pessoas por Igreja
                </h3>

                <div class="max-h-96 overflow-y-auto">
                    <ul class="space-y-2">
                        @foreach ($pessoasPorIgreja as $item)
                            <li class="flex justify-between text-gray-700 dark:text-gray-200">
                                <span>{{ $item['igreja'] }}</span>
                                <span class="font-bold">{{ $item['total_pessoas'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div
                class="lg:col-span-2 rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Pessoas por Região</h3>
                <div class="flex-grow">
                    <canvas id="pessoasPorRegiaoChart"></canvas>
                </div>
            </div>
            
            <div
                class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Igrejas por Região</h3>
                <div class="max-h-80 overflow-y-auto pr-2">
                    <ul class="space-y-3">
                        @forelse ($igrejasPorRegiao as $item)
                            <li class="flex justify-between items-center text-sm">
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item['regiao'] }}</span>
                                <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $item['total_igrejas'] }} igrejas</span>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma região com igrejas.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- AJUSTE: Card do gráfico de banners substituído pelo de igrejas por bloco --}}
        <div class="mt-8">
            <div
                class="rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Distribuição de Igrejas por
                    Bloco
                </h3>
                <div class="h-80 md:h-96">
                    <canvas id="igrejasPorBlocoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let pessoasChart = null;
                let igrejasChart = null; // Variável para o novo gráfico

                const chartDefaultOptions = (isDarkMode) => ({
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: isDarkMode ? '#9ca3af' : '#4b5563'
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

                const initPessoasChart = () => {
                    const ctx = document.getElementById('pessoasPorRegiaoChart');
                    if (!ctx) return;
                    if (pessoasChart) pessoasChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    pessoasChart = new window.Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($chartPessoasPorRegiao['labels']),
                            datasets: [{
                                label: 'Pessoas',
                                data: @json($chartPessoasPorRegiao['data']),
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }]
                        },
                        options: chartDefaultOptions(isDarkMode)
                    });
                };

                // AJUSTE: Função para o novo gráfico de Igrejas por Bloco
                const initIgrejasPorBlocoChart = () => {
                    const ctx = document.getElementById('igrejasPorBlocoChart');
                    if (!ctx) return;
                    if (igrejasChart) igrejasChart.destroy();
                    const isDarkMode = document.documentElement.classList.contains('dark');

                    igrejasChart = new window.Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: @json($chartIgrejasPorBloco['labels']),
                            datasets: [{
                                label: 'Igrejas',
                                data: @json($chartIgrejasPorBloco['data']),
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.7)', 'rgba(236, 72, 153, 0.7)',
                                    'rgba(168, 85, 247, 0.7)', 'rgba(34, 197, 94, 0.7)',
                                    'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)',
                                    'rgba(14, 165, 233, 0.7)', 'rgba(217, 70, 239, 0.7)',
                                    'rgba(132, 204, 22, 0.7)', 'rgba(249, 115, 22, 0.7)',
                                    'rgba(99, 102, 241, 0.7)', 'rgba(20, 184, 166, 0.7)'
                                ],
                                hoverOffset: 8,
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        color: isDarkMode ? '#9ca3af' : '#4b5563'
                                    }
                                }
                            }
                        }
                    });
                };

                Livewire.on('dashboardRefreshed', () => {
                    setTimeout(() => {
                        initPessoasChart();
                        initIgrejasPorBlocoChart(); // Chama a função do novo gráfico
                    }, 10);
                });

                // Inicia os gráficos na carga da página
                initPessoasChart();
                initIgrejasPorBlocoChart(); // Chama a função do novo gráfico
            });
        </script>
    @endpush
</div>
