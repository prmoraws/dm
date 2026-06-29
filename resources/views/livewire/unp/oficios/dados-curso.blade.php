@section('title', 'Dados para Certificados')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Dados para Certificados') }}
        </h2>
    </div>
</x-slot>

<div x-data="{
    isIos: ['iPhone', 'iPad', 'iPod'].includes(navigator.platform) || (navigator.userAgent.includes('Mac') && navigator.maxTouchPoints > 1),
    viewOrDownload(dadoId) {
        if (this.isIos) {
            window.open('{{ route('dados.pdf.view', ['id' => 'TEMP_DADO_ID']) }}'.replace('TEMP_DADO_ID', dadoId), '_blank');
        } else {
            $wire.view(dadoId);
        }
    }
}">
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        <button @click="show = false" class="text-teal-600 dark:text-teal-400">&times;</button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500"
                            placeholder="Buscar por nome ou curso...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Dados de Curso
                    </button>
                </div>

                @if ($isOpen)
                    <div wire:key="create-edit-dados"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-3xl p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $dados_id ? 'Editar' : 'Criar' }} Dados de Curso</h2>
                            <form wire:submit.prevent="store" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label for="nome"
                                            class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Nome
                                            (Identificador)</label><input type="text" wire:model.defer="nome"
                                            id="nome"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600"
                                            placeholder="Ex: Formatura Refrigeração 2025">
                                        @error('nome')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="presidio_id"
                                            class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">1.
                                            Presídio</label><select wire:model.live="presidio_id"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                            <option value="">Selecione...</option>
                                            @foreach ($presidioOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('presidio_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="pt-4 border-t dark:border-gray-700">
                                    <label for="curso_id"
                                        class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">2. Curso
                                        (com status
                                        "Certificando")</label>
                                    <select wire:model.live="curso_id"
                                        class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600"
                                        @disabled(!$presidio_id)>
                                        <option value="">Selecione um curso...</option>
                                        @foreach ($cursoOptions as $id => $nome)
                                            <option value="{{ $id }}">{{ $nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('curso_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t dark:border-gray-700">
                                    <div><label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Diretor(a)</label><input
                                            type="text" wire:model="diretor_nome"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled></div>
                                    <div><label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Instrutor</label><input
                                            type="text" wire:model="instrutor_nome"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled></div>
                                    <div><label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Início</label><input
                                            type="text" wire:model="inicio"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled></div>
                                    <div><label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Fim</label><input
                                            type="text" wire:model="fim"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled></div>
                                    <div class="md:col-span-2"><label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Carga
                                            Horária</label><input type="text" wire:model="carga"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled></div>
                                </div>
                                <div><label class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Dados
                                        (Informações do Curso)</label>
                                    <textarea wire:model="dados_informacao" rows="6"
                                        class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed" disabled></textarea>
                                </div>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedDados)
                    <div wire:key="view-modal-dados"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 sm:p-10 flex flex-col items-center justify-start pt-10"
                        x-data="{ open: @json($isViewOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeViewModal()"
                        x-transition>
                        <div class="w-full max-w-[210mm] flex justify-end items-center mb-4 px-4 sm:px-0 print:hidden">
                            <button onclick="printOficio()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimir
                            </button>
                            <button wire:click="closeViewModal" type="button"
                                class="ml-4 p-2 rounded-full text-gray-500 bg-white/50 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="w-full overflow-x-auto flex justify-start md:justify-center">
                            <div id="oficio-para-impressao"
                                class="w-[209mm] min-h-[296mm] bg-white text-black px-10 shadow-2xl font-sans text-sm">
                                <div class="flex flex-col max-h-9/10">
                                    <header
                                        class="opacity-75 overflow-hidden flex flex-wrap object-scale-down justify-end items-start border-b-4 rounded-sm border-blue-950">
                                        <div class="w-40 pb-1">
                                            <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
                                        </div>
                                    </header>
                                    {{-- CORREÇÃO: Posição do título da Coordenadoria --}}
                                    <p class="text-lg font-semibold text-center mt-1">Coordenadoria de Evangelização
                                        Estadual nas Unidades Prisionais</p>
                                    <main class="h-[55rem] pl-10 flex-grow pt-6">
                                        <h3 class="font-bold underline text-base">DADOS DO CURSO</h3>
                                        <div class="space-y-0 text-left text-base leading-snug pt-4">
                                            <p><strong class="font-bold">NOME DO PROFESSOR:</strong>
                                                {{ $selectedDados->instrutor->nome ?? 'N/A' }}</p>
                                            <p class="uppercase"><strong class="font-bold">NOME DO DIRETOR DA
                                                    UNIDADE:</strong>
                                                {{ $selectedDados->presidio->diretor ?? 'N/A' }}</p>
                                            <p class="uppercase"><strong class="font-bold">NOME DA UNIDADE:</strong>
                                                {{ $selectedDados->presidio->nome ?? 'N/A' }}</p>
                                            <p><strong class="font-bold">NOME DO BISPO RESPONSAVEL DO GRUPO:</strong>
                                                SERGIO SIMPLÍCIO DOS SANTOS</p>
                                            <p><strong class="font-bold">CURSO:</strong>
                                                {{ $selectedDados->curso->nome ?? 'N/A' }}</p>
                                            <p><strong class="font-bold">DATA DE INICIO:</strong>
                                                {{ $selectedDados->inicio->format('d/m/Y') }}</p>
                                            <p><strong class="font-bold">DATA DE TERMINO:</strong>
                                                {{ $selectedDados->fim->format('d/m/Y') }}</p>
                                            <p><strong class="font-bold">CARGA HORARIA:</strong>
                                                {{ $selectedDados->carga }}
                                            </p>
                                        </div>
                                        <div class="pt-4">
                                            <h3 class="font-bold underline text-base">ETAPAS DO CURSO:</h3>
                                            <div class="pl-10 whitespace-pre-line text-xl leading-none">
                                                {{ $selectedDados->informacaoCurso->informacao ?? 'Nenhuma informação adicional.' }}
                                            </div>
                                        </div>
                                    </main>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <h3 class="text-xl font-bold">Confirmar Exclusão</h3>
                            <p class="my-4">Tem certeza que deseja apagar este registro de dados?</p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 text-white py-2 px-4 rounded-lg">Apagar</button></div>
                        </div>
                    </div>
                @endif

                {{-- Tabela Desktop --}}
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Curso</th>
                                <th class="py-3 px-6 text-left">Presídio</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($results as $index => $dado)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $dado->nome }}</td>
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">
                                        {{ $dado->curso->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">
                                        {{ $dado->presidio->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button @click="viewOrDownload({{ $dado->id }})"
                                                class="w-5 transform text-gray-500 hover:text-green-500"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $dado->id }})"
                                                class="w-5 transform text-gray-500 hover:text-blue-500"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $dado->id }})"
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
                                        Nenhum registro
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $dado)
                        <div wire:key="dado-card-{{ $dado->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $dado->nome }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Curso:
                                        {{ $dado->curso->nome ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Presídio:
                                        {{ $dado->presidio->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="flex flex-row space-x-4 ml-4 items-center">
                                    <button @click="viewOrDownload({{ $dado->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $dado->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $dado->id }})"
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
                            Nenhum registro.
                        </div>
                    @endforelse
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

            @media print {
                body * {
                    visibility: hidden
                }

                #oficio-para-impressao,
                #oficio-para-impressao * {
                    visibility: visible
                }

                @media print {

                    html,
                    body {
                        zoom: 100% !important;
                        transform: scale(1) !important;
                        width: 100% !important;
                        height: auto !important;
                        overflow: visible !important;
                    }
                }

                #oficio-para-impressao {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: auto;
                    margin: 0;
                    padding: 0;
                    border: none;
                    box-shadow: none
                }
            }
        </style>
        <script>
            function printOficio() {
                window.print();
            }
        </script>
    </div>
