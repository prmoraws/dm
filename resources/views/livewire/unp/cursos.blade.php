@section('title', 'Cursos')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Cursos') }}
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
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        <button @click="show = false"
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200">&times;</button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        {{-- CORREÇÃO: Classes dark mode no input de busca --}}
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Buscar por nome ou presídio...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Novo Curso
                    </button>
                </div>

                {{-- INÍCIO DO MODAL CRIAR/EDITAR CORRIGIDO --}}
                @if ($isOpen)
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl mx-4 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $curso_id ? 'Editar Curso' : 'Criar Curso' }}</h2>
                            <form wire:submit.prevent="store" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nome"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome
                                            do Curso</label>
                                        <input type="text" id="nome" wire:model.defer="nome"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('nome')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="presidio_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Presídio
                                            (Unidade)</label>
                                        <select id="presidio_id" wire:model.defer="presidio_id"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                            <option value="">Selecione...</option>
                                            @foreach ($presidioOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('presidio_id')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="instrutor_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Professor
                                            (Instrutor)</label>
                                        <select id="instrutor_id" wire:model.defer="instrutor_id"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                            <option value="">Selecione...</option>
                                            @foreach ($instrutorOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('instrutor_id')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="dia_hora"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Dia e
                                            Hora</label>
                                        <input type="text" id="dia_hora" wire:model.defer="dia_hora"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('dia_hora')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="carga"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Carga
                                            Horária</label>
                                        <input type="text" id="carga" wire:model.defer="carga"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('carga')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="reeducandos"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Qtd.
                                            Reeducandos</label>
                                        <input type="number" id="reeducandos" wire:model.defer="reeducandos"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            min="0" required>
                                        @error('reeducandos')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="inicio"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Início</label>
                                        <input type="date" id="inicio" wire:model.defer="inicio"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('inicio')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="fim"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Fim</label>
                                        <input type="date" id="fim" wire:model.defer="fim"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('fim')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="formatura"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Formatura</label>
                                        <input type="date" id="formatura" wire:model.defer="formatura"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                    <div>
                                        <label for="status"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                        <select id="status" wire:model.defer="status"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            <option value="">Selecione...</option>
                                            <option value="CURSANDO">Cursando</option>
                                            <option value="CERTIFICANDO">Certificando</option>
                                            <option value="FINALIZADO">Finalizado</option>
                                            <option value="PENDENTE">Pendente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-6">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">{{ $curso_id ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedCurso)
                    <div wire:key="view-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center"
                        x-data="{ open: @json($isViewOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeViewModal()"
                        x-transition @click.self="$wire.closeViewModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl mx-4 p-6 md:p-8"
                            x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-90">
                            <div
                                class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $selectedCurso->nome }}</h2>
                                <button wire:click="closeViewModal" type="button"
                                    class="p-2 -m-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700"><svg
                                        class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                                <p><strong class="text-gray-500">Presídio (Unidade):</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->presidio->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500">Dia e Hora:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->dia_hora }}</span>
                                </p>
                                <p><strong class="text-gray-500">Professor (Instrutor):</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->instrutor->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500">Carga Horária:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->carga }}</span>
                                </p>
                                <p><strong class="text-gray-500">Qtd. Reeducandos:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->reeducandos }}</span>
                                </p>
                                <p><strong class="text-gray-500">Início:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($selectedCurso->inicio)->format('d/m/Y') }}</span>
                                </p>
                                <p><strong class="text-gray-500">Fim:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($selectedCurso->fim)->format('d/m/Y') }}</span>
                                </p>
                                <p><strong class="text-gray-500">Formatura:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->formatura ? \Carbon\Carbon::parse($selectedCurso->formatura)->format('d/m/Y') : 'Pendente' }}</span>
                                </p>
                                <p><strong class="text-gray-500">Status:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedCurso->status ?? 'Não informado' }}</span>
                                </p>
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
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este curso?
                                Esta ação não pode ser desfeita.</p>
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
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Unidade (Presídio)</th>
                                <th class="py-3 px-6 text-center">Reeducandos</th>
                                <th class="py-3 px-6 text-center">Fim</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $curso)
                                <tr wire:key="curso-{{ $curso->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left">{{ $curso->nome }}</td>
                                    <td class="py-3 px-6 text-left">{{ $curso->presidio->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">{{ $curso->reeducandos }}</td>
                                    <td class="py-3 px-6 text-center">
                                        {{ \Carbon\Carbon::parse($curso->fim)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        {{-- CORREÇÃO: Lógica de cores para o Status --}}
                                        @php
                                            $status = strtolower($curso->status ?? '');
                                            $colorClass = match ($status) {
                                                'cursando'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'certificando'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'finalizado'
                                                    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                default
                                                    => 'bg-gray-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $colorClass }}">
                                            {{ $curso->status ?? 'Pendente' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $curso->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $curso->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $curso->id }})"
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Nenhum curso
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards para telas menores --}}
                <div class="md:hidden space-y-4">
                    @forelse ($results as $index => $curso)
                        <div wire:key="curso-card-{{ $curso->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $curso->nome }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Presídio:
                                        {{ $curso->presidio->nome ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Reeducandos: <span
                                            class="font-medium">{{ $curso->reeducandos }}</span></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Fim: <span
                                            class="font-medium">{{ \Carbon\Carbon::parse($curso->fim)->format('d/m/Y') }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">Status:
                                        @php
                                            $status = strtolower($curso->status ?? '');
                                            $colorClass = match ($status) {
                                                'cursando'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'certificando'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'finalizado'
                                                    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                default
                                                    => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 text-xs font-semibold leading-tight rounded-full {{ $colorClass }}">
                                            {{ $curso->status ?? 'Pendente' }}
                                        </span>
                                    </p>
                                </div>
                                {{-- CORREÇÃO: Adicionados ícones SVG que estavam faltando nos botões --}}
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $curso->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $curso->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $curso->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhum curso encontrado.</div>
                    @endforelse
                </div>

                @if ($results->hasPages())
                    <div class="pt-4">{{ $results->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bloco de estilos movido para @push para evitar erros do Livewire --}}
    @push('styles')
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
    @endpush
</div>
