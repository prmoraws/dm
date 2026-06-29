@section('title', 'Instrutores')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Instrutores') }}
        </h2>
    </div>
</x-slot>

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
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Buscar por nome ou bloco...">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Novo Instrutor
                    </button>
                </div>

                {{-- MODAL DE CRIAR/EDITAR CORRIGIDO --}}
                @if ($isOpen)
                    <div wire:key="create-edit-modal"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10"
                        @click.self="$wire.closeModal()">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-3xl mx-4 p-6 md:p-8"
                            x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false; progress = 0;"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $instrutor_id ? 'Editar Instrutor' : 'Criar Instrutor' }}</h2>
                            <form wire:submit.prevent="store" enctype="multipart/form-data" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nome"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nome
                                            Completo</label>
                                        <input type="text" id="nome" wire:model.defer="nome"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                        @error('nome')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="telefone"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Telefone</label>
                                        <input type="tel" id="telefone" wire:model.defer="telefone"
                                            x-mask="(99) 99999-9999"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            placeholder="(99) 99999-9999">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="rg"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">RG</label>
                                        <input type="text" id="rg" wire:model.defer="rg" x-mask="999.999.999-99"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            placeholder="999.999.999-9">
                                    </div>
                                    <div>
                                        <label for="cpf"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">CPF</label>
                                        <input type="text" id="cpf" wire:model.defer="cpf"
                                            x-mask="999.999.999-99"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            placeholder="999.999.999-99">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="bloco_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Bloco</label>
                                        <select id="bloco_id" wire:model.live="bloco_id"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                            <option value="">Selecione um Bloco</option>
                                            @foreach ($blocoOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('bloco_id')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="igreja_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Igreja</label>
                                        <select id="igreja_id" wire:model.defer="igreja_id"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            @if (empty($igrejas)) disabled @endif required>
                                            <option value="">Selecione uma Igreja</option>
                                            @foreach ($igrejas as $igreja)
                                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('igreja_id')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="categoria_id"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Categoria</label>
                                        <select id="categoria_id" wire:model.defer="categoria_id"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                            required>
                                            <option value="">Selecione...</option>
                                            @foreach ($categoriaOptions as $id => $nome)
                                                <option value="{{ $id }}">{{ $nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('categoria_id')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="profissao"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Profissão</label>
                                        <input type="text" id="profissao" wire:model.defer="profissao"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="testemunho"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Testemunho</label>
                                        <textarea id="testemunho" wire:model.defer="testemunho" rows="3"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"></textarea>
                                    </div>
                                    <div>
                                        <label for="carga"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Carga
                                            Horária</label>
                                        <input type="text" id="carga" wire:model.defer="carga"
                                            class="w-full rounded-md dark:text-gray-300 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                    </div>
                                </div>
                                <div
                                    class="pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Batismo
                                            </p>
                                            <div class="flex gap-4">
                                                <label class="flex items-center"><input type="checkbox"
                                                        wire:model="batismo" value="aguas"
                                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                        class="ml-2 text-sm text-gray-600 dark:text-gray-400">Nas
                                                        Águas</span></label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        wire:model="batismo" value="espirito"
                                                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                        class="ml-2 text-sm text-gray-600 dark:text-gray-400">Com
                                                        Espírito Santo</span></label>
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <label class="flex items-center"><input type="checkbox"
                                                    wire:model="certificado"
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">Certificado</span></label>
                                            <label class="flex items-center"><input type="checkbox"
                                                    wire:model="inscricao"
                                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"><span
                                                    class="ml-2 text-sm text-gray-600 dark:text-gray-400">Inscrição</span></label>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="foto"
                                            class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Foto</label>
                                        <div class="flex items-center gap-4">
                                            @if ($foto)
                                                <img src="{{ $foto->temporaryUrl() }}"
                                                    class="h-16 w-16 object-cover rounded-full">
                                            @elseif(is_string($selectedInstrutor?->foto) && $selectedInstrutor->foto)
                                                <img src="{{ asset('storage/' . $selectedInstrutor->foto) }}"
                                                    class="h-16 w-16 object-cover rounded-full">
                                            @endif
                                            <input id="foto" type="file" wire:model="foto"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900">
                                        </div>
                                        <div x-show="isUploading"
                                            class="mt-2 w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                            <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${progress}%`">
                                            </div>
                                        </div>
                                        @error('foto')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">{{ $instrutor_id ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedInstrutor)
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
                            <div class="flex justify-end items-start mb-4 pb-4">
                                <button wire:click="closeViewModal" type="button"
                                    class="p-2 -m-2 rounded-full text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span class="sr-only">Fechar modal</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-6 -mt-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ $selectedInstrutor->foto ? asset($selectedInstrutor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedInstrutor->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                        class="h-24 w-24 rounded-full object-cover shadow-md">
                                </div>
                                <div class="text-center sm:text-left">
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                                        {{ $selectedInstrutor->nome }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $selectedInstrutor->profissao }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $selectedInstrutor->telefone }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        RG: {{ $selectedInstrutor->rg ?: 'Não informado' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        CPF: {{ $selectedInstrutor->cpf ?: 'Não informado' }}</p>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 text-sm space-y-3">
                                <p><strong class="text-gray-500 dark:text-gray-400">Bloco:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedInstrutor->bloco->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Igreja:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedInstrutor->igreja->nome ?? 'N/A' }}</span>
                                </p>
                                <p><strong class="text-gray-500 dark:text-gray-400">Categoria:</strong> <span
                                        class="text-gray-900 dark:text-gray-200">{{ $selectedInstrutor->categoria->nome ?? 'N/A' }}</span>
                                </p>
                                <div class="flex gap-6">
                                    <p><strong class="text-gray-500 dark:text-gray-400">Bat. Águas:</strong> <span
                                            class="font-semibold {{ $selectedInstrutor->batismoAguas ? 'text-green-600' : 'text-red-600' }}">{{ $selectedInstrutor->batismoAguas ? 'Sim' : 'Não' }}</span>
                                    </p>
                                    <p><strong class="text-gray-500 dark:text-gray-400">Bat. Espírito
                                            Santo:</strong><span
                                            class="font-semibold {{ $selectedInstrutor->batismoEspirito ? 'text-green-600' : 'text-red-600' }}">{{ $selectedInstrutor->batismoEspirito ? 'Sim' : 'Não' }}</span>
                                    </p>
                                </div>
                                <div class="flex gap-6">
                                    <p><strong class="text-gray-500 dark:text-gray-400">Certificado:</strong> <span
                                            class="font-semibold {{ $selectedInstrutor->certificado ? 'text-green-600' : 'text-red-600' }}">{{ $selectedInstrutor->certificado ? 'Sim' : 'Não' }}</span>
                                    </p>
                                    <p><strong class="text-gray-500 dark:text-gray-400">Inscrição:</strong> <span
                                            class="font-semibold {{ $selectedInstrutor->inscricao ? 'text-green-600' : 'text-red-600' }}">{{ $selectedInstrutor->inscricao ? 'Sim' : 'Não' }}</span>
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <p class="font-semibold text-gray-500 dark:text-gray-400">Testemunho:</p>
                                    <p class="text-gray-800 dark:text-gray-300 italic mt-1">
                                        "{{ $selectedInstrutor->testemunho ?: 'Não informado' }}"</p>
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
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar este
                                registro? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-3">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold py-2 px-5 rounded-lg">Cancelar</button>
                                <button wire:click="delete"
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
                                <th class="py-3 px-6 text-left">Bloco</th>
                                <th class="py-3 px-6 text-left">Igreja</th>
                                <th class="py-3 px-6 text-left">Telefone</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $instrutor)
                                <tr wire:key="instrutor-{{ $instrutor->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 text-left flex items-center gap-3">
                                        <img src="{{ $instrutor->foto ? asset($instrutor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($instrutor->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                            class="h-8 w-8 rounded-full object-cover">
                                        <span>{{ $instrutor->nome }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $instrutor->bloco->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $instrutor->igreja->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $instrutor->telefone }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $instrutor->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $instrutor->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110"><svg
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $instrutor->id }})"
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Nenhum instrutor
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse ($results as $index => $instrutor)
                        <div wire:key="instrutor-card-{{ $instrutor->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $instrutor->foto ? asset($instrutor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($instrutor->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                        class="h-12 w-12 rounded-full object-cover">
                                    <div class="flex-1">
                                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ $instrutor->nome }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $instrutor->bloco->nome ?? 'N/A' }} /
                                            {{ $instrutor->igreja->nome ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col space-y-4 ml-2">
                                    <button wire:click="view({{ $instrutor->id }})"
                                        class="w-5 transform text-gray-500 hover:text-green-500 hover:scale-110"
                                        aria-label="Visualizar"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $instrutor->id }})"
                                        class="w-5 transform text-gray-500 hover:text-blue-500 hover:scale-110"
                                        aria-label="Editar"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $instrutor->id }})"
                                        class="w-5 transform text-gray-500 hover:text-red-500 hover:scale-110"
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
                            Nenhum instrutor encontrado.</div>
                    @endforelse
                </div>

                @if ($results->hasPages())
                    <div class="pt-4">{{ $results->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Animações e Máscaras --}}
    <script src="https://unpkg.com/alpinejs-masked-input/dist/masked-input.min.js"></script>
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
