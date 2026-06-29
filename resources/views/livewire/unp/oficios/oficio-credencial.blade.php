@section('title', 'Ofícios de Credencial')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h4a2 2 0 012 2v1m-6.003 4.003l-.001-1.001A2 2 0 1112 9a2 2 0 110 4.002l.001-1.001M12 14a3 3 0 100-6 3 3 0 000 6z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Ofícios de Credencial') }}
        </h2>
    </div>
</x-slot>

{{-- ADICIONADO O WRAPPER ALPINE.JS --}}
<div x-data="{
    isIos: ['iPhone', 'iPad', 'iPod'].includes(navigator.platform) || (navigator.userAgent.includes('Mac') && navigator.maxTouchPoints > 1),
    viewOrDownload(oficioId) {
        if (this.isIos) {
            window.open('{{ route('credencial.pdf.view', ['id' => 'TEMP_OFICIO_ID']) }}'.replace('TEMP_OFICIO_ID', oficioId), '_blank');
        } else {
            $wire.view(oficioId);
        }
    },
    applyMask(value, pattern) {
        let i = 0;
        const v = value.toString().replace(/\D/g, '');
        return pattern.replace(/[#*]/g, () => v[i++] || '');
    }
}">
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if (session()->has('message') || session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                    class="rounded-lg shadow-lg my-6 p-4 border-l-4 {{ session()->has('message') ? 'bg-teal-50 dark:bg-teal-900/50 border-teal-500' : 'bg-red-50 dark:bg-red-900/50 border-red-500' }}">
                    <div class="flex items-center justify-between">
                        <p
                            class="text-sm font-medium {{ session()->has('message') ? 'text-teal-800 dark:text-teal-200' : 'text-red-800 dark:text-red-200' }}">
                            {{ session('message') ?? session('error') }}
                        </p>
                        <button @click="show = false"
                            class="{{ session()->has('message') ? 'text-teal-600 dark:text-teal-400' : 'text-red-600 dark:text-red-400' }}">&times;</button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500"
                            placeholder="Buscar por nome ou presídio...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Ofício de Credencial
                    </button>
                </div>

                @if ($isOpen)
                    {{-- CORRIGIDO: alinhamento do modal e cores de texto e inputs --}}
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        x-data="{ open: @json($isOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeModal()"
                        x-transition @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-xl p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $oficio_id ? 'Editar' : 'Criar' }} Ofício</h2>
                            <form wire:submit.prevent="store" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="presidio_id" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Presídio</label>
                                        <select wire:model.live="presidio_id"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                            <option value="">Selecione...</option>
                                            @foreach ($presidioOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-bold text-gray-400 dark:text-gray-500 mb-2">Diretor</label>
                                        <input type="text" wire:model="diretor_nome"
                                            class="w-full rounded-md bg-gray-200 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed"
                                            disabled>
                                    </div>
                                </div>
                                <div>
                                    <label for="nome" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Nome do
                                        Credenciado</label>
                                    <input type="text" wire:model.defer="nome" id="nome"
                                        class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                    @error('nome')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="cpf" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">CPF</label>
                                        <input type="text" wire:model.defer="cpf" id="cpf"
                                            x-on:input="$event.target.value = applyMask($event.target.value, '###.###.###-##')"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600" placeholder="000.000.000-00">
                                        @error('cpf')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="rg" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">RG (Opcional)</label>
                                        <input type="text" wire:model.defer="rg" id="rg"
                                            x-on:input="$event.target.value = applyMask($event.target.value, '#.###.###-*')"
                                            class="w-full rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600" placeholder="0.000.000-0">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4"><button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button><button
                                        type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedOficio)
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
                            <button wire:click="closeViewModal" type="button"
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
                                        <div class="flex justify-between items-start">
                                            <div class="space-y-0 text-left text-sm leading-snug">
                                                {{-- CORREÇÃO: Espaçamento reduzido e campo em negrito --}}
                                                <p class="font-bold pl-5">{{ $selectedOficio->numero_oficio }}</p>
                                                <p class="pl-5 pb-4">{{ $selectedOficio->data_formatada }}</p>
                                                <p class="font-bold pl-5">{{ $selectedOficio->destinatario }}</p>
                                                <p class="font-bold pl-5">ASSUNTO: SOLICITAÇÃO DE CREDENCIAL DE ACESSO
                                                </p>
                                                <p class="font-bold uppercase pl-5">
                                                    {{ $selectedOficio->diretor_formatado }}</p>
                                            </div>
                                            <div
                                                class="print:absolute print:right-0 w-64 border-2 border-red-600 p-2 text-xs space-y-0 shrink-0 ml-8">
                                                <p class="text-center text-red-600 pb-2 font-bold">Confirmo recebimento
                                                    e
                                                    dou ciência.</p>
                                                <p class="text-red-600 font-bold">Nome:
                                                    ____________________________________ <span
                                                        class="block h-3"></span>
                                                </p>
                                                <p class="text-red-600 font-bold">Assinatura:
                                                    _______________________________ <span class="block h-3"></span></p>
                                                <p class="text-red-600 font-bold">Data:
                                                    ___________/___________/_____________ <span
                                                        class="block h-3"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-6 space-y-4 text-justify text-sm leading-snug">
                                            <p class="indent-8 px-5 pb-10">Com os cumprimentos de estilo, a Universal
                                                nos
                                                Presídios -
                                                UNP, vem, através de seu Coordenador Geral que a este subscreve,
                                                requerer
                                                autorização emissão (renovação) da credencial de acesso para esta
                                                unidade,
                                                no período máximo de 30 dias para que não haja interrupção no trabalho
                                                evangelístico.</p>
                                            <div class="pl-8 font-semibold font-serif pb-10">
                                                <p>Nome: {{ $selectedOficio->nome }}</p>
                                                <p>CPF: {{ $selectedOficio->cpf }}</p>
                                                <p>RG: {{ $selectedOficio->rg }}</p>
                                            </div>
                                            <p class="indent-8 px-5 pb-40">Cumpre salientar nossa Instituição continua
                                                comprometida a
                                                corroborar com os cuidados necessários em que lhe compete, contribuindo
                                                e
                                                atendendo as orientações e determinações da Unidade. De plano, registra
                                                e
                                                reitera protestos de elevada estima e
                                                consideração. Que o Senhor Deus os abençoe!</p>
                                        </div>
                                        <div class="mt-16 pb-4 text-center">
                                            <p>Atenciosamente,</p>
                                            <div class="inline-block mt-2">
                                                {{-- CORREÇÃO: Caminho da imagem ajustado --}}
                                                <img src="{{ asset('formaturas/assinatura-pastor.png') }}"
                                                    alt="Assinatura" class="h-20 mx-auto">
                                                <div class="border-t border-black mt-1">
                                                    <p class="font-semibold">Bispo Sérgio Simplício dos Santos</p>
                                                    <p class="text-xs">Coordenador da UNP no Estado da Bahia</p>
                                                    <p class="text-xs">Contato e WhatsApp (71)99982-9897 | <span
                                                            class="text-blue-700 underline underline-offset-4">E-mail:
                                                            sergsantos@universal.org</span></p>
                                                </div>
                                            </div>
                                        </div>
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
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este ofício?
                                Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 font-semibold py-2 px-4 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">Apagar</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Credenciado</th>
                                <th class="py-3 px-6 text-left">CPF</th>
                                <th class="py-3 px-6 text-left">Presídio</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse($results as $index => $oficio)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left">{{ $oficio->nome }}</td>
                                    <td class="py-3 px-6 text-left">{{ $oficio->cpf }}</td>
                                    <td class="py-3 px-6 text-left">{{ $oficio->presidio->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $oficio->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $oficio->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $oficio->id }})"
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
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhum ofício
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $oficio)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $oficio->nome }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">CPF: {{ $oficio->cpf }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Presídio:
                                        {{ $oficio->presidio->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button @click="viewOrDownload({{ $oficio->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $oficio->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500"><svg fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $oficio->id }})"
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
                            Nenhum ofício encontrado.</div>
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
