@section('title', 'Banners')

<x-slot name="header">
    {{-- Cabeçalho com ícone e animação --}}
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Banners') }}
        </h2>
    </div>
</x-slot>

<div>
    {{-- Gradiente de fundo adicionado --}}
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-4"
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-600 dark:text-teal-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}
                            </p>
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

            {{-- Card principal com estilo aprimorado --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                            placeholder="Buscar por nome do banner...">
                    </div>

                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Novo Banner
                    </button>
                </div>

                @if ($isOpen)
                    <div wire:key="create-edit-modal" x-data="{ open: @json($isOpen) }" x-show="open"
                        x-on:keydown.escape.window="$wire.closeModal()" x-transition @click.self="$wire.closeModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                                {{ $banner_id ? 'Editar Banner' : 'Criar Banner' }}
                            </h2>
                            <form wire:submit.prevent="store" class="space-y-6">
                                <div>
                                    <label for="nome"
                                        class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Nome
                                        do Banner</label>
                                    <input type="text" id="nome" wire:model.defer="nome"
                                        class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                        placeholder="Digite o nome do banner" required>
                                    @error('nome')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="descricao"
                                        class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Descrição</label>
                                    <textarea id="descricao" wire:model.defer="descricao" rows="4"
                                        class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                        placeholder="Digite uma breve descrição (opcional)"></textarea>
                                    @error('descricao')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="foto"
                                        class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Foto</label>
                                    <input id="foto" type="file" wire:model="foto"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900">
                                    <div wire:loading wire:target="foto" class="text-sm text-gray-500 mt-2">
                                        Carregando...</div>
                                    @error('foto')
                                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 focus:outline-none focus:shadow-outline">
                                        {{ $banner_id ? 'Atualizar' : 'Criar' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedBanner)
                    <div wire:key="view-modal" x-data="{ open: @json($isViewOpen), currentSlide: 0, slides: 3 }" x-show="open"
                        x-on:keydown.escape.window="$wire.closeViewModal()" x-transition @click.self="$wire.closeViewModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 md:p-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detalhes do Banner
                                </h3>
                                <button wire:click="closeViewModal"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                                    &times;
                                </button>
                            </div>
                            <div class="relative overflow-hidden h-64 flex items-center justify-center">
                                <div class="flex transition-transform duration-500 ease-in-out h-full"
                                    x-bind:style="'transform: translateX(-' + (currentSlide * 100) + '%)'">
                                    <div class="w-full flex-shrink-0 h-full">
                                        <div class="flex flex-col items-center justify-center h-full">
                                            @if ($selectedBanner->foto)
                                                <img src="{{ asset($selectedBanner->foto) }}"
                                                    alt="{{ $selectedBanner->nome }}"
                                                    class="max-w-full max-h-full object-contain rounded-lg">
                                            @else
                                                <p class="text-gray-500 dark:text-gray-400">Sem foto informada</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-full flex-shrink-0 h-full">
                                        <div
                                            class="flex flex-col items-center justify-center h-full text-center p-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Nome</p>
                                            <p
                                                class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                                                {{ $selectedBanner->nome }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full flex-shrink-0 h-full">
                                        <div
                                            class="flex flex-col items-center justify-center h-full text-center p-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Descrição</p>
                                            <p class="text-gray-700 dark:text-gray-300 mt-2">
                                                {{ $selectedBanner->descricao ?: 'Não informada' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-4">
                                <button @click="currentSlide = (currentSlide - 1 + slides) % slides"
                                    class="bg-gray-800 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <div class="flex justify-center space-x-2">
                                    <template x-for="i in slides" :key="i">
                                        <span @click="currentSlide = i - 1"
                                            class="h-2 w-2 rounded-full cursor-pointer"
                                            :class="currentSlide === (i - 1) ? 'bg-blue-500' : 'bg-gray-400'"></span>
                                    </template>
                                </div>
                                <button @click="currentSlide = (currentSlide + 1) % slides"
                                    class="bg-gray-800 bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($confirmDeleteId)
                    <div wire:key="delete-modal" x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                        @click.self="$wire.set('confirmDeleteId', null)"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md flex items-center justify-center z-50">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 md:p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Confirmar Exclusão
                                </h2>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Tem certeza de que deseja excluir
                                este banner? Esta ação é irreversível.</p>
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

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('nome')">Nome</th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('descricao')">
                                    Descrição</th>
                                <th class="py-3 px-6 text-left">Foto</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $banner)
                                <tr wire:key="banner-{{ $banner->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left">{{ $banner->nome }}</td>
                                    <td class="py-3 px-6 text-left truncate max-w-xs"
                                        title="{{ $banner->descricao }}">{{ $banner->descricao ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if ($banner->foto)
                                            <img src="{{ asset($banner->foto) }}" alt="{{ $banner->nome }}"
                                                class="h-10 w-16 object-cover rounded-md shadow-sm">
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $banner->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Visualizar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button wire:click="edit({{ $banner->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $banner->id }})"
                                                class="w-5 transform hover:text-red-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Excluir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum banner encontrado para o termo "{{ $searchTerm }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse ($results as $index => $banner)
                        <div wire:key="banner-card-{{ $banner->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $banner->nome }}</p>
                                    @if ($banner->foto)
                                        <img src="{{ asset($banner->foto) }}" alt="{{ $banner->nome }}"
                                            class="h-24 w-full object-cover rounded mt-2 shadow-sm">
                                    @endif
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $banner->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Visualizar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $banner->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $banner->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Excluir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500 dark:text-gray-400">
                            Nenhum banner encontrado.
                        </div>
                    @endforelse
                </div>

                @if ($results->hasPages())
                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bloco de estilos para as animações --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            animation: slideUp 0.5s ease-out forwards;
            animation-delay: var(--delay, 0s);
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