@section('title', 'Gerenciar Times')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 1.857a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Gestão Avançada de Times') }}
        </h2>
    </div>
</x-slot>

<div>
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message') || session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                    class="rounded-lg shadow-lg my-6 p-4 border-l-4 {{ session()->has('message') ? 'bg-teal-50 dark:bg-teal-900/50 border-teal-500' : 'bg-red-50 dark:bg-red-900/50 border-red-500' }}">
                    <p
                        class="text-sm font-medium {{ session()->has('message') ? 'text-teal-800 dark:text-teal-200' : 'text-red-800 dark:text-red-200' }}">
                        {{ session('message') ?? session('error') }}
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Buscar por nome do time..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-200">
                    </div>
                    <button wire:click="create()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Novo Time
                    </button>
                </div>

                <!-- Modal Criar / Editar Time -->
                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 p-4 flex items-center justify-center overflow-y-auto">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6 my-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                                {{ $team_id ? 'Editar Propriedades do Time' : 'Criar Novo Time' }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Configure o nome e quem terá acesso
                                total (Dono) sobre as configurações deste time no Jetstream.</p>

                            <form wire:submit.prevent="store" class="space-y-4">
                                <div>
                                    <label for="name"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome do
                                        Time</label>
                                    <input type="text" wire:model.defer="name" id="name"
                                        class="w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600 dark:text-gray-100 focus:ring-blue-500"
                                        required>
                                    @error('name')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pt-2">
                                    <label for="owner_id"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dono do
                                        Time (Acesso Máximo)</label>
                                    <select wire:model.defer="owner_id" id="owner_id"
                                        class="w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600 dark:text-gray-100 focus:ring-blue-500"
                                        required>
                                        <option value="">Selecione o Usuário Proprietário...</option>
                                        @foreach ($allUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('owner_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div
                                    class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">Salvar
                                        Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Modal Exclusão -->
                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto border-t-4 border-red-500">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Aviso de Exclusão</h3>
                            <p class="my-4 text-gray-600 dark:text-gray-300">Tem certeza que deseja apagar
                                permanentemente este time? Usuários perderão seus perfis associados a ele e não poderão
                                acessar as rotas protegidas correspondentes.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">Cancelar</button>
                                <button wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Excluir
                                    Permanentemente</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabela para Desktop -->
                <div
                    class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-black/5">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="py-4 px-6 text-left">Nome do Time</th>
                                <th class="py-4 px-6 text-left">Proprietário (Dono)</th>
                                <th class="py-4 px-6 text-center">Nº de Membros</th>
                                <th class="py-4 px-6 text-center">Gerenciamento</th>
                            </tr>
                        </thead>
                        <tbody
                            class="text-gray-600 dark:text-gray-300 text-sm divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($teams as $index => $team)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-4 px-6 font-semibold flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                            {{ substr($team->name, 0, 1) }}
                                        </div>
                                        <span class="text-gray-900 dark:text-gray-100">{{ $team->name }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            @if ($team->owner)
                                                <img class="h-6 w-6 rounded-full object-cover border border-gray-300 dark:border-gray-600"
                                                    src="{{ $team->owner->profile_photo_url }}" alt="">
                                                <span
                                                    class="text-gray-700 dark:text-gray-300">{{ $team->owner->name }}</span>
                                            @else
                                                <span class="text-red-500 font-medium">Sem Dono Atribuído</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span
                                            class="px-3 py-1 font-semibold text-xs rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            {{ $team->users->count() }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-4">
                                            <button wire:click="edit({{ $team->id }})"
                                                class="text-gray-400 hover:text-blue-500 transform hover:scale-110 transition-all"
                                                title="Editar Propriedades">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            @if (strtolower(trim($team->name)) !== 'adm' && strtolower(trim($team->name)) !== 'admin')
                                                <button wire:click="confirmDelete({{ $team->id }})"
                                                    class="text-gray-400 hover:text-red-500 transform hover:scale-110 transition-all"
                                                    title="Excluir Time">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum time cadastrado no sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tabela Mobile -->
                <div class="block md:hidden space-y-4">
                    @forelse($teams as $index => $team)
                        <div wire:key="team-card-{{ $team->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                            {{ substr($team->name, 0, 1) }}
                                        </div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ $team->name }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Dono:</strong> {{ $team->owner?->name ?? 'Sem Dono' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Membros:</strong> {{ $team->users->count() }} usuários
                                    </p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="edit({{ $team->id }})"
                                        class="text-gray-400 hover:text-blue-500 bg-gray-50 dark:bg-gray-700 p-2 rounded-full">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    @if (strtolower(trim($team->name)) !== 'adm' && strtolower(trim($team->name)) !== 'admin')
                                        <button wire:click="confirmDelete({{ $team->id }})"
                                            class="text-gray-400 hover:text-red-500 bg-gray-50 dark:bg-gray-700 p-2 rounded-full">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 text-center text-gray-500">
                            Nenhum time encontrado.
                        </div>
                    @endforelse
                </div>

                @if ($teams->hasPages())
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 mt-4">{{ $teams->links() }}</div>
                @endif
            </div>
        </div>
    </div>
    <style>
        .animate-fade-in {
            animation: fadeIn .5s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            animation: slideUp .5s ease-out forwards;
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
