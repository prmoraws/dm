@section('title', 'Dashboard de Eventos')

{{-- 
    AJUSTE 1: Correção do espaçamento do topo.
    A classe 'py-10 sm:py-12' foi substituída por 'pt-28 pb-10 sm:pt-28 sm:pb-12'.
    O 'pt-28' (padding-top) compensa a altura da barra de navegação fixa (h-16 = 4rem) 
    e adiciona um espaçamento para o conteúdo, resolvendo a sobreposição.
--}}
<div class="bg-slate-50 dark:bg-gray-900/90 min-h-screen pt-28 pb-10 sm:pt-28 sm:pb-12" x-data="{}"
    x-init="$watch('darkMode', val => {
        @this.dispatch('dashboardRefreshed');
    });">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white sm:truncate">
                    Dashboard de Eventos
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Visão geral e consolidada dos dados do evento.
                </p>
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
                    <svg wire:loading wire:target="exportData" class="animate-spin -ml-1 mr-2 h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="exportData">Exportar Relatório</span>
                    <span wire:loading wire:target="exportData">Exportando...</span>
                </button>
            </div>
        </div>

        @if ($message)
            <div x-data="{ show: true }" x-show="show" x-transition.leave.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="mb-6 rounded-md p-4 {{ str_contains($message, 'Erro') ? 'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-300' : 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-300' }}">
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $cards = [
                    [
                        // AJUSTE 2: Título do card alterado
                        'title' => 'Terreiros',
                        'icon' => 'users',
                        'data' => [
                            ['value' => $terreirosCount, 'label' => 'TERREIROS'],
                            ['value' => $convidadosCount, 'label' => 'CONVIDADOS'],
                        ],
                        'button' => ['route' => 'terreiros', 'label' => 'Ver Terreiros'],
                        'color' => 'blue',
                    ],
                    [
                        'title' => 'Instituições',
                        'icon' => 'office-building',
                        'data' => [
                            ['value' => $instituicoesCount, 'label' => 'INSTITUIÇÕES'],
                            ['value' => $instituicoesConvidados, 'label' => 'CONVIDADOS'],
                        ],
                        'button' => ['route' => 'instituicoes', 'label' => 'Ver Instituições'],
                        'color' => 'purple',
                    ],
                    [
                        'title' => 'Cestas Entregues',
                        'icon' => 'shopping-cart',
                        'data' => [['value' => $totalCestas, 'label' => 'TOTAL DE CESTAS']],
                        'button' => ['route' => 'cestas', 'label' => 'Ver Entregas'],
                        'color' => 'green',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 hover:shadow-2xl dark:hover:shadow-gray-900 transition-all duration-300 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $card['title'] }}</h3>
                            @foreach ($card['data'] as $item)
                                <p class="mt-2">
                                    <span
                                        class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($item['value']) }}</span>
                                    <span
                                        class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-1">{{ $item['label'] }}</span>
                                </p>
                            @endforeach
                        </div>
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-{{ $card['color'] }}-500/10">
                            @if ($card['icon'] === 'users')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3.75 3.75 0 0112 15.75c1.25 0 2.42.494 3.293 1.352m-6.586-8.552a3.75 3.75 0 016.586 0M6 6.878A3.75 3.75 0 0112 3c1.25 0 2.42.494 3.293 1.352m-6.586 8.552a3.75 3.75 0 00-6.586 0M12 12.75a3.75 3.75 0 01-6.586 0M18 15.75a3.75 3.75 0 01-6.586 0" />
                                </svg>
                            @elseif($card['icon'] === 'office-building')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6h1.5m-1.5 3h1.5m-1.5 3h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            @elseif($card['icon'] === 'shopping-cart')
                                <svg class="h-6 w-6 text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.821-6.831M15 1.5a3 3 0 00-3 3h3V1.5z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    @if (isset($card['button']))
                        <div class="mt-4">
                            <button wire:click="redirectTo('{{ $card['button']['route'] }}')"
                                class="text-sm font-semibold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 hover:text-{{ $card['color'] }}-800 dark:hover:text-{{ $card['color'] }}-200 transition-colors">
                                {{ $card['button']['label'] }} &rarr;
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-5 gap-8">

            <div class="lg:col-span-2 grid grid-cols-1 gap-6 content-start">
                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                        Convidados por Bloco</h3>
                    <div class="max-h-40 overflow-y-auto pr-2">
                        <ul class="space-y-2">
                            @forelse ($blocosConvidados as $bloco => $total)
                                <li class="flex justify-between items-center text-sm">
                                    <span
                                        class="font-medium text-gray-800 dark:text-gray-200">{{ $bloco }}</span>
                                    <span
                                        class="font-semibold text-gray-600 dark:text-gray-300">{{ number_format($total) }}
                                        convidados</span>
                                </li>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum bloco encontrado.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div
                    class="relative overflow-hidden rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                        Consolidado Geral</h3>
                    <ul class="space-y-3">
                        @php
                            $consolidado = [
                                ['label' => 'Total Terreiros', 'value' => $terreirosCount],
                                ['label' => 'Total Instituições', 'value' => $instituicoesCount],
                                ['label' => 'Total Convidados (Terreiros)', 'value' => $convidadosCount],
                                ['label' => 'Total Convidados (Instituições)', 'value' => $instituicoesConvidados],
                                [
                                    'label' => 'Total Geral de Entidades',
                                    'value' => $totalGeralTerreirosInstituicoes,
                                    'bold' => true,
                                ],
                                [
                                    'label' => 'Total Geral de Convidados',
                                    'value' => $totalConvidadosGeral,
                                    'bold' => true,
                                ],
                            ];
                        @endphp
                        @foreach ($consolidado as $item)
                            <li
                                class="flex justify-between items-baseline text-sm @if ($item['bold'] ?? false) border-t border-gray-200 dark:border-gray-700 pt-3 @endif">
                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $item['label'] }}</span>
                                <span
                                    class="font-bold text-lg text-gray-900 dark:text-white">{{ number_format($item['value']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div
                class="lg:col-span-3 rounded-xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm shadow-lg dark:shadow-gray-900/50 ring-1 ring-black/5 p-6 flex flex-col">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Distribuição por Classe</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Quantidade de cestas entregues por tipo de
                    entidade.</p>
                <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="relative h-64 w-full">
                        <canvas id="distribuicaoClasseChart"></canvas>
                    </div>
                    <div class="space-y-4">
                        <p
                            class="flex justify-between items-baseline border-b border-gray-200 dark:border-gray-700 pb-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">TOTAL GERAL</span>
                            <span
                                class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCestas) }}</span>
                        </p>
                        @php
                            $distribuicaoData = [
                                ['label' => 'Terreiros', 'value' => $totalCestasTerreiros, 'color' => 'bg-sky-500'],
                                [
                                    'label' => 'Instituições',
                                    'value' => $totalCestasInstituicoes,
                                    'color' => 'bg-purple-500',
                                ],
                                ['label' => 'IURD', 'value' => $totalCestasIURD, 'color' => 'bg-amber-500'],
                            ];
                        @endphp
                        @foreach ($distribuicaoData as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="h-3 w-3 rounded-full {{ $item['color'] }} mr-3"></span>
                                    <span
                                        class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item['label'] }}</span>
                                </div>
                                <span
                                    class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ number_format($item['value']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('exportDataCompleted', (response) => {
                    if (response && response.url) {
                        const link = document.createElement('a');
                        link.href = response.url;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else if (response.error) {
                        alert('Erro ao exportar: ' + response.error);
                    }
                });

                let distribuicaoChart = null;
                const chartColors = {
                    sky: 'rgb(14, 165, 233)',
                    purple: 'rgb(168, 85, 247)',
                    amber: 'rgb(245, 158, 11)'
                };

                const initDistribuicaoChart = () => {
                    const ctx = document.getElementById('distribuicaoClasseChart');
                    if (!ctx) return;
                    if (distribuicaoChart) {
                        distribuicaoChart.destroy();
                    }

                    const isDarkMode = document.documentElement.classList.contains('dark');

                    distribuicaoChart = new window.Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: @json($chartDistribuicaoClasse['labels']),
                            datasets: [{
                                data: @json($chartDistribuicaoClasse['data']),
                                backgroundColor: [
                                    chartColors.sky,
                                    chartColors.purple,
                                    chartColors.amber,
                                ],
                                hoverOffset: 8,
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false, // Legenda customizada em HTML
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: isDarkMode ? '#1f2937' : '#ffffff',
                                    titleColor: isDarkMode ? '#f3f4f6' : '#111827',
                                    bodyColor: isDarkMode ? '#d1d5db' : '#374151',
                                    borderColor: isDarkMode ? '#374151' : '#e5e7eb',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed !== null) {
                                                label += new Intl.NumberFormat('pt-BR').format(context
                                                    .parsed);
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });
                };

                Livewire.on('dashboardRefreshed', () => {
                    initDistribuicaoChart();
                });

                initDistribuicaoChart();
            });
        </script>
    @endpush
</div>
