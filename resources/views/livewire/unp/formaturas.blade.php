@section('title', 'Formaturas')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M12 14l9-5-9-5-9 5 9 5z" />
            <path
                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222 4 2.222V20" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Formaturas') }}
        </h2>
    </div>
</x-slot>

{{-- ÚNICO ELEMENTO RAIZ PARA O LIVEWIRE --}}
<div>
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
                            <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        </div>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">&times;</button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Buscar por presídio ou curso...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Registrar Formatura
                    </button>
                </div>

                {{-- MODAL DE CRIAR/EDITAR CORRIGIDO --}}
                @if ($isOpen)
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl mx-4 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $formatura_id ? 'Editar Formatura' : 'Registrar Nova Formatura' }}</h2>
                            <form wire:submit.prevent="store" enctype="multipart/form-data" class="space-y-6">
                                <div>
                                    <label for="presidio_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">1. Selecione o Presídio</label>
                                    <select id="presidio_id" wire:model.live="presidio_id" class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800" required>
                                        <option value="">Selecione...</option>
                                        @foreach ($presidioOptions as $id => $nome)
                                            <option value="{{ $id }}">{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('presidio_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="curso_id" class="block text-sm font-bold mb-2 {{ !$isPresidioSelected ? 'text-gray-400 dark:text-gray-600' : 'text-gray-700 dark:text-gray-300' }}">2. Selecione o Curso (Status: Certificando)</label>
                                    <select id="curso_id" wire:model.live="curso_id" class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 disabled:bg-gray-200 dark:disabled:bg-gray-700 disabled:cursor-not-allowed" @disabled(!$isPresidioSelected) required>
                                        <option value="">Aguardando seleção do presídio...</option>
                                        @foreach ($cursoOptions as $id => $nome)
                                            <option value="{{ $id }}">{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('curso_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Instrutor</label>
                                        <input type="text" wire:model="instrutor_nome" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-300 bg-gray-200 cursor-not-allowed" disabled>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Início</label>
                                        <input type="date" wire:model="inicio" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-300 bg-gray-200 cursor-not-allowed" disabled>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Fim</label>
                                        <input type="date" wire:model="fim" class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-300 bg-gray-200 cursor-not-allowed" disabled>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-6">
                                    <div>
                                        <label for="formatura"
                                            class="block text-sm font-bold mb-2 {{ !$isCursoSelected ? 'text-gray-400 dark:text-gray-600' : 'text-gray-800 dark:text-gray-300' }}">3.
                                            Data da Formatura</label>
                                        <input id="formatura" type="date" wire:model.defer="formatura"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800 disabled:bg-gray-200 dark:disabled:bg-gray-700 disabled:cursor-not-allowed"
                                            @disabled(!$isCursoSelected)>
                                        @error('formatura')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label for="lista"
                                                class="block text-sm font-bold mb-2 {{ !$isCursoSelected ? 'text-gray-400 dark:text-gray-600' : 'text-gray-800 dark:text-gray-300' }}">Lista
                                                (PDF)</label>
                                            <input id="lista" type="file" wire:model="lista" accept=".pdf"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled(!$isCursoSelected)>
                                            @if ($formatura_id && $selectedFormatura?->lista)
                                                <div class="mt-2"><a href="{{ asset($selectedFormatura->lista) }}"
                                                        target="_blank"
                                                        class="text-blue-500 hover:underline text-xs">Ver arquivo
                                                        atual</a></div>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="conteudo"
                                                class="block text-sm font-bold mb-2 {{ !$isCursoSelected ? 'text-gray-400 dark:text-gray-600' : 'text-gray-800 dark:text-gray-300' }}">Conteúdo
                                                (PDF)</label>
                                            <input id="conteudo" type="file" wire:model="conteudo"
                                                accept=".pdf"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled(!$isCursoSelected)>
                                            @if ($formatura_id && $selectedFormatura?->conteudo)
                                                <div class="mt-2"><a
                                                        href="{{ asset($selectedFormatura->conteudo) }}"
                                                        target="_blank"
                                                        class="text-blue-500 hover:underline text-xs">Ver arquivo
                                                        atual</a></div>
                                            @endif
                                        </div>
                                        <div>
                                            <label for="oficio"
                                                class="block text-sm font-bold mb-2 {{ !$isCursoSelected ? 'text-gray-400 dark:text-gray-600' : 'text-gray-800 dark:text-gray-300' }}">Ofício
                                                (PDF)</label>
                                            <input id="oficio" type="file" wire:model="oficio" accept=".pdf"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                @disabled(!$isCursoSelected)>
                                            @if ($formatura_id && $selectedFormatura?->oficio)
                                                <div class="mt-2"><a href="{{ asset($selectedFormatura->oficio) }}"
                                                        target="_blank"
                                                        class="text-blue-500 hover:underline text-xs">Ver arquivo
                                                        atual</a></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-6">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed"
                                        @disabled(!$isCursoSelected)>{{ $formatura_id ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedFormatura)
                    <div wire:key="view-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center"
                        x-data="{ open: @json($isViewOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeViewModal()"
                        x-transition @click.self="$wire.closeViewModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 md:p-8"
                            x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90">
                            <div
                                class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $selectedFormatura->curso->nome }}</h2>
                                <button wire:click="closeViewModal" type="button"
                                    class="p-2 -m-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700"><svg
                                        class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></button>
                            </div>
                            <div class="space-y-4 text-sm">
                                <p><strong class="text-gray-500">Presídio:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedFormatura->presidio->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500">Instrutor:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedFormatura->instrutor->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500">Período:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($selectedFormatura->inicio)->format('d/m/Y') }}
                                        a {{ \Carbon\Carbon::parse($selectedFormatura->fim)->format('d/m/Y') }}</span>
                                </p>
                                <p><strong class="text-gray-500">Formatura:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedFormatura->formatura ? \Carbon\Carbon::parse($selectedFormatura->formatura)->format('d/m/Y') : 'Pendente' }}</span>
                                </p>
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">Documentos</h4>
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        @if ($selectedFormatura->lista)
                                            <a href="{{ asset($selectedFormatura->lista) }}" target="_blank"
                                                class="text-blue-500 hover:underline flex items-center gap-1"> <svg
                                                    class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg> Lista </a>
                                        @endif
                                        @if ($selectedFormatura->conteudo)
                                            <a href="{{ asset($selectedFormatura->conteudo) }}" target="_blank"
                                                class="text-blue-500 hover:underline flex items-center gap-1"> <svg
                                                    class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg> Conteúdo </a>
                                        @endif
                                        @if ($selectedFormatura->oficio)
                                            <a href="{{ asset($selectedFormatura->oficio) }}" target="_blank"
                                                class="text-blue-500 hover:underline flex items-center gap-1"> <svg
                                                    class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg> Ofício </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div wire:key="delete-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4"
                        x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                        @click.self="$wire.set('confirmDeleteId', null)">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto"
                            x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90">
                            <div class="flex items-center gap-3 mb-4"><svg class="h-6 w-6 text-red-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar esta
                                formatura? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg flex items-center gap-2">Excluir</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('presidio_id')">
                                    Presídio</th>
                                <th class="py-3 px-6 text-left">Curso</th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('inicio')">Período
                                </th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('formatura')">
                                    Formatura</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $formatura)
                                <tr wire:key="formatura-{{ $formatura->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left truncate"
                                        title="{{ $formatura->presidio->nome ?? '' }}">
                                        {{ $formatura->presidio->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left truncate"
                                        title="{{ $formatura->curso->nome ?? '' }}">
                                        {{ $formatura->curso->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ \Carbon\Carbon::parse($formatura->inicio)->format('d/m/y') }} a
                                        {{ \Carbon\Carbon::parse($formatura->fim)->format('d/m/y') }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $formatura->formatura ? \Carbon\Carbon::parse($formatura->formatura)->format('d/m/Y') : 'Pendente' }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $formatura->id }})"
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
                                            <button wire:click="edit({{ $formatura->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                                aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $formatura->id }})"
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhuma formatura encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse ($results as $index => $formatura)
                        <div wire:key="formatura-card-{{ $formatura->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $formatura->presidio->nome ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Curso:
                                        {{ $formatura->curso->nome ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Período:
                                        {{ \Carbon\Carbon::parse($formatura->inicio)->format('d/m/y') }} a
                                        {{ \Carbon\Carbon::parse($formatura->fim)->format('d/m/y') }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $formatura->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Visualizar"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $formatura->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $formatura->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110 transition-all duration-150"
                                        aria-label="Excluir"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                            </path>
                                        </svg></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhuma formatura encontrada.</div>
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
