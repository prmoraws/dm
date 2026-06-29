<div>
    <x-slot name="header">
    <div 
        x-data="{ generating: false }"
        class="flex justify-between items-center"
    >
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Espelho Político: {{ $cidade->nome }}
        </h2>
        <div class="flex space-x-2">
            <a href="{{ route('politica.espelho.edit', $cidade) }}" wire:navigate class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                Editar Espelho
            </a>
            <button 
            @click="generating = true; generatePdf('{{ $cidade->nome }}').finally(() => generating = false)"
            :disabled="generating"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-wait flex items-center"
        >
            <svg x-show="generating" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="generating ? 'Gerando...' : 'Gerar PDF'">Gerar PDF</span>
        </button>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div id="report-content" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">

                {{-- Seção de Dados Políticos (do Espelho) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-900 dark:text-white">Presidente Local</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $cidade->espelho?->presidente_local ?? 'Não informado' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-900 dark:text-white">Indicação</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $cidade->espelho?->indicacao_bispo ?? 'Não informado' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <h3 class="font-bold text-gray-900 dark:text-white">Nº de Filiações</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $cidade->espelho?->filiados_republicanos ?? 'Não informado' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Prefeito Atual</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $cidade->espelho?->prefeito_atual_nome ?? 'N/A' }} ({{$cidade->espelho?->prefeito_atual_partido ?? 'N/A' }})</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">População</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($cidade->populacao ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Votos do Prefeito</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($cidade->espelho?->prefeito_atual_votos ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Cadeiras na Câmara</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $cidade->cadeiras_camara ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

              {{-- Seção de Votações --}}
                <div class="space-y-8">
                    {{-- Resultados para Deputados Federais --}}
                    @if (!empty($federalDeputiesStats))
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                                Votação: Deputados Federais de Interesse (2022)
                            </h3>
                            <div class="mt-4 space-y-4">
                                @foreach($federalDeputiesStats as $stat)
                                    @include('livewire.politica.partials.voting-stats-accordion', ['stat' => $stat])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Resultados para Deputados Estaduais --}}
                    @if (!empty($stateDeputiesStats))
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                                Votação: Deputados Estaduais de Interesse (2022)
                            </h3>
                            <div class="mt-4 space-y-4">
                                @foreach($stateDeputiesStats as $stat)
                                    @include('livewire.politica.partials.voting-stats-accordion', ['stat' => $stat])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Resultados para Vereadores --}}
                    @if (!empty($councilorsStats))
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                                Votação: Vereadores de Interesse (2024)
                            </h3>
                            <div class="mt-4 space-y-4">
                                @foreach($councilorsStats as $stat)
                                    @include('livewire.politica.partials.voting-stats-accordion', ['stat' => $stat])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                {{-- Seção de Observações --}}
                <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">Observações</h3>
                    <div class="mt-2 prose dark:prose-invert max-w-none">
                       <p>{{ $cidade->espelho?->observacoes ?? 'Nenhuma observação cadastrada.' }}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
