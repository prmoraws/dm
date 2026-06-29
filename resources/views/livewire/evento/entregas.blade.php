@section('title', 'Distribuição de Cestas')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m16 0l-4 4m4-4l-4-4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12l4 4m-4-4l4-4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Distribuição de Cestas') }}
        </h2>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

            <!-- Mensagens de Feedback -->
            @if ($errorMessage)
                <div class="bg-red-100 border-t-4 border-red-500 rounded-b text-red-900 dark:text-white px-4 py-3 shadow-md my-3"
                    role="alert">
                    <p class="text-sm">{{ $errorMessage }}</p>
                </div>
            @endif

            <!-- Campo de Pesquisa -->
            <div class="my-4">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar por nome..."
                    class="w-full md:w-1/3 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                    aria-label="Pesquisar por nome" />
            </div>

            <!-- Tabela de Resultados Unificada -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-sm">
                            <th wire:click="sortBy('nome')" class="p-3 text-left cursor-pointer">Nome @if ($sortField === 'nome')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('tipo')" class="p-3 text-left cursor-pointer">Tipo @if ($sortField === 'tipo')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('cestas')" class="p-3 text-center cursor-pointer">Cestas Entregues
                                @if ($sortField === 'cestas')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('convidados')" class="p-3 text-center cursor-pointer">Convidados
                                @if ($sortField === 'convidados')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </th>
                            <th class="p-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300">
                        @forelse($results as $item)
                            <tr wire:key="{{ $item->tipo }}-{{ $item->id }}"
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="p-3 text-left truncate max-w-xs" title="{{ $item->nome }}">
                                    {{ $item->nome }}</td>
                                <td class="p-3 text-left">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->tipo === 'terreiro' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $item->tipo }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    @if ($item->cestas)
                                        <span class="font-semibold">{{ $item->cestas }}</span>
                                    @else
                                        <span class="text-red-500 font-bold" title="Não entregue">!</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">{{ $item->convidados }}</td>
                                <td class="p-3 text-center">
                                    <button wire:click="viewDetails({{ $item->id }}, '{{ $item->tipo }}')"
                                        class="bg-blue-500 hover:bg-blue-700 text-white text-sm py-1 px-2 rounded flex items-center gap-1 mx-auto"
                                        aria-label="Visualizar detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-gray-800">
                                <td colspan="5" class="p-3 text-center text-gray-500 dark:text-gray-400">Nenhum
                                    registro encontrado para "{{ $search }}".</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="mt-4">
                {{ $results->links() }}
            </div>

            <!-- Modal de Visualização -->
            @if ($selectedEntidade)
                <div wire:key="view-modal" x-data="{ open: true }" x-show="open"
                    x-on:keydown.escape.window="open && $wire.closeModal()"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click.self="$wire.closeModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 overflow-y-auto p-4">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-3xl mx-auto my-8 p-6 md:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                {{ $selectedEntidade['nome'] }}</h2>
                            <button wire:click="closeModal"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"><svg
                                    xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg></button>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-md font-semibold text-blue-600 dark:text-blue-400 mb-2">Detalhes da
                                    Entrega</h4>
                                <div class="border-t-2 border-blue-600 dark:border-blue-400 my-2"></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Cestas Recebidas:</strong>
                                    {{ $selectedEntidade['cestas'] ?? 'Nenhuma entrega registrada' }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Convidados
                                        Esperados:</strong> {{ $selectedEntidade['convidados'] ?? '0' }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Observação:</strong>
                                    {{ $selectedEntidade['observacao'] ?? 'Nenhuma' }}</p>
                            </div>
                            @if ($selectedEntidade['foto'])
                                <div>
                                    <h4 class="text-md font-semibold text-blue-600 dark:text-blue-400 mb-2">Foto da
                                        Entrega</h4>
                                    <div class="border-t-2 border-blue-600 dark:border-blue-400 my-2"></div>
                                    <img src="{{ url($selectedEntidade['foto']) }}" alt="Foto da entrega"
                                        class="mt-2 w-full max-h-[60vh] object-contain rounded mx-auto">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
