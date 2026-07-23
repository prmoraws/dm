@section('title', 'Gerenciar Usuários')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Gerenciar Usuários') }}
        </h2>
    </div>
</x-slot>

<div>
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message') || session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
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
                            placeholder="Buscar por nome ou e-mail..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <button wire:click="create()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Usuário
                    </button>
                </div>

                <!-- Modal Criar / Editar -->
                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 p-4 flex items-center justify-center overflow-y-auto">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6 my-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $user_id ? 'Editar Usuário' : 'Criar Novo Usuário' }}
                            </h2>
                            <form wire:submit.prevent="store" class="space-y-4">

                                <!-- Foto de Perfil -->
                                <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                                    <input type="file" class="hidden" wire:model.live="photo" x-ref="photo"
                                        x-on:change="
                                                        photoName = $refs.photo.files[0].name;
                                                        const reader = new FileReader();
                                                        reader.onload = (e) => {
                                                            photoPreview = e.target.result;
                                                        };
                                                        reader.readAsDataURL($refs.photo.files[0]);
                                                " />

                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Foto de
                                        Perfil</label>

                                    <div class="mt-2 flex items-center gap-4">
                                        <!-- Foto Atual -->
                                        <div x-show="! photoPreview">
                                            @if ($current_profile_photo_url)
                                                <img src="{{ $current_profile_photo_url }}" alt="Current Photo"
                                                    class="h-16 w-16 rounded-full object-cover border dark:border-gray-600">
                                            @else
                                                <div
                                                    class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold border dark:border-gray-600">
                                                    {{ substr($name, 0, 1) ?: '?' }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Preview da Nova Foto -->
                                        <div x-show="photoPreview" style="display: none;">
                                            <span
                                                class="block h-16 w-16 rounded-full bg-cover bg-no-repeat bg-center border dark:border-gray-600"
                                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>

                                        <button type="button"
                                            class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                                            x-on:click.prevent="$refs.photo.click()">
                                            Selecionar Nova Foto
                                        </button>
                                    </div>
                                    @error('photo')
                                        <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="name"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nome:</label>
                                    <input type="text" wire:model.defer="name" id="name"
                                        class="mt-1 w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                                        required>
                                    @error('name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300">E-mail:</label>
                                    <input type="email" wire:model.defer="email" id="email"
                                        class="mt-1 w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                                        required>
                                    @error('email')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password"
                                        class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                        Senha:
                                        @if ($user_id)
                                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Deixe em
                                                branco para manter a atual)</span>
                                        @endif
                                    </label>
                                    <input type="password" wire:model.defer="password" id="password"
                                        class="mt-1 w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                                        {{ !$user_id ? 'required' : '' }}>
                                    @error('password')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="team_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300">Time
                                            (Acesso):</label>
                                        <select wire:model.defer="team_id" id="team_id"
                                            class="mt-1 w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                                            required>
                                            <option value="">Selecione...</option>
                                            @foreach ($allTeams as $team)
                                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('team_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="bloco_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300">Bloco
                                            Responsável:</label>
                                        <select wire:model.defer="bloco_id" id="bloco_id"
                                            class="mt-1 w-full rounded-md dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                            <option value="">Nenhum</option>
                                            @foreach ($allBlocos as $bloco)
                                                <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('bloco_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg transition">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="photo">Salvar Usuário</span>
                                        <span wire:loading wire:target="photo">Carregando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Modal Exclusão -->
                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Confirmar Exclusão</h3>
                            <p class="my-4 text-gray-600 dark:text-gray-300">Tem certeza que deseja apagar este
                                usuário? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">Cancelar</button>
                                <button wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Sim,
                                    Apagar</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabela Desktop -->
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Usuário</th>
                                <th class="py-3 px-6 text-left">E-mail</th>
                                <th class="py-3 px-6 text-left">Time</th>
                                <th class="py-3 px-6 text-left">Bloco</th>
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
                                            class="px-2 py-1 font-semibold leading-tight text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $user->currentTeam->name ?? 'Sem Time' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6">
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-xs rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                            {{ optional($user->bloco)->nome ?? 'Geral/Admin' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="edit({{ $user->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button wire:click="confirmDelete({{ $user->id }})"
                                                class="w-5 transform hover:text-red-500 hover:scale-110 transition-all">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Nenhum usuário
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Cartões Mobile -->
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
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                                    <div class="mt-2 flex gap-2">
                                        <span
                                            class="px-2 py-1 font-semibold text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Time: {{ $user->currentTeam->name ?? 'Sem Time' }}
                                        </span>
                                        <span
                                            class="px-2 py-1 font-semibold text-xs rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                            Bloco: {{ optional($user->bloco)->nome ?? 'Nenhum' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col space-y-3 ml-4">
                                    <button wire:click="edit({{ $user->id }})"
                                        class="text-gray-500 hover:text-blue-500">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                        class="text-gray-500 hover:text-red-500">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                        </svg>
                                    </button>
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
