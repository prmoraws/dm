@section('title', 'Entrega de Cestas')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4m16 0l-4 4m4-4l-4-4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12l4 4m-4-4l4-4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Entrega de Cestas') }}
        </h2>
    </div>
</x-slot>

<div>
    <!-- Main Content -->
    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Mensagens de Sucesso/Erro -->
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                    class="bg-teal-50 dark:bg-teal-900 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
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
                            class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-200"><svg
                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                </div>
            @endif
            @if (session()->has('error') || $errorMessage)
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                    class="bg-red-50 dark:bg-red-900 border-l-4 border-red-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ session('error') ?: $errorMessage }}</p>
                        </div>
                        <button @click="show = false"
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200"><svg
                                xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <!-- Search and Add Button -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Buscar por Nome, Identificado ou Contato..."
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Incluir Entrega
                    </button>
                </div>

                <!-- ############# INÍCIO DOS MODAIS INTEGRADOS ############# -->

                <!-- Modal de Criar/Editar -->
                @if ($isOpen)
                    <div wire:key="create-edit-modal" x-data="{ open: @json($isOpen) }" x-show="open"
                        x-on:keydown.escape.window="open && $wire.closeModal()"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        @click.self="$wire.closeModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 overflow-y-auto p-4">

                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-3xl mx-auto my-8 p-6 md:p-8">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $editId ? 'Editar Entrega' : 'Criar Entrega' }}
                            </h2>
                            <form wire:submit.prevent="save" enctype="multipart/form-data" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if (!$editId)
                                        <div>
                                            <label for="searchNome"
                                                class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Pesquisar
                                                Nome</label>
                                            <input id="searchNome" type="text"
                                                wire:model.live.debounce.300ms="searchNome"
                                                class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                                placeholder="Digite para pesquisar...">
                                        </div>
                                        <div>
                                            <label for="nome"
                                                class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Nome</label>
                                            <select id="nome" wire:model.live="nome"
                                                class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                                required>
                                                <option value="">Selecione um nome</option>
                                                <optgroup label="Terreiros">
                                                    @foreach ($terreiros as $terreiro)
                                                        <option value="{{ $terreiro }}">{{ $terreiro }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Instituições">
                                                    @foreach ($instituicoes as $instituicao)
                                                        <option value="{{ $instituicao }}">{{ $instituicao }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            @error('nome')
                                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <div>
                                            <label for="nome"
                                                class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Nome</label>
                                            <input id="nome" type="text" wire:model.live="nome" readonly
                                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 shadow-sm">
                                        </div>
                                    @endif
                                    <div>
                                        <label for="identificado"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Identificado</label>
                                        <input id="identificado" type="text" wire:model.live="identificado"
                                            readonly
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 shadow-sm">
                                        @error('identificado')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="contato"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Contato</label>
                                        <input id="contato" type="text" wire:model.live="contato" readonly
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 shadow-sm">
                                        @error('contato')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="cestas"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Quantidade
                                            de Cestas</label>
                                        <input id="cestas" type="number" wire:model.defer="cestas"
                                            class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                            placeholder="ex: 10" required>
                                        @error('cestas')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="observacao"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Observação</label>
                                        <textarea id="observacao" wire:model.defer="observacao"
                                            class="shadow-sm appearance-none border rounded-md w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150"
                                            placeholder="Observações adicionais"></textarea>
                                        @error('observacao')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="foto"
                                            class="block text-sm font-bold text-gray-800 dark:text-gray-300 mb-2">Foto</label>
                                        <input id="foto" type="file" wire:model="foto"
                                            accept="image/jpeg,image/png,image/jpg,image/gif"
                                            class="mt-1 block w-full text-gray-700 dark:text-gray-200">
                                        @if ($fotoAtual && !$foto)
                                            <div class="mt-2"><img src="{{ Storage::url($fotoAtual) }}"
                                                    alt="Foto atual da entrega"
                                                    class="h-20 w-20 object-cover rounded"></div>
                                        @endif
                                        <div wire:loading wire:target="foto" class="text-sm text-gray-500 mt-2">
                                            Carregando...</div>
                                        @error('foto')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 mt-4">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 font-semibold py-2 px-5 rounded-lg transition-all duration-150">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition-all duration-300 focus:outline-none focus:shadow-outline">{{ $editId ? 'Atualizar' : 'Criar' }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Modal de Visualização -->
                @if ($isViewOpen && $selectedCesta)
                    <div wire:key="view-modal" x-data="{ open: @json($isViewOpen) }" x-show="open"
                        x-on:keydown.escape.window="open && $wire.closeViewModal()"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        @click.self="$wire.closeViewModal()"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 overflow-y-auto p-4">
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-3xl mx-auto my-8 p-6 md:p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $selectedCesta->nome }}</h2>
                                <button wire:click="closeViewModal"
                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"><svg
                                        xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg></button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-md font-semibold text-blue-600 dark:text-blue-400 mb-2">Informações
                                    </h4>
                                    <div class="border-t-2 border-blue-600 dark:border-blue-400 my-2"></div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Identificado:</strong>
                                        {{ $selectedCesta->terreiro }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Contato:</strong>
                                        {{ $selectedCesta->contato }}</p>
                                </div>
                                <div>
                                    <h4 class="text-md font-semibold text-blue-600 dark:text-blue-400 mb-2">Detalhes da
                                        Entrega</h4>
                                    <div class="border-t-2 border-blue-600 dark:border-blue-400 my-2"></div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Cestas:</strong>
                                        {{ $selectedCesta->cestas }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Observação:</strong>
                                        {{ $selectedCesta->observacao ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <h4 class="text-md font-semibold text-blue-600 dark:text-blue-400 mb-2">Foto</h4>
                                    <div class="border-t-2 border-blue-600 dark:border-blue-400 my-2"></div>
                                     <img src="{{ $selectedCesta->foto ? asset($selectedCesta->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedCesta->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Modal de Confirmação de Exclusão -->
                @if ($confirmDeleteId)
                    <div wire:key="delete-modal" x-data="{ open: @json($confirmDeleteId) }" x-show="open"
                        x-on:keydown.escape.window="open && $wire.set('confirmDeleteId', null)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        @click.self="$wire.set('confirmDeleteId', null)"
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar Exclusão
                                </h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Tem certeza que deseja apagar esta
                                entrega? Esta ação não pode ser desfeita.</p>
                            <div class="flex justify-end gap-2">
                                <button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded">Cancelar</button>
                                <button wire:click="delete"
                                    class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded flex items-center gap-2"><svg
                                        xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                    </svg>Apagar</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabela de Resultados -->
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead>
                            <tr
                                class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('nome')">Nome
                                    @if ($sortField === 'nome')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('terreiro')">
                                    Identificado @if ($sortField === 'terreiro')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                                <th class="py-3 px-6 text-left cursor-pointer" wire:click="sortBy('cestas')">Cestas
                                    @if ($sortField === 'cestas')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                                <th class="py-3 px-6 text-center">Foto</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($cestasList as $cesta)
                                <tr wire:key="cesta-{{ $cesta->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="py-3 px-6 text-left truncate max-w-xs" title="{{ $cesta->nome }}">
                                        {{ $cesta->nome }}</td>
                                    <td class="py-3 px-6 text-left truncate max-w-xs" title="{{ $cesta->terreiro }}">
                                        {{ $cesta->terreiro }}</td>
                                    <td class="py-3 px-6 text-left">{{ $cesta->cestas }}</td>
                                    <td class="py-3 px-6 text-center">
                                        @if ($cesta->foto)
                                            <img src="{{ url($cesta->foto) }}" alt="Foto da entrega"
                                                class="h-16 w-16 object-cover rounded-md mx-auto">
                                        @else
                                            <span class="text-xs text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $cesta->id }})"
                                                class="w-5 transform hover:text-green-500 hover:scale-110 transition-all"
                                                aria-label="Ver"><svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $cesta->id }})"
                                                class="w-5 transform hover:text-blue-500 hover:scale-110 transition-all"
                                                aria-label="Editar"><svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $cesta->id }})"
                                                class="w-5 transform hover:text-red-500 hover:scale-110 transition-all"
                                                aria-label="Excluir"><svg xmlns="http://www.w3.org/2000/svg"
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Nenhuma entrega
                                        encontrada para "{{ $search }}".</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Lista para Mobile -->
                <div class="md:hidden space-y-4">
                    @forelse ($cestasList as $cesta)
                        <div wire:key="cesta-card-{{ $cesta->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $cesta->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Identificado:
                                        {{ $cesta->terreiro }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Cestas: {{ $cesta->cestas }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    @if ($cesta->foto)
                                        <img src="{{ url($cesta->foto) }}" alt="Foto da entrega"
                                            class="h-20 w-20 object-cover rounded-md">
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end space-x-4 mt-4">
                                <button wire:click="view({{ $cesta->id }})"
                                    class="w-6 text-gray-500 hover:text-green-500" aria-label="Ver"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg></button>
                                <button wire:click="edit({{ $cesta->id }})"
                                    class="w-6 text-gray-500 hover:text-blue-500" aria-label="Editar"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg></button>
                                <button wire:click="confirmDelete({{ $cesta->id }})"
                                    class="w-6 text-gray-500 hover:text-red-500" aria-label="Excluir"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12" />
                                    </svg></button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhuma entrega encontrada.</div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if ($cestasList->hasPages())
                    <div class="mt-6">
                        {{ $cestasList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
