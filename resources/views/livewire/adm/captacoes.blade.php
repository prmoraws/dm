@section('title', 'Gerenciar Captações')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Gerenciar Captações') }}
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
                            placeholder="Buscar por nome ou CPF..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                </div>

                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Editar Captação de
                                {{ $state['nome'] ?? '' }}</h2>
                            <form wire:submit.prevent="update" class="space-y-6">
                                <section>
                                    <h3
                                        class="text-lg font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                        Dados do Pastor</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="state.bloco_id"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label>
                                            <select id="state.bloco_id" wire:model.live="state.bloco_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                                <option value="">Selecione</option>
                                                @foreach ($blocoOptions as $bloco)
                                                    <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="state.regiao_id"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label>
                                            <select id="state.regiao_id" wire:model.defer="state.regiao_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                @disabled(empty($regiaoOptions))>
                                                <option value="">Selecione um bloco</option>
                                                @foreach ($regiaoOptions as $regiao)
                                                    <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="state.cargo"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
                                            <select id="state.cargo" wire:model.defer="state.cargo"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                                <option value="responsavel">Responsável</option>
                                                <option value="auxiliar">Auxiliar</option>
                                            </select>
                                        </div>
                                    </div>
                                </section>
                                <div class="flex justify-end gap-3 pt-6 border-t dark:border-gray-700">
                                    <button type="button" wire:click="closeModal"
                                        class="bg-gray-300 hover:bg-gray-400 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Salvar
                                        Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($isViewOpen && $selectedCaptacao)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                            <div class="flex justify-between items-start pb-4 border-b dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Revisão da Captação</h2>
                                <button wire:click="closeViewModal"
                                    class="p-2 -m-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">&times;</button>
                            </div>
                            <div
                                class="mt-6 text-sm text-gray-700 dark:text-gray-300 space-y-6 max-h-[70vh] overflow-y-auto">
                                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-1 flex flex-col items-center space-y-4">
                                        @if (!empty($selectedCaptacao->payload['foto']))
                                            <div>
                                                <p class="text-xs font-bold text-center mb-1 dark:text-gray-400">FOTO
                                                    PASTOR</p>
                                                <img src="{{ asset($selectedCaptacao->payload['foto']) }}"
                                                    class="h-40 w-40 rounded-md object-cover border-2 border-gray-200 dark:border-gray-700">
                                            </div>
                                        @endif
                                        @if (!empty($selectedCaptacao->payload['foto_esposa']))
                                            <div>
                                                <p class="text-xs font-bold text-center mb-1 dark:text-gray-400">FOTO
                                                    ESPOSA</p>
                                                <img src="{{ asset($selectedCaptacao->payload['foto_esposa']) }}"
                                                    class="h-40 w-40 rounded-md object-cover border-2 border-gray-200 dark:border-gray-700">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="md:col-span-2 space-y-4">
                                        <div>
                                            <h4 class="font-semibold text-blue-500">Dados do Pastor</h4>
                                            <div class="border-t my-1 dark:border-gray-700"></div>
                                            <p><strong>Nome:</strong> {{ $selectedCaptacao->payload['nome'] ?? 'N/A' }}
                                            </p>
                                            <p><strong>CPF:</strong> {{ $selectedCaptacao->payload['cpf'] ?? 'N/A' }}
                                            </p>
                                            <p><strong>Cargo:</strong> <span
                                                    class="capitalize">{{ $selectedCaptacao->payload['cargo'] ?? 'N/A' }}</span>
                                            </p>
                                            <p><strong>Whatsapp:</strong>
                                                {{ $selectedCaptacao->payload['whatsapp'] ?? 'N/A' }}</p>
                                            <p><strong>Email:</strong>
                                                {{ $selectedCaptacao->payload['email'] ?? 'N/A' }}</p>
                                            <p><strong>Nascimento:</strong>
                                                {{ $selectedCaptacao->payload['nascimento'] ?? 'N/A' }}</p>
                                            <p><strong>Entrada no Grupo:</strong>
                                                {{ $selectedCaptacao->payload['entrada'] ?? 'N/A' }}</p>
                                            <p><strong>Tempo no Estado/Bloco:</strong>
                                                {{ $selectedCaptacao->payload['chegada'] ?? 'N/A' }}</p>
                                            <p><strong>Já foi Preso:</strong>
                                                {{ $selectedCaptacao->payload['preso'] ?? 0 ? 'Sim' : 'Não' }}</p>
                                            <p><strong>Dias de Trabalho:</strong>
                                                {{ is_array($selectedCaptacao->payload['trabalho']) && !empty($selectedCaptacao->payload['trabalho']) ? implode(', ', $selectedCaptacao->payload['trabalho']) : 'Não informado' }}
                                            </p>
                                        </div>
                                        @if (!empty($selectedCaptacao->payload['nome_esposa']))
                                            <div class="pt-4">
                                                <h4 class="font-semibold text-pink-500">Dados da Esposa</h4>
                                                <div class="border-t my-1 dark:border-gray-700"></div>
                                                <p><strong>Nome:</strong>
                                                    {{ $selectedCaptacao->payload['nome_esposa'] ?? 'N/A' }}</p>
                                                <p><strong>Whatsapp:</strong>
                                                    {{ $selectedCaptacao->payload['whatsapp_esposa'] ?? 'N/A' }}</p>
                                                <p><strong>Tempo de Obra:</strong>
                                                    {{ $selectedCaptacao->payload['obra'] ?? 'N/A' }}</p>
                                                <p><strong>Tempo de Casado:</strong>
                                                    {{ $selectedCaptacao->payload['casado'] ?? 'N/A' }}</p>
                                                <p><strong>Missionária Consagrada:</strong>
                                                    {{ $selectedCaptacao->payload['consagrada_esposa'] ?? 0 ? 'Sim' : 'Não' }}
                                                </p>
                                                <p><strong>Já foi Presa:</strong>
                                                    {{ $selectedCaptacao->payload['preso_esposa'] ?? 0 ? 'Sim' : 'Não' }}
                                                </p>
                                                <p><strong>Dias de Trabalho:</strong>
                                                    {{ is_array($selectedCaptacao->payload['trabalho_esposa']) && !empty($selectedCaptacao->payload['trabalho_esposa']) ? implode(', ', $selectedCaptacao->payload['trabalho_esposa']) : 'Não informado' }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </section>

                                @if (!empty($selectedCaptacao->payload['modelo']))
                                    <section class="pt-4 border-t dark:border-gray-700">
                                        <h3 class="text-lg font-semibold text-indigo-500">Veículo</h3>
                                        <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-2">
                                            <div>
                                                <p><strong>Modelo:</strong>
                                                    {{ $selectedCaptacao->payload['modelo'] ?? 'N/A' }}</p>
                                                <p><strong>Ano:</strong>
                                                    {{ $selectedCaptacao->payload['ano'] ?? 'N/A' }}</p>
                                                <p><strong>Placa:</strong>
                                                    {{ $selectedCaptacao->payload['placa'] ?? 'N/A' }}</p>
                                                <p><strong>KM:</strong> {{ $selectedCaptacao->payload['km'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p><strong>Demanda:</strong></p>
                                                <p class="whitespace-pre-wrap">
                                                    {{ $selectedCaptacao->payload['demanda'] ?? 'Nenhuma.' }}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                            @if (!empty($selectedCaptacao->payload['foto_frente']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_frente']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Frente</p>
                                                </div>
                                            @endif
                                            @if (!empty($selectedCaptacao->payload['foto_tras']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_tras']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Traseira</p>
                                                </div>
                                            @endif
                                            @if (!empty($selectedCaptacao->payload['foto_direita']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_direita']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Direita</p>
                                                </div>
                                            @endif
                                            @if (!empty($selectedCaptacao->payload['foto_esquerda']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_esquerda']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Esquerda</p>
                                                </div>
                                            @endif
                                            @if (!empty($selectedCaptacao->payload['foto_dentro']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_dentro']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Interior</p>
                                                </div>
                                            @endif
                                            @if (!empty($selectedCaptacao->payload['foto_cambio']))
                                                <div class="text-center"><img
                                                        src="{{ asset($selectedCaptacao->payload['foto_cambio']) }}"
                                                        class="h-24 w-full object-cover rounded">
                                                    <p class="text-xs mt-1 text-gray-500">Câmbio</p>
                                                </div>
                                            @endif
                                        </div>
                                    </section>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmActionId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <h3 class="text-xl font-bold">Confirmar Ação</h3>
                            <p class="my-4">Tem certeza que deseja
                                <strong>{{ $actionType === 'approve' ? 'APROVAR' : 'REJEITAR' }}</strong> esta
                                captação?
                            </p>
                            <div class="flex justify-end gap-3"><button wire:click="$set('confirmActionId', null)"
                                    class="bg-gray-300 py-2 px-4 rounded-lg">Cancelar</button><button
                                    wire:click="performAction"
                                    class="{{ $actionType === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white py-2 px-4 rounded-lg">Confirmar</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">CPF</th>
                                <th class="py-3 px-6 text-left">Data da Submissão</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse($results as $index => $captacao)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 font-semibold">{{ $captacao->nome }}</td>
                                    <td class="py-3 px-6">{{ $captacao->cpf }}</td>
                                    <td class="py-3 px-6">{{ $captacao->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button wire:click="view({{ $captacao->id }})"
                                                class="text-xs py-1 px-3 rounded-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">Visualizar</button>
                                            <button wire:click="edit({{ $captacao->id }})"
                                                class="text-xs py-1 px-3 rounded-full bg-blue-200 text-blue-800 hover:bg-blue-300">Editar</button>
                                            <button wire:click="confirmAction({{ $captacao->id }}, 'approve')"
                                                class="text-xs py-1 px-3 rounded-full bg-green-200 text-green-800 hover:bg-green-300">Aprovar</button>
                                            <button wire:click="confirmAction({{ $captacao->id }}, 'reject')"
                                                class="text-xs py-1 px-3 rounded-full bg-red-200 text-red-800 hover:bg-red-300">Rejeitar</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 px-6 text-center text-gray-500">Nenhuma captação
                                        pendente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse($results as $captacao)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold">{{ $captacao->nome }}</p>
                                    <p class="text-sm text-gray-600">CPF: {{ $captacao->cpf }}</p>
                                    <p class="text-sm text-gray-600">Data:
                                        {{ $captacao->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button wire:click="view({{ $captacao->id }})"
                                    class="text-xs py-1 px-3 rounded-full bg-gray-200">Visualizar</button>
                                <button wire:click="edit({{ $captacao->id }})"
                                    class="text-xs py-1 px-3 rounded-full bg-blue-200 text-blue-800">Editar</button>
                                <button wire:click="confirmAction({{ $captacao->id }}, 'approve')"
                                    class="text-xs py-1 px-3 rounded-full bg-green-200 text-green-800">Aprovar</button>
                                <button wire:click="confirmAction({{ $captacao->id }}, 'reject')"
                                    class="text-xs py-1 px-3 rounded-full bg-red-200 text-red-800">Rejeitar</button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhuma captação pendente.</div>
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
