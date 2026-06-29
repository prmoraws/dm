@section('title', 'Regiões')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.707 4.586L4.586 7.707M16.293 4.586l3.121 3.121M12 21a9 9 0 110-18 9 9 0 010 18z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Regiões') }}
        </h2>
    </div>
</x-slot>

<div>
    <!-- Main Content -->
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Mensagem de Sucesso -->
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-4"
                    class="bg-teal-50 dark:bg-teal-900 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600 dark:text-teal-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        </div>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <!-- Search and Add Button -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

                    <!-- Campo de Pesquisa (sem botão) -->
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                            placeholder="Buscar por Região ou Bloco...">
                    </div>

                    <!-- Botão criar novo -->
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Nova Região
                    </button>
                </div>

                <!-- Modal de Criar/Editar -->
                @if ($isOpen)
                    <div wire:key="create-edit-modal" x-data="{ open: @json($isOpen) }" x-show="open"
                        x-on:keydown.escape.window="open && $wire.closeModal()"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90" @click.self="$wire.closeModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md flex items-center justify-center z-50">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                                {{ $regiao_id ? 'Editar Região' : 'Criar Região' }}
                            </h2>
                            <form wire:submit.prevent="store" class="space-y-6">
                                <div>
                                    <label for="nome"
                                        class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Nome da
                                        Região</label>
                                    <input type="text" id="nome" wire:model.defer="nome"
                                        class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                        placeholder="Digite o nome da região" required>
                                    @error('nome')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="bloco_id"
                                        class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Bloco</label>
                                    <select id="bloco_id" wire:model.defer="bloco_id"
                                        class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                        required>
                                        <option value="">-- Selecione um bloco --</option>
                                        @foreach ($blocos as $bloco)
                                            <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('bloco_id')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 focus:outline-none focus:shadow-outline">
                                        {{ $regiao_id ? 'Atualizar' : 'Criar' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Modal de Confirmação de Exclusão -->
                @if ($confirmDeleteId)
                    <div wire:key="delete-modal" x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="open && $wire.set('confirmDeleteId', null)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        @click.self="$wire.set('confirmDeleteId', null)"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md flex items-center justify-center z-50">
                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 md:p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Confirmar Exclusão</h2>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Tem certeza de que deseja excluir
                                esta região? Esta ação é irreversível.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                    Cancelar
                                </button>
                                <button wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg flex items-center gap-2 transition-all duration-150 focus:outline-none focus:shadow-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                    </svg>
                                    Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabela para Desktop -->
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead>
                            <tr
                                class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Bloco</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $regiao)
                                <tr wire:key="regiao-{{ $regiao->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 animate-slide-up"
                                    style="--delay: {{ (($index % 20) + 1) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left">{{ $regiao->id }}</td>
                                    <td class="py-3 px-6 text-left">{{ $regiao->nome }}</td>
                                    <td class="py-3 px-6 text-left">{{ $regiao->bloco->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button wire:click="edit({{ $regiao->id }})"
                                                class="w-4 transform hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Editar região">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $regiao->id }})"
                                                class="w-4 transform hover:text-red-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Excluir região">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhuma região encontrada para o termo "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Lista de Cartões para Mobile -->
                <div class="md:hidden space-y-4">
                    @forelse ($results as $index => $regiao)
                        <div wire:key="regiao-card-{{ $regiao->id }}" x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-4"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ (($index % 20) + 1) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">ID:
                                        {{ $regiao->id }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $regiao->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Bloco:
                                        {{ $regiao->bloco->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <button wire:click="edit({{ $regiao->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Editar região">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $regiao->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Excluir região">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma região encontrada para o termo "{{ $search }}".
                        </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if ($results->hasPages())
                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Inline Styles -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out var(--delay) forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</div>
