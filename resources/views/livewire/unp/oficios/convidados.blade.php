@section('title', 'Convidados')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        {{-- Ícone do Cabeçalho --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Convidados e Voluntários') }}
        </h2>
    </div>
</x-slot>

<div>
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">&times;</button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Buscar por nome ou CPF..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Convidado
                    </button>
                </div>

                {{-- MODAL DE CRIAR/EDITAR INTEGRADO E CORRIGIDO --}}
                @if ($isOpen)
                    <div wire:key="create-edit-convidado"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6"
                            x-data="{
                                applyMask(value, pattern) {
                                    let i = 0;
                                    const v = value.toString().replace(/\D/g, '');
                                    return pattern.replace(/[#]/g, () => v[i++] || '');
                                }
                            }">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $convidado_id ? 'Editar' : 'Criar' }} Convidado</h2>
                            <form wire:submit.prevent="store" class="space-y-4">
                                <div>
                                    <label for="nome"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome
                                        Completo</label>
                                    <input type="text" wire:model.defer="nome" id="nome"
                                        class="w-full uppercase  rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                    @error('nome')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cpf"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">CPF</label>
                                    <input type="text" wire:model.defer="cpf" id="cpf"
                                        x-on:input="$event.target.value = applyMask($event.target.value, '###.###.###-##')"
                                        class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600"
                                        placeholder="000.000.000-00" maxlength="14">
                                    @error('cpf')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="rg"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">RG
                                        (Opcional)</label>
                                    <input type="text" wire:model.defer="rg" id="rg"
                                        class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                    @error('rg')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="classe"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Classe</label>
                                    <select wire:model.defer="classe" id="classe"
                                        class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                        <option value="">Selecione...</option>
                                        @foreach ($classeOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('classe')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal()"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- INÍCIO DO MODAL DE VISUALIZAÇÃO (ADICIONADO) --}}
                @if ($isViewOpen && $selectedConvidado)
                    <div wire:key="view-convidado-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeViewModal()">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                Detalhes do Convidado
                            </h2>
                            <div class="space-y-4 text-gray-700 dark:text-gray-300">
                                <div>
                                    <span class="font-semibold">Nome:</span>
                                    <p>{{ $selectedConvidado->nome }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold">CPF:</span>
                                    <p>{{ $selectedConvidado->cpf }}</p>
                                </div>
                                @if ($selectedConvidado->rg)
                                    <div>
                                        <span class="font-semibold">RG:</span>
                                        <p>{{ $selectedConvidado->rg }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-semibold">Classe:</span>
                                    <p class="capitalize">{{ $selectedConvidado->classe }}</p>
                                </div>
                            </div>
                            <div class="flex justify-end pt-6 mt-6 border-t dark:border-gray-700">
                                <button type="button" wire:click="closeViewModal()"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Fechar</button>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div wire:key="delete-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4"
                        @click.self="$wire.set('confirmDeleteId', null)">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este
                                registro?
                            </p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg flex items-center gap-2">Excluir</button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tabela Desktop --}}
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">CPF</th>
                                <th class="py-3 px-6 text-left">Classe</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($results as $index => $convidado)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">{{ $convidado->nome }}</td>
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">{{ $convidado->cpf }}</td>
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300 capitalize">
                                        {{ $convidado->classe }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="edit({{ $convidado->id }})"
                                                class="w-5 transform text-gray-500 hover:text-blue-500"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $convidado->id }})"
                                                class="w-5 transform text-gray-500 hover:text-red-500"><svg
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
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum convidado
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards Mobile --}}
                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $convidado)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $convidado->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">CPF: {{ $convidado->cpf }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Classe: <span
                                            class="capitalize">{{ $convidado->classe }}</span></p>
                                </div>
                                <div class="flex flex-row items-center space-x-4 ml-4">
                                    {{-- BOTÃO DE VISUALIZAR ADICIONADO --}}
                                    <button wire:click="view({{ $convidado->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $convidado->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $convidado->id }})"
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
                            Nenhum convidado encontrado.</div>
                    @endforelse
                </div>

                @if ($results->hasPages())
                    <div class="pt-4">{{ $results->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn .5s ease-out forwards
        }

        .animate-slide-up {
            opacity: 0;
            animation: slideUp .5s ease-out forwards;
            animation-delay: var(--delay, 0s)
        }

        @keyframes fadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }
    </style>
</div>
