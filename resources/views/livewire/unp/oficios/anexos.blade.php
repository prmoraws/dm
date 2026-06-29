@section('title', 'Anexos de Ofício')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Anexos de Ofício') }}
        </h2>
    </div>
</x-slot>

<div x-data="{
    isIos: ['iPhone', 'iPad', 'iPod'].includes(navigator.platform) || (navigator.userAgent.includes('Mac') && navigator.maxTouchPoints > 1),
    viewOrDownload(anexoId) {
        if (this.isIos) {
            window.open('{{ route('anexos.pdf.view', ['id' => 'TEMP_ANEXO_ID']) }}'.replace('TEMP_ANEXO_ID', anexoId), '_blank');
        } else {
            $wire.view(anexoId);
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
                            placeholder="Buscar por título..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500">
                    </div>
                    <button wire:click="create()"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Anexo
                    </button>
                </div>

                {{-- INÍCIO DO MODAL DE CRIAR/EDITAR (INCORPORADO E CORRIGIDO) --}}
                @if ($isOpen)
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl mx-4 p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $anexo_id ? 'Editar' : 'Criar' }} Anexo</h2>
                            <form wire:submit.prevent="store" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="titulo"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Título
                                            do Anexo</label>
                                        <input type="text" wire:model.defer="titulo" id="titulo"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                        @error('titulo')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="data"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Data</label>
                                        <input type="date" wire:model.defer="data" id="data"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                        @error('data')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t dark:border-gray-700">
                                    <div wire:ignore>
                                        <label
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Esposas</label>
                                        <x-advanced-select :options="$listasConvidados['esposas']" wire:model="esposas_selected"
                                            placeholder="Selecione as esposas..." />
                                    </div>
                                    <div wire:ignore>
                                        <label
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Convidados</label>
                                        <x-advanced-select :options="$listasConvidados['convidados']" wire:model="convidados_selected"
                                            placeholder="Selecione os convidados..." />
                                    </div>
                                    <div wire:ignore>
                                        <label
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Comunicação</label>
                                        <x-advanced-select :options="$listasConvidados['comunicacao']" wire:model="comunicacao_selected"
                                            placeholder="Selecione da comunicação..." />
                                    </div>
                                    <div wire:ignore>
                                        <label
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Organização</label>
                                        <x-advanced-select :options="$listasConvidados['organizacao']" wire:model="organizacao_selected"
                                            placeholder="Selecione da organização..." />
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button type="button" wire:click="closeModal()"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">Salvar
                                        Anexo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedAnexo)
                    <div wire:key="view-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 sm:p-10 flex flex-col items-center justify-start pt-10"
                        x-data="{ open: @json($isViewOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeViewModal()"
                        x-transition>
                        <div class="w-full max-w-[210mm] flex justify-end items-center mb-4 print:hidden">
                            <button onclick="printOficio()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2"><svg
                                    class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>Imprimir</button>
                            <button wire:click="closeViewModal()" type="button"
                                class="ml-4 p-2 rounded-full text-gray-500 bg-white/50 hover:bg-gray-200"><svg
                                    class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg></button>
                        </div>
                        <div class="w-full overflow-x-auto flex justify-start md:justify-center"">
                            <div id="oficio-para-impressao"
                                class="w-[209mm] min-h-[296mm] bg-white text-black px-10 shadow-2xl font-sans text-sm">
                                <div class="flex flex-col max-h-9/10"> {{-- aqui fundo --}}
                                    <header
                                        class="opacity-75 overflow-hidden flex flex-wrap object-scale-down justify-end items-start border-b-4 rounded-sm border-blue-950">
                                        <div class="w-40 pb-1">
                                            <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
                                        </div>
                                    </header>
                                    {{-- CORREÇÃO: Posição do título da Coordenadoria --}}
                                    <p class="text-lg font-semibold text-center mt-1">Coordenadoria de Evangelização
                                        Estadual nas Unidades Prisionais</p>
                                    <main class="h-[55rem] flex-grow pt-6">
                                        <h2 class="text-center font-bold text-lg mb-6 underline">
                                            {{ strtoupper($selectedAnexo->titulo) }}</h2>
                                        @foreach ($selectedAnexo->listas_formatadas as $titulo_lista => $lista_convidados)
                                            @if ($lista_convidados->filter()->isNotEmpty())
                                                <div class="mb-6">
                                                    <h3 class="font-bold text-base mb-2 underline">{{ $titulo_lista }}
                                                    </h3>
                                                    <div class="space-y-1 text-sm">
                                                        @foreach ($lista_convidados->filter() as $convidado)
                                                            <p>{{ $convidado->nome }} - CPF: {{ $convidado->cpf }}</p>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </main>
                                    <footer
                                        class="opacity-75 sticky text-center mb-9 text-xs pt-2 border-t-4 rounded-sm border-blue-950 mt-auto">
                                        <p
                                            class="border-y-2 mx-52 text-xl text-blue-950 rounded-sm border-blue-950 font-bold">
                                            UNIVERSAL NOS <span class="text-red-600">PRESÍDIOS</span></p>
                                        <p class="pt-2">Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA
                                            40280-000</p>
                                    </footer>
                                </div>
                            </div>
                        </div>
                @endif

                {{-- CORREÇÃO: Adicionado o modal de exclusão --}}
                @if ($confirmDeleteId)
                    <div class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4"
                        x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="$wire.set('confirmDeleteId', null)" x-transition
                        @click.self="$wire.set('confirmDeleteId', null)">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto"
                            x-show="open" x-transition>
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este anexo?
                                Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                                <button wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">Apagar</button>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- Tabela Desktop --}}
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Título do Anexo</th>
                                <th class="py-3 px-6 text-left">Data</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($results as $index => $anexo)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">{{ $anexo->titulo }}</td>
                                    <td class="py-3 px-6 text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($anexo->data)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button @click="viewOrDownload({{ $anexo->id }})"
                                                class="w-5 transform text-gray-500 hover:text-green-500"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $anexo->id }})"
                                                class="w-5 transform text-gray-500 hover:text-blue-500"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $anexo->id }})"
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
                                    <td colspan="3" class="py-3 px-6 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum anexo
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards Mobile --}}
                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $anexo)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $anexo->titulo }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Data:
                                        {{ \Carbon\Carbon::parse($anexo->data)->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex flex-row items-center space-x-4 ml-4">
                                    <button @click="viewOrDownload({{ $anexo->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $anexo->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $anexo->id }})"
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
                            Nenhum anexo encontrado.</div>
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

        @media print {
            body * {
                visibility: hidden;
            }

            #oficio-para-impressao,
            #oficio-para-impressao * {
                visibility: visible;
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
                box-shadow: none;
            }
        }
    </style>
    <script>
        function printOficio() {
            window.print();
        }
    </script>
</div>
