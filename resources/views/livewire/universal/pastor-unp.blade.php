@section('title', 'Pastores UNP')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Pastores UNP') }}
        </h2>
    </div>
</x-slot>

<div>
    <div x-data="{
        printOrOpenTab(url) {
            // Detecta se é iOS para abrir em nova aba em vez de pop-up de impressão
            const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            if (isIos) {
                window.open(url, '_blank');
            } else {
                const printWindow = window.open(url, '_blank', 'height=800,width=800');
                printWindow.onload = function() {
                    printWindow.print();
                }
            }
        }
    }">
        <div
            class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                @if (session()->has('message'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                        class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="w-full md:w-1/3">
                            <input type="text" wire:model.live.debounce.300ms="searchTerm"
                                placeholder="Buscar por nome..."
                                class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                        </div>
                        <button wire:click="create"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Novo Pastor UNP
                        </button>
                    </div>

                    @if ($isOpen)
                        <div wire:key="create-edit-pastor-unp"
                            class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10">
                            <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                    {{ $pastor_id ? 'Editar' : 'Criar' }} Pastor UNP</h2>
                                <form wire:submit.prevent="store" class="space-y-6">
                                    {{-- DADOS DO PASTOR --}}
                                    <section>
                                        <h3
                                            class="text-lg font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                            Dados do Pastor</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label for="bloco_id"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label>
                                                <select id="bloco_id" wire:model.live="bloco_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                    required>
                                                    <option value="">Selecione</option>
                                                    @foreach ($blocoOptions as $bloco)
                                                        <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                                    @endforeach
                                                </select>
                                                @error('bloco_id')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="regiao_id"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label>
                                                <select id="regiao_id" wire:model.defer="regiao_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                    @disabled(!$regiaoOptions)>
                                                    <option value="">Selecione um bloco primeiro</option>
                                                    @foreach ($regiaoOptions as $regiao)
                                                        <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>
                                                    @endforeach
                                                </select>
                                                @error('regiao_id')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="cargo"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
                                                <select id="cargo" wire:model.defer="cargo"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                    required>
                                                    <option value="">Selecione</option>
                                                    <option value="responsavel">Responsável</option>
                                                    <option value="auxiliar">Auxiliar</option>
                                                </select>
                                                @error('cargo')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="nome"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                                                <input type="text" id="nome" wire:model.defer="nome"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                    required>
                                                @error('nome')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="nascimento"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data
                                                    de Nascimento</label>
                                                <input type="date" id="nascimento" wire:model.defer="nascimento"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                                @error('nascimento')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="email"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                                <input type="email" id="email" wire:model.defer="email"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                                @error('email')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="whatsapp"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Whatsapp</label>
                                                <input type="text" id="whatsapp" wire:model.defer="whatsapp"
                                                    x-mask="(99) 99999-9999"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                    required>
                                                @error('whatsapp')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="telefone"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                                <input type="text" id="telefone" wire:model.defer="telefone"
                                                    x-mask="(99) 9999-9999"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="chegada"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quanto
                                                    tempo está no estado/bloco</label>
                                                <input type="text" id="chegada" wire:model.defer="chegada"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="entrada"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data
                                                    que entrou no grupo</label>
                                                <input type="date" id="entrada" wire:model.defer="entrada"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Já
                                                    foi
                                                    preso?</label>
                                                <input type="radio" id="preso_sim" wire:model.defer="preso"
                                                    value="1"><label for="preso_sim" class="ml-1">Sim</label>
                                                <input type="radio" id="preso_nao" wire:model.defer="preso"
                                                    value="0"><label for="preso_nao" class="ml-1">Não</label>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dias
                                                    que faz o trabalho no presídio</label>
                                                <div class="mt-2 grid grid-cols-4 md:grid-cols-7 gap-2">
                                                    @foreach (['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB', 'DOM'] as $dia)
                                                        <label class="flex items-center space-x-2"><input
                                                                type="checkbox" wire:model.defer="trabalho"
                                                                value="{{ $dia }}"><span>{{ $dia }}</span></label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <label for="foto"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>
                                                <input type="file" id="foto" wire:model="foto"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                @if ($foto)
                                                    <div class="mt-2"><img src="{{ $foto->temporaryUrl() }}"
                                                            alt="Pré-visualização"
                                                            class="h-20 w-20 object-cover rounded-full"></div>
                                                @elseif ($foto_atual)
                                                    <div class="mt-2"><img src="{{ asset($foto_atual) }}"
                                                            alt="Foto atual"
                                                            class="h-20 w-20 object-cover rounded-full">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    {{-- DADOS DA ESPOSA --}}
                                    <section>
                                        <h3
                                            class="text-lg font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                            Dados da Esposa</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="md:col-span-2">
                                                <label for="nome_esposa"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                                                <input type="text" id="nome_esposa" wire:model.defer="nome_esposa"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="obra"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempo
                                                    de obra</label>
                                                <input type="text" id="obra" wire:model.defer="obra"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="email_esposa"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                                <input type="email" id="email_esposa"
                                                    wire:model.defer="email_esposa"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="whatsapp_esposa"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Whatsapp</label>
                                                <input type="text" id="whatsapp_esposa"
                                                    wire:model.defer="whatsapp_esposa" x-mask="(99) 99999-9999"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="telefone_esposa"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                                <input type="text" id="telefone_esposa"
                                                    wire:model.defer="telefone_esposa" x-mask="(99) 9999-9999"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <label for="casado"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempo
                                                    de casado</label>
                                                <input type="text" id="casado" wire:model.defer="casado"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <label
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">Missionária
                                                    consagrada?</label>
                                                <input type="radio" id="consagrada_sim"
                                                    wire:model.defer="consagrada_esposa" value="1"><label
                                                    for="consagrada_sim" class="ml-1">Sim</label>
                                                <input type="radio" id="consagrada_nao"
                                                    wire:model.defer="consagrada_esposa" value="0"><label
                                                    for="consagrada_nao" class="ml-1">Não</label>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Já
                                                    foi
                                                    presa?</label>
                                                <input type="radio" id="preso_esposa_sim"
                                                    wire:model.defer="preso_esposa" value="1"><label
                                                    for="preso_esposa_sim" class="ml-1">Sim</label>
                                                <input type="radio" id="preso_esposa_nao"
                                                    wire:model.defer="preso_esposa" value="0"><label
                                                    for="preso_esposa_nao" class="ml-1">Não</label>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dias
                                                    que faz o trabalho no presídio</label>
                                                <div class="mt-2 grid grid-cols-4 md:grid-cols-7 gap-2">
                                                    @foreach (['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB', 'DOM'] as $dia)
                                                        <label class="flex items-center space-x-2"><input
                                                                type="checkbox" wire:model.defer="trabalho_esposa"
                                                                value="{{ $dia }}"><span>{{ $dia }}</span></label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div>
                                                <label for="foto_esposa"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto
                                                    da Esposa</label>
                                                <input type="file" id="foto_esposa" wire:model="foto_esposa"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                @if ($foto_esposa)
                                                    <div class="mt-2"><img src="{{ $foto_esposa->temporaryUrl() }}"
                                                            alt="Pré-visualização"
                                                            class="h-20 w-20 object-cover rounded-full"></div>
                                                @elseif ($foto_esposa_atual)
                                                    <div class="mt-2"><img src="{{ asset($foto_esposa_atual) }}"
                                                            alt="Foto atual da esposa"
                                                            class="h-20 w-20 object-cover rounded-full"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                    <div class="flex justify-end gap-3 pt-6 border-t dark:border-gray-700">
                                        <button type="button" wire:click="closeModal"
                                            class="bg-gray-300 hover:bg-gray-400 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Salvar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Modal de Visualização --}}
                    @if ($isViewOpen && $selectedPastor)
                        <div
                            class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start md:items-center justify-start md:justify-center">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                                <div class="flex justify-between items-start pb-4 border-b dark:border-gray-700">
                                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detalhes do Pastor
                                    </h2>
                                    <div>
                                        <button
                                            @click="printOrOpenTab('{{ route('pastor-unp.print', $selectedPastor->id) }}')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white text-sm py-1 px-3 rounded-lg">Imprimir
                                            Ficha</button>
                                        <button wire:click="closeViewModal"
                                            class="p-2 -m-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">&times;</button>
                                    </div>
                                </div>
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-1 flex flex-col items-center">
                                        <img src="{{ $selectedPastor->foto ? asset($selectedPastor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedPastor->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                            alt="Foto de {{ $selectedPastor->nome }}"
                                            class="h-40 w-40 rounded-full object-cover mb-4">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ $selectedPastor->nome }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">
                                            {{ $selectedPastor->cargo }}</p>
                                    </div>
                                    <div class="md:col-span-2 space-y-4">
                                        <div>
                                            <h4 class="font-semibold text-blue-500">Detalhes Pessoais</h4>
                                            <div class="border-t my-1"></div>
                                            <p><strong>Email:</strong> {{ $selectedPastor->email ?? 'N/A' }}</p>
                                            <p><strong>Whatsapp:</strong> {{ $selectedPastor->whatsapp }}</p>
                                            <p><strong>Telefone:</strong> {{ $selectedPastor->telefone ?? 'N/A' }}</p>
                                            <p><strong>Nascimento:</strong>
                                                {{ $selectedPastor->nascimento ? $selectedPastor->nascimento->format('d/m/Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-blue-500">Dados do Grupo</h4>
                                            <div class="border-t my-1"></div>
                                            <p><strong>Bloco:</strong> {{ $selectedPastor->bloco->nome ?? 'N/A' }}</p>
                                            <p><strong>Região:</strong> {{ $selectedPastor->regiao->nome ?? 'N/A' }}
                                            </p>
                                            <p><strong>Entrada no grupo:</strong>
                                                {{ $selectedPastor->entrada ? $selectedPastor->entrada->format('d/m/Y') : 'N/A' }}
                                            </p>
                                            <p><strong>Tempo no estado/bloco:</strong>
                                                {{ $selectedPastor->chegada ?? 'N/A' }}</p>
                                            <p><strong>Já foi preso?</strong>
                                                {{ $selectedPastor->preso ? 'Sim' : 'Não' }}
                                            </p>
                                            <p><strong>Dias de trabalho:</strong>
                                                {{ is_array($selectedPastor->trabalho) ? implode(', ', $selectedPastor->trabalho) : 'Não informado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @if ($selectedPastor->nome_esposa)
                                    <div
                                        class="mt-6 pt-6 border-t dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="md:col-span-1 flex flex-col items-center">
                                            <img src="{{ $selectedPastor->foto_esposa ? asset($selectedPastor->foto_esposa) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedPastor->nome_esposa) . '&color=F472B6&background=FCE7F3' }}"
                                                alt="Foto de {{ $selectedPastor->nome_esposa }}"
                                                class="h-40 w-40 rounded-full object-cover mb-4">
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                                {{ $selectedPastor->nome_esposa }}</h3>
                                        </div>
                                        <div class="md:col-span-2 space-y-4">
                                            <div>
                                                <h4 class="font-semibold text-pink-500">Detalhes da Esposa</h4>
                                                <div class="border-t my-1"></div>
                                                <p><strong>Whatsapp:</strong>
                                                    {{ $selectedPastor->whatsapp_esposa ?? 'N/A' }}</p>
                                                <p><strong>Tempo de Obra:</strong> {{ $selectedPastor->obra ?? 'N/A' }}
                                                </p>
                                                <p><strong>Tempo de Casado:</strong>
                                                    {{ $selectedPastor->casado ?? 'N/A' }}
                                                </p>
                                                <p><strong>Missionária Consagrada?</strong>
                                                    {{ $selectedPastor->consagrada_esposa ? 'Sim' : 'Não' }}</p>
                                                <p><strong>Já foi presa?</strong>
                                                    {{ $selectedPastor->preso_esposa ? 'Sim' : 'Não' }}</p>
                                                <p><strong>Dias de trabalho:</strong>
                                                    {{ is_array($selectedPastor->trabalho_esposa) ? implode(', ', $selectedPastor->trabalho_esposa) : 'Não informado' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($confirmDeleteId)
                        <div
                            class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                            <div
                                class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                                <h3 class="text-xl font-bold">Confirmar Exclusão</h3>
                                <p class="my-4">Tem certeza que deseja apagar este registro?</p>
                                <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                        class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button><button
                                        wire:click="delete"
                                        class="bg-red-600 text-white py-2 px-4 rounded-lg">Apagar</button></div>
                            </div>
                        </div>
                    @endif

                    <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                        <table class="w-full table-auto">
                            <thead
                                class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                                <tr>
                                    <th class="py-3 px-6 text-left">Nome</th>
                                    <th class="py-3 px-6 text-left">Bloco</th>
                                    <th class="py-3 px-6 text-left">Cargo</th>
                                    <th class="py-3 px-6 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                                @forelse($results as $index => $pastor)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                        style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                        <td class="py-3 px-6 font-semibold">{{ $pastor->nome }}</td>
                                        <td class="py-3 px-6">{{ $pastor->bloco->nome ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 capitalize">{{ $pastor->cargo }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex items-center justify-center space-x-3">
                                                <button wire:click="view({{ $pastor->id }})"
                                                    class="w-5 transform hover:text-green-500"><svg fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg></button>
                                                <button wire:click="edit({{ $pastor->id }})"
                                                    class="w-5 transform hover:text-blue-500"><svg fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg></button>
                                                <button wire:click="confirmDelete({{ $pastor->id }})"
                                                    class="w-5 transform hover:text-red-500"><svg fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                                    </svg></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhum registro
                                            encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden space-y-4">
                        @forelse($results as $index => $pastor)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                                style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 space-y-1">
                                        <p class="text-lg font-bold">{{ $pastor->nome }}</p>
                                        <p class="text-sm text-gray-600">Bloco: {{ $pastor->bloco->nome ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-600 capitalize">Cargo: {{ $pastor->cargo }}</p>
                                    </div>
                                    <div class="flex flex-col space-y-4 ml-4">
                                        <button wire:click="view({{ $pastor->id }})" class="w-5 text-gray-500"><svg
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg></button>
                                        <button wire:click="edit({{ $pastor->id }})" class="w-5 text-gray-500"><svg
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg></button>
                                        <button wire:click="confirmDelete({{ $pastor->id }})"
                                            class="w-5 text-gray-500"><svg fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                            </svg></button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                                Nenhum registro encontrado.</div>
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
