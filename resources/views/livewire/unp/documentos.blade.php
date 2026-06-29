@section('title', 'Gerenciador de Documentos')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Gerenciador de Documentos') }}
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
                        {{ session('message') ?? session('error') }}</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Documento
                    </button>
                </div>

                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-2xl p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $documento_id ? 'Editar' : 'Criar' }} Documento</h2>
                            <form wire:submit.prevent="store" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label for="nome" class="block text-sm font-bold mb-2">Nome
                                            (Identificador)</label><input type="text" wire:model.defer="nome"
                                            id="nome" class="w-full rounded-md dark:bg-gray-800">
                                        @error('nome')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div><label for="classe"
                                            class="block text-sm font-bold mb-2">Classe</label><select
                                            wire:model.defer="classe" id="classe"
                                            class="w-full rounded-md dark:bg-gray-800">
                                            <option value="">Selecione...</option>
                                            @foreach ($classeOptions as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('classe')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div><label for="titulo" class="block text-sm font-bold mb-2">Título</label><input
                                        type="text" wire:model.defer="titulo" id="titulo"
                                        class="w-full rounded-md dark:bg-gray-800">
                                    @error('titulo')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div><label for="descricao" class="block text-sm font-bold mb-2">Descrição</label>
                                    <textarea wire:model.defer="descricao" id="descricao" rows="3" class="w-full rounded-md dark:bg-gray-800"></textarea>
                                </div>
                                <div>
                                    <label for="upload" class="block text-sm font-bold mb-2">Adicionar Novos
                                        Arquivos</label>
                                    <input type="file" wire:model="upload" id="upload" multiple
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                    <div wire:loading wire:target="upload" class="text-sm text-gray-500 mt-2">Carregando
                                        arquivos...</div>
                                    @error('upload.*')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if (!empty($existing_files))
                                    <div class="pt-4 border-t dark:border-gray-700">
                                        <p class="text-sm font-bold mb-2">Arquivos Anexados:</p>
                                        <ul class="space-y-2">
                                            @foreach ($existing_files as $fileId => $fileName)
                                                <li
                                                    class="flex items-center justify-between bg-gray-200 dark:bg-gray-700 p-2 rounded">
                                                    <span class="text-sm truncate">{{ $fileName }}</span>
                                                    <button type="button"
                                                        wire:click="removeFile('{{ $fileId }}')"
                                                        class="text-red-500 hover:text-red-700 text-xs font-bold">REMOVER</button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="flex justify-end gap-3 pt-4"><button type="button" wire:click="closeModal"
                                        class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button><button type="submit"
                                        wire:loading.attr="disabled"
                                        class="bg-blue-600 text-white py-2 px-4 rounded-lg">Salvar</button></div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedDocumento)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 p-4 flex items-center justify-center">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-lg p-6">
                            <div class="flex justify-between items-start mb-4 pb-4 border-b dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $selectedDocumento->titulo }}</h2>
                                <button wire:click="closeViewModal"
                                    class="p-2 -m-2 rounded-full text-gray-400 hover:bg-gray-200">&times;</button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-bold text-gray-500">Nome Identificador:</p>
                                    <p>{{ $selectedDocumento->nome }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-500">Classe:</p>
                                    <p>{{ $classeOptions[$selectedDocumento->classe] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-500">Descrição:</p>
                                    <p class="whitespace-pre-wrap">{{ $selectedDocumento->descricao ?? 'Nenhuma.' }}
                                    </p>
                                </div>
                                @if (!empty($selectedDocumento->upload))
                                    <div>
                                        <p class="text-sm font-bold text-gray-500">Arquivos:</p>
                                        <ul class="mt-2 space-y-2">
                                            @foreach ($selectedDocumento->upload as $fileId => $fileName)
                                                <li
                                                    class="flex items-center justify-between text-sm bg-gray-200 dark:bg-gray-800 p-2 rounded-md">
                                                    <span class="truncate">{{ $fileName }}</span>
                                                    <div class="flex items-center space-x-3 ml-2">
                                                        <a href="https://drive.google.com/file/d/{{ $fileId }}/view"
                                                            target="_blank" title="Visualizar no Google Drive"
                                                            class="text-blue-500 hover:text-blue-700">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                        <a href="https://docs.google.com/uc?id={{ $fileId }}&export=download"
                                                            target="_blank" title="Baixar Arquivo"
                                                            class="text-gray-500 hover:text-gray-700">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <h3 class="text-xl font-bold">Confirmar Exclusão</h3>
                            <p class="my-4">Tem certeza que deseja apagar este documento e seus arquivos?</p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmDeleteId', null)"
                                    class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button><button
                                    wire:click="delete"
                                    class="bg-red-600 text-white py-2 px-4 rounded-lg">Apagar</button></div>
                        </div>
                    </div>
                @endif

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Título</th>
                                <th class="py-3 px-6 text-left">Classe</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600 dark:text-gray-300">
                            @forelse($results as $index => $doc)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 font-semibold">{{ $doc->nome }}</td>
                                    <td class="py-3 px-6">{{ $doc->titulo }}</td>
                                    <td class="py-3 px-6">{{ $classeOptions[$doc->classe] ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $doc->id }})"
                                                class="w-5 transform hover:text-green-500"><svg fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $doc->id }})"
                                                class="w-5 transform hover:text-blue-500"><svg fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $doc->id }})"
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
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhum documento
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $doc)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $doc->nome }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $doc->titulo }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $doc->id }})" class="w-5 text-gray-500"><svg
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $doc->id }})" class="w-5 text-gray-500"><svg
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $doc->id }})"
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
                            Nenhum documento encontrado.</div>
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
