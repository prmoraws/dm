<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gerenciamento de Candidatos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if (session()->has('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <x-button wire:click="create">
                        Adicionar Candidato
                    </x-button>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Pesquisar..." class="form-input rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-700">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Partido</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:border-gray-700">
                            @forelse ($candidatos as $candidato)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $candidato->nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $candidato->partido }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $candidato->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Editar</button>
                                        <button wire:click="delete({{ $candidato->id }})" wire:confirm="Tem certeza que deseja excluir este candidato?" class="text-red-600 hover:text-red-900 dark:text-red-400 ml-4">Excluir</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum candidato encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $candidatos->links() }}</div>
            </div>
        </div>
    </div>

    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $candidateId ? 'Editar Candidato' : 'Adicionar Novo Candidato' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="nome" value="Nome do Candidato" />
                    <x-input id="nome" type="text" class="mt-1 block w-full" wire:model="nome" />
                    <x-input-error for="nome" class="mt-2" />
                </div>
                <div>
                    <x-label for="partido" value="Partido (Opcional)" />
                    <x-input id="partido" type="text" class="mt-1 block w-full" wire:model="partido" />
                    <x-input-error for="partido" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Cancelar
            </x-secondary-button>

            <x-button class="ms-3" wire:click="{{ $candidateId ? 'update' : 'store' }}">
                Salvar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>