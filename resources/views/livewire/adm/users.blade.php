@section('title', 'Gerenciar Usuários e Times')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Gerenciar Usuários e Times') }}
        </h2>
    </div>
</x-slot>

<div>
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Buscar por nome ou e-mail..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                </div>

                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 p-4 flex items-center justify-center">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Gerenciar Time do
                                Usuário</h2>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-bold">Usuário:</label>
                                    <p class="dark:text-gray-200">{{ $name }}</p>
                                </div>
                                <div><label class="block text-sm font-bold">E-mail:</label>
                                    <p class="dark:text-gray-200">{{ $email }}</p>
                                </div>
                                <div>
                                    <label for="team_id" class="block text-sm font-bold mb-2">Atribuir ao Time:</label>
                                    <select wire:model.defer="team_id" id="team_id"
                                        class="w-full rounded-md dark:bg-gray-800">
                                        @foreach ($allTeams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button>
                                    <button type="button" wire:click="updateUserTeam"
                                        class="bg-blue-600 text-white py-2 px-4 rounded-lg">Salvar Alterações</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabela para telas maiores -->
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">E-mail</th>
                                <th class="py-3 px-6 text-left">Time Atual</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse($users as $index => $user)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 flex items-center gap-3">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                            src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                        <span class="font-semibold">{{ $user->name }}</span>
                                    </td>
                                    <td class="py-3 px-6">{{ $user->email }}</td>
                                    <td class="py-3 px-6">
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-xs rounded-full bg-gray-200 dark:bg-gray-600">
                                            {{ $user->currentTeam->name ?? 'Nenhum' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button wire:click="manageUser({{ $user->id }})"
                                            class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs py-1 px-3 rounded-full">Gerenciar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhum usuário
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Cartões para telas menores -->
                <div class="block md:hidden space-y-4">
                    @forelse($users as $index => $user)
                        <div wire:key="user-card-{{ $user->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center gap-3">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                            src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ $user->name }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">E-mail: {{ $user->email }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Time:
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-xs rounded-full bg-gray-200 dark:bg-gray-600">
                                            {{ $user->currentTeam->name ?? 'Nenhum' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <button wire:click="manageUser({{ $user->id }})"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs py-1 px-3 rounded-full">Gerenciar</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhum usuário encontrado.
                        </div>
                    @endforelse
                </div>

                @if ($users->hasPages())
                    <div class="pt-4">{{ $users->links() }}</div>
                @endif
            </div>
        </div>
    </div>
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
