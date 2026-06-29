@section('title', 'Pastores')

<div>
    {{-- O conteúdo está agora dentro de um único div raiz para o Livewire --}}
    {{-- A classe py-12 e o padding-top (pt-16) corrigem a sobreposição do menu fixo --}}
    <div class="pt-16">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight mb-6 px-4 sm:px-0">
                    {{ __('Pastores') }}
                </h2>

                <!-- Mensagens de Feedback -->
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300"
                        role="alert">
                        <p class="text-sm font-medium">{{ session('message') }}</p>
                    </div>
                @endif
                @if (session()->has('error') || $errorMessage)
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="mb-6 p-4 rounded-lg bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300"
                        role="alert">
                        <p class="text-sm font-medium">{{ session('error') ?: $errorMessage }}</p>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <!-- Área de Controle: Pesquisa e Botão -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="w-full md:w-1/3">
                            <input wire:model.live.debounce.300ms="search" type="text"
                                placeholder="Buscar por Sede, Pastor, Esposa..."
                                class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                        </div>
                        <button wire:click="create"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Criar Pastor
                        </button>
                    </div>

                    <!-- Tabela para Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Sede</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Pastor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Esposa</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($results as $pastor)
                                    <tr wire:key="pastor-{{ $pastor->id }}"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $pastor->sede }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $pastor->pastor }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $pastor->esposa }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <button wire:click="view({{ $pastor->id }})"
                                                    class="text-green-600 hover:text-green-900 dark:hover:text-green-400"
                                                    aria-label="Visualizar"><svg class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg></button>
                                                <button wire:click="edit({{ $pastor->id }})"
                                                    class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                                                    aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg></button>
                                                <button wire:click="confirmDelete({{ $pastor->id }})"
                                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                    aria-label="Excluir"><svg class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                                        </path>
                                                    </svg></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Nenhum pastor encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
                        {{ $results->links() }}
                    </div>
                </div>

                <!-- Cards para Mobile -->
                <div class="md:hidden space-y-4 mt-6">
                    @forelse ($results as $pastor)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                        {{ $pastor->sede }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $pastor->pastor }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Esposa:
                                        {{ $pastor->esposa }}</p>
                                </div>
                                <div class="flex flex-col space-y-3 ml-4">
                                    <button wire:click="view({{ $pastor->id }})"
                                        class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $pastor->id }})"
                                        class="text-blue-500 hover:text-blue-700"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $pastor->id }})"
                                        class="text-red-500 hover:text-red-700"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                            </path>
                                        </svg></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhum pastor encontrado.</div>
                    @endforelse
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                </div>

                <!-- Modais -->
                @if ($isOpen)
                    <div wire:key="create-edit-modal" x-data="{ open: @json($isOpen) }" x-show="open"
                        x-on:keydown.escape.window="$wire.closeModal()" x-transition @click.self="$wire.closeModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6 md:p-8 mx-auto my-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                {{ $pastor_id ? 'Editar Pastor' : 'Criar Pastor' }}</h3>
                            <form wire:submit.prevent="store" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2"><label for="sede"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sede</label><input
                                            type="text" id="sede" wire:model.defer="sede"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200 p-2">
                                        @error('sede')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div><label for="pastor"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pastor</label><input
                                            type="text" id="pastor" wire:model.defer="pastor"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200 p-2">
                                        @error('pastor')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div><label for="telefone"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label><input
                                            type="text" id="telefone" wire:model.defer="telefone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200 p-2">
                                        @error('telefone')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div><label for="esposa"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Esposa</label><input
                                            type="text" id="esposa" wire:model.defer="esposa"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200 p-2">
                                        @error('esposa')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div><label for="tel_epos"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone
                                            da Esposa</label><input type="text" id="tel_epos"
                                            wire:model.defer="tel_epos"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-gray-200 p-2">
                                        @error('tel_epos')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4"><button type="button"
                                        wire:click="closeModal"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg">Cancelar</button><button
                                        type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-md">{{ $pastor_id ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedPastor)
                    <div wire:key="view-modal" x-data="{ open: @json($isViewOpen) }" x-show="open"
                        x-on:keydown.escape.window="$wire.closeViewModal()" x-transition
                        @click.self="$wire.closeViewModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6 md:p-8 mx-auto my-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $selectedPastor->pastor }}</h3>
                                <button wire:click="closeViewModal"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">&times;</button>
                            </div>
                            <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                <p><strong>Sede:</strong> {{ $selectedPastor->sede }}</p>
                                <p><strong>Esposa:</strong> {{ $selectedPastor->esposa }}</p>
                                <p><strong>Telefone Pastor:</strong> {{ $selectedPastor->telefone }}</p>
                                <p><strong>Telefone Esposa:</strong> {{ $selectedPastor->tel_epos }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div wire:key="delete-modal" x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                        @click.self="$wire.set('confirmDeleteId', null)"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <div class="flex items-center gap-3 mb-4"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este
                                registro? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg shadow-md flex items-center gap-2"><svg
                                        xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                    </svg>Apagar</button></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
