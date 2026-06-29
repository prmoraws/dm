<div>
    {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">Painel de Debug</p>
            <pre class="text-sm">{{ print_r($debugInfo, true) }}</pre>
        </div>
    </div> --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editando Espelho: {{ $cidade->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="space-y-6">
                            <div>
                                <x-label for="presidente_local" value="Presidente Local" />
                                <x-input id="presidente_local" type="text" class="mt-1 block w-full" wire:model="presidente_local" />
                                <x-input-error for="presidente_local" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="indicacao_bispo" value="Indicação" />
                                <x-input id="indicacao_bispo" type="text" class="mt-1 block w-full" wire:model="indicacao_bispo" />
                                <x-input-error for="indicacao_bispo" class="mt-2" />
                            </div>
                            <div>
                                <x-label for="filiados_republicanos" value="Nº de Filiações" />
                                <x-input id="filiados_republicanos" type="number" class="mt-1 block w-full" wire:model="filiados_republicanos" />
                                <x-input-error for="filiados_republicanos" class="mt-2" />
                            </div>
                             <div>
                                <x-label for="observacoes" value="Observações" />
                                <textarea id="observacoes" rows="8" class="form-input rounded-md shadow-sm mt-1 block w-full dark:bg-gray-900 dark:border-gray-700" wire:model="observacoes"></textarea>
                                <x-input-error for="observacoes" class="mt-2" />
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3 border border-gray-200 dark:border-gray-600">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Dados do Prefeito (Automático)</h3>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Prefeito Eleito</span>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $prefeito_atual_nome ?? 'Não importado' }} ({{$prefeito_atual_partido ?? 'N/A'}})</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Votos Recebidos</span>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format((int)($prefeito_atual_votos ?? 0), 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <x-label value="Deputados Federais de Interesse" />
                                <select multiple wire:model="selectedFederais" class="form-multiselect mt-1 block w-full h-32 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700">
                                    @forelse($deputadosFederaisDaCidade as $candidato)
                                        <option value="{{ $candidato->id }}">{{ $candidato->nome }}</option>
                                    @empty
                                        <option disabled>Nenhum Dep. Federal encontrado nesta cidade.</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <x-label value="Deputados Estaduais de Interesse" />
                                <select multiple wire:model="selectedEstaduais" class="form-multiselect mt-1 block w-full h-32 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700">
                                     @forelse($deputadosEstaduaisDaCidade as $candidato)
                                        <option value="{{ $candidato->id }}">{{ $candidato->nome }}</option>
                                    @empty
                                        <option disabled>Nenhum Dep. Estadual encontrado nesta cidade.</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <x-label value="Vereadores de Interesse" />
                                <select multiple wire:model="selectedVereadores" class="form-multiselect mt-1 block w-full h-48 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700">
                                     @forelse($vereadoresDaCidade as $candidato)
                                        <option value="{{ $candidato->id }}">{{ $candidato->nome }}</option>
                                    @empty
                                        <option disabled>Nenhum Vereador encontrado nesta cidade.</option>
                                    @endforelse
                                </select>
                                <p class="text-sm text-gray-500 mt-1">Segure Ctrl (ou Cmd) para selecionar mais de um.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <x-button>
                            Salvar Alterações
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>