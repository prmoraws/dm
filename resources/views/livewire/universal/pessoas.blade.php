@section('title', 'Pessoas')

{{-- Adiciona a classe Carbon para uso na formatação de datas --}}
@use(Carbon\Carbon)

<div>
    <x-slot name="header">
        {{-- CORREÇÃO: Cabeçalho com ícone e animação --}}
        <div class="flex items-center space-x-3 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                {{ __('Pessoas') }}
            </h2>
        </div>
    </x-slot>

    {{-- CORREÇÃO: Gradiente de fundo adicionado --}}
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

            {{-- CORREÇÃO: Card principal com estilo aprimorado --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input wire:model.live.debounce.500ms="search" type="text"
                            placeholder="Buscar por Nome, Celular ou Igreja..."
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Nova Pessoa
                    </button>
                </div>

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Celular</th>
                                <th class="py-3 px-6 text-left">Bloco</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $pessoa)
                                {{-- CORREÇÃO: Animação de slide-up com delay --}}
                                <tr wire:key="pessoa-{{ $pessoa->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 animate-slide-up"
                                    style="--delay: {{ (($index % 10) + 1) * 0.05 }}s;">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $pessoa->nome }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $pessoa->celular }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $pessoa->bloco->nome ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            
                                            {{-- ALTERAÇÃO INICIADA --}}
                                            @php
                                                $printRoute = Auth::user()->currentTeam->name === 'Secretaria'
                                                    ? 'secretaria.pessoas.print.ficha'
                                                    : 'universal.pessoas.print.ficha';
                                            @endphp
                                            <a href="{{ route($printRoute, $pessoa->id) }}" target="_blank" class="w-5 transform hover:text-gray-500 hover:scale-110" title="Imprimir Ficha">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </a>
                                            {{-- ALTERAÇÃO FINALIZADA --}}

                                            <button wire:click="view({{ $pessoa->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Visualizar"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $pessoa->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $pessoa->id }})"
                                                class="w-5 transform hover:text-red-500 hover:scale-110 transition-all duration-150"
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
                                        Nenhuma pessoa encontrada para o termo "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($results->hasPages())
                    <div class="pt-4">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>

            <div class="md:hidden space-y-4 mt-6">
                @forelse ($results as $index => $pessoa)
                    {{-- CORREÇÃO: Animação de slide-up com delay --}}
                    <div wire:key="pessoa-card-{{ $pessoa->id }}"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                        style="--delay: {{ (($index % 10) + 1) * 0.05 }}s;">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $pessoa->nome }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Celular: {{ $pessoa->celular }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Bloco:
                                    {{ $pessoa->bloco->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="flex flex-col space-y-4 ml-4">

                                {{-- ALTERAÇÃO INICIADA --}}
                                @php
                                    $printRoute = Auth::user()->currentTeam->name === 'Secretaria'
                                        ? 'secretaria.pessoas.print.ficha'
                                        : 'universal.pessoas.print.ficha';
                                @endphp
                                <a href="{{ route($printRoute, $pessoa->id) }}" target="_blank" class="w-5 transform hover:text-gray-500 hover:scale-110" title="Imprimir Ficha">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </a>
                                {{-- ALTERAÇÃO FINALIZADA --}}

                                <button wire:click="view({{ $pessoa->id }})"
                                    class="w-5 transform text-gray-500 hover:text-green-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg></button>
                                <button wire:click="edit({{ $pessoa->id }})"
                                    class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg></button>
                                <button wire:click="confirmDelete({{ $pessoa->id }})"
                                    class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110 transition-all duration-150"><svg
                                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                        </path>
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500 dark:text-gray-400">
                        Nenhuma pessoa encontrada.</div>
                @endforelse
                @if ($results->hasPages())
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>

            @if ($isOpen)
                {{-- CORREÇÃO: Fundo com backdrop-blur e animação de zoom/scale --}}
                <div wire:key="create-edit-modal" x-data="{ open: @json($isOpen) }" x-show="open"
                    x-on:keydown.escape.window="$wire.closeModal()" x-transition @click.self="$wire.closeModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl p-6 md:p-8 mx-auto my-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            {{ $pessoa_id ? 'Editar Pessoa' : 'Criar Pessoa' }}</h3>

                        {{-- Alerta de Erro --}}
                        @if ($errorMessage)
                            <div x-data="{ show: true }" x-show="show" x-transition
                                class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-800 dark:bg-red-900/50 dark:text-red-300"
                                role="alert">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm font-medium">{{ $errorMessage }}</p>
                                    <button @click="show = false" type="button" class="text-red-500">
                                        <span class="sr-only">Dispensar</span>
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @endif

                        <form wire:submit.prevent="store" enctype="multipart/form-data" class="space-y-6">
                            {{-- Seção de Liderança e Grupos --}}
                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Liderança e Grupos</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label for="bloco_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label><select
                                            id="bloco_id" wire:model.live="bloco_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            @foreach ($allBlocos as $bloco)
                                                <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('bloco_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="regiao_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label><select
                                            id="regiao_id" wire:model.live="regiao_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            @if (empty($regiaos)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($regiaos as $regiao)
                                                <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('regiao_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="igreja_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Igreja</label><select
                                            id="igreja_id" wire:model="igreja_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            @if (empty($igrejas)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($igrejas as $igreja)
                                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('igreja_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="categoria_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label><select
                                            id="categoria_id" wire:model="categoria_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            @foreach ($allCategorias as $categoria)
                                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('categoria_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="cargo_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label><select
                                            id="cargo_id" wire:model="cargo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            @foreach ($allCargos as $cargo)
                                                <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('cargo_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="grupo_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grupo</label><select
                                            id="grupo_id" wire:model="grupo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            @foreach ($allGrupos as $grupo)
                                                <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('grupo_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Informações Pessoais</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2"><label for="nome"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome
                                            Completo</label><input type="text" id="nome" wire:model="nome"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        @error('nome')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="celular"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</label><input
                                            type="text" id="celular" wire:model="celular"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        @error('celular')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="telefone"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label><input
                                            type="text" id="telefone" wire:model="telefone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="email"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label><input
                                            type="email" id="email" wire:model="email"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        @error('email')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="profissao"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profissão</label><input
                                            type="text" id="profissao" wire:model="profissao"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div class="md:col-span-3"><label for="aptidoes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aptidões</label><input
                                            type="text" id="aptidoes" wire:model="aptidoes"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Endereço</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="lg:col-span-2"><label for="endereco"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço</label><input
                                            type="text" id="endereco" wire:model="endereco"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        @error('endereco')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="bairro"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label><input
                                            type="text" id="bairro" wire:model="bairro"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                        @error('bairro')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="cep"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label><input
                                            type="text" id="cep" wire:model="cep"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="estado_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label><select
                                            id="estado_id" wire:model.live="estado_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione</option>
                                            @foreach ($allEstados as $estado)
                                                <option value="{{ $estado->id }}">{{ $estado->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('estado_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="cidade_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label><select
                                            id="cidade_id" wire:model="cidade_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            @if (empty($cidades)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($cidades as $cidade)
                                                <option value="{{ $cidade->id }}">{{ $cidade->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('cidade_id')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Outras Informações</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Trabalho</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="interno"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Interno</span></label><label
                                            class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="externo"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Externo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Batismo</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                                value="aguas"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Nas
                                                Águas</span></label><label class="flex items-center"><input
                                                type="checkbox" wire:model="batismo" value="espirito"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Com Espírito
                                                Santo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Preso</p><label
                                            class="flex items-center"><input type="checkbox" wire:model="preso"
                                                value="preso"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Já foi
                                                Preso(a)</span></label><label class="flex items-center"><input
                                                type="checkbox" wire:model="preso" value="familiar"
                                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                class="ml-2 text-sm text-gray-600 dark:text-gray-400">Familiar
                                                Preso</span></label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div><label for="conversao"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                            Conversão</label><input type="date" id="conversao"
                                            wire:model="conversao"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div><label for="obra"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                            Entrada na Obra</label><input type="date" id="obra"
                                            wire:model="obra"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                                <div class="mt-4"><label for="testemunho"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Testemunho</label>
                                    <textarea id="testemunho" wire:model="testemunho" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"></textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="foto"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>
                                    <input id="foto" type="file" wire:model="foto"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @if ($fotoAtual && !$foto)
                                        <div class="mt-2"><img src="{{ asset($fotoAtual) }}" alt="Foto atual" class="h-20 w-20 object-cover rounded-full"></div>
                                    @elseif($foto)
                                        <div class="mt-2"><img src="{{ $foto->temporaryUrl() }}" alt="Preview"
                                                class="h-20 w-20 object-cover rounded-full"></div>
                                    @endif
                                    <div wire:loading wire:target="foto" class="text-sm text-gray-500 mt-2">
                                        Carregando...</div>
                                    @error('foto')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </section>
                            <div class="flex justify-end gap-3 pt-4">
                                {{-- CORREÇÃO: Estilo dos botões --}}
                                <button type="button" wire:click="closeModal"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg transition-all duration-150">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 focus:outline-none focus:shadow-outline">
                                    {{ $pessoa_id ? 'Atualizar' : 'Criar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if ($isViewOpen && $selectedPessoa)
                {{-- CORREÇÃO: Fundo com backdrop-blur e animação de zoom/scale --}}
                <div wire:key="view-modal" x-data="{ open: @json($isViewOpen) }" x-show="open"
                    x-on:keydown.escape.window="$wire.closeViewModal()" x-transition
                    @click.self="$wire.closeViewModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl p-6 md:p-8 mx-auto my-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedPessoa->nome }}
                            </h3>
                            <button wire:click="closeViewModal"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100 text-3xl leading-none">&times;</button>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3 flex-shrink-0">
                                <img src="{{ $selectedPessoa->foto ? asset($selectedPessoa->foto) : 'https://ui-avatars.com/api/?name='.urlencode($selectedPessoa->nome).'&color=7F9CF5&background=EBF4FF' }}" alt="Foto de {{ $selectedPessoa->nome }}" class="h-40 w-40 rounded-full object-cover mx-auto mb-4 border-4 border-white dark:border-gray-700 shadow-lg">
                            </div>
                            <div class="md:w-2/3 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Detalhes Pessoais</h4>
                                    <div class="border-t my-1"></div>
                                    <p><strong>Email:</strong> {{ $selectedPessoa->email ?? 'N/A' }}</p>
                                    <p><strong>Celular:</strong> {{ $selectedPessoa->celular }}</p>
                                    <p><strong>Telefone:</strong> {{ $selectedPessoa->telefone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Localização</h4>
                                    <div class="border-t my-1"></div>
                                    <p>{{ $selectedPessoa->endereco }}, {{ $selectedPessoa->bairro }}</p>
                                    <p>{{ optional($selectedPessoa->cidade)->nome }} -
                                        {{ optional(optional($selectedPessoa->cidade)->estado)->uf }}, CEP:
                                        {{ $selectedPessoa->cep ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Dados Espirituais</h4>
                                    <div class="border-t my-1"></div>
                                    <p><strong>Conversão:</strong> {{ $selectedPessoa->conversao ? Carbon::parse($selectedPessoa->conversao)->format('d/m/Y') : 'N/A' }}</p>
                                    {{-- CORREÇÃO: Removido json_decode(), pois a propriedade já é um array --}}
                                    @php
                                        // Garante que os atributos sejam arrays, mesmo que venham mal formatados do DB
                                        $batismoArray = is_array($selectedPessoa->batismo) ? $selectedPessoa->batismo : [];
                                        $presoArray = is_array($selectedPessoa->preso) ? $selectedPessoa->preso : [];
                                    @endphp
                                    <p><strong>Batismo:</strong> Águas:
                                        {{ in_array('aguas', $batismoArray) ? 'Sim' : 'Não' }} |
                                        Espírito Santo:
                                        {{ in_array('espirito', $batismoArray) ? 'Sim' : 'Não' }}</p>
                                    <p><strong>Situação:</strong> Já foi preso(a):
                                        {{ in_array('preso', $presoArray) ? 'Sim' : 'Não' }} |
                                        Familiar Preso:
                                        {{ in_array('familiar', $presoArray) ? 'Sim' : 'Não' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-600 dark:text-blue-400">Testemunho</h4>
                                    <div class="border-t my-1"></div>
                                    <p class="italic text-gray-600 dark:text-gray-400">
                                        {{ $selectedPessoa->testemunho ?: 'Nenhum testemunho registrado.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($confirmDeleteId)
                {{-- CORREÇÃO: Fundo com backdrop-blur e animação de zoom/scale --}}
                <div wire:key="delete-modal" x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                    x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                    @click.self="$wire.set('confirmDeleteId', null)"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                        <div class="flex items-center gap-3 mb-4"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este registro?
                            Esta ação não pode ser desfeita.</p>
                        <div class="flex justify-end gap-3">
                            {{-- CORREÇÃO: Estilo dos botões --}}
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
                                Apagar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- CORREÇÃO: Bloco de estilos para as animações --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            /* Inicia invisível */
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