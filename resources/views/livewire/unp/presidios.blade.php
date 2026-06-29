@section('title', 'Presídios')

<x-slot name="header">
    {{-- CORREÇÃO: Cabeçalho com ícone e animação --}}
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.707 4.586L4.586 7.707m11.707-3.121l-3.121 3.121M12 21a9 9 0 110-18 9 9 0 010 18z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Presídios') }}
        </h2>
    </div>
</x-slot>

<div>
    {{-- CORREÇÃO: Gradiente de fundo --}}
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
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">&times;</button>
                    </div>
                </div>
            @endif

            {{-- Card Principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Buscar por nome, diretor ou adjunto...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Incluir Presídio
                    </button>
                </div>

                @if ($isOpen)
                    {{-- CORREÇÃO: Alinhamento do modal e cores dos inputs --}}
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl mx-4 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $presidio_id ? 'Editar Presídio' : 'Criar Presídio' }}</h2>
                            <form wire:submit.prevent="save" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nome"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Nome
                                            do Presídio</label>
                                        <input type="text" id="nome" wire:model.defer="nome"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300"
                                            required>
                                        @error('nome')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="diretor"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Diretor</label>
                                        <input type="text" id="diretor" wire:model.defer="diretor"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300"
                                            required>
                                        @error('diretor')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="contato_diretor"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Contato
                                            do Diretor</label>
                                        <input type="text" id="contato_diretor" wire:model.defer="contato_diretor"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300"
                                            required>
                                        @error('contato_diretor')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="adjunto"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Diretor
                                            Adjunto</label>
                                        <input type="text" id="adjunto" wire:model.defer="adjunto"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label for="contato_adjunto"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Contato
                                            do Adjunto</label>
                                        <input type="text" id="contato_adjunto" wire:model.defer="contato_adjunto"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label for="laborativa"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Responsável
                                            Laborativa</label>
                                        <input type="text" id="laborativa" wire:model.defer="laborativa"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                    </div>
                                    <div>
                                        <label for="contato_laborativa"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Contato
                                            Laborativa</label>
                                        <input type="text" id="contato_laborativa"
                                            wire:model.defer="contato_laborativa"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                    </div>
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div>
                                        <label for="visita"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Detalhes
                                            da Visita</label>
                                        <textarea id="visita" wire:model.defer="visita" rows="4"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300"></textarea>
                                    </div>
                                    <div>
                                        <label for="interno"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Trabalho
                                            Interno</label>
                                        <textarea id="interno" wire:model.defer="interno" rows="4"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300"></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-6">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">{{ $presidio_id ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedPresidio)
                    {{-- CORREÇÃO: Alinhamento do modal --}}
                    <div wire:key="view-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeViewModal()">
                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl mx-4 p-6 md:p-8">
                            <div
                                class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col">
                                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                        {{ $selectedPresidio->nome }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Detalhes do Presídio</p>
                                </div>
                                <button wire:click="closeViewModal" type="button"
                                    class="p-2 -m-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700"><span
                                        class="sr-only">Fechar</span><svg class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-sm">
                                <p><strong class="text-gray-500 dark:text-gray-400">Diretor:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->diretor }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Contato Diretor:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->contato_diretor }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Adjunto:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->adjunto ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Contato Adjunto:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->contato_adjunto ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Laborativa:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->laborativa ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Contato Laborativa:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedPresidio->contato_laborativa ?? 'N/A' }}</span>
                                </p>
                                <div class="md:col-span-2 pt-2">
                                    <p class="font-semibold text-gray-500 dark:text-gray-400">Visita:</p>
                                    <p class="text-gray-800 dark:text-gray-300 mt-1 whitespace-pre-wrap">
                                        {{ $selectedPresidio->visita ?: 'Não informado' }}</p>
                                </div>
                                <div class="md:col-span-2 pt-2">
                                    <p class="font-semibold text-gray-500 dark:text-gray-400">Trabalho Interno:</p>
                                    <p class="text-gray-800 dark:text-gray-300 mt-1 whitespace-pre-wrap">
                                        {{ $selectedPresidio->interno ?: 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                  {{-- INÍCIO DO MODAL DE EXCLUSÃO IMPLEMENTADO --}}
                @if ($confirmDeleteId)
                    <div wire:key="delete-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4"
                        @click.self="$wire.set('confirmDeleteId', null)">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este presídio? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                <button wire:click="delete" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg flex items-center gap-2">Sim, Excluir</button>
                            </div>
                        </div>
                    </div>
                @endif
                

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('nome')">Nome</th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('diretor')">Diretor
                                </th>
                                <th class="py-3 px-6 text-left">Contato</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($presidios as $index => $presidio)
                                <tr wire:key="presidio-{{ $presidio->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left">{{ $presidio->nome }}</td>
                                    <td class="py-3 px-6 text-left">{{ $presidio->diretor }}</td>
                                    <td class="py-3 px-6 text-left">{{ $presidio->contato_diretor }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $presidio->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $presidio->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $presidio->id }})"
                                                class="w-5 transform hover:text-red-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                                </svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhum presídio
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse ($presidios as $index => $presidio)
                        <div wire:key="presidio-card-{{ $presidio->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $presidio->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Diretor:
                                        {{ $presidio->diretor }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $presidio->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $presidio->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $presidio->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                        </svg></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhum presídio encontrado.</div>
                    @endforelse
                </div>

                @if ($presidios->hasPages())
                    <div class="pt-4">{{ $presidios->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Animações --}}
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
