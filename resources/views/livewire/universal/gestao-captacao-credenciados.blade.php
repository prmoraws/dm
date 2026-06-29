<div>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestão de Captação - Credenciados') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session()->has('message'))
                <div
                    class="mb-4 p-4 bg-teal-50 dark:bg-teal-900/30 border-l-4 border-teal-500 text-teal-800 dark:text-teal-200 rounded-lg shadow-sm">
                    {{ session('message') }}
                </div>
            @endif

            <div
                class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Buscar por nome ou CPF..."
                        class="w-full md:w-1/3 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-blue-500">

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $results->total() }} registros pendentes
                    </span>
                </div>

                {{-- Tabela Desktop --}}
                <div class="hidden md:block">
                    <table class="w-full text-sm">
                        <thead
                            class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">Nome</th>
                                <th class="px-6 py-4 text-left">WhatsApp</th>
                                <th class="px-6 py-4 text-left">Data de Envio</th>
                                <th class="px-6 py-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($results as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 font-medium dark:text-gray-100">{{ $item->nome }}</td>
                                    <td class="px-6 py-4 dark:text-gray-300">{{ $item->celular }}</td>
                                    <td class="px-6 py-4 dark:text-gray-400 text-xs">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="view({{ $item->id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all text-xs shadow-sm">
                                            REVISAR DADOS
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Nenhuma
                                        captação pendente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile List --}}
                <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($results as $item)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold dark:text-gray-100">{{ $item->nome }}</h4>
                                <p class="text-xs text-gray-500">{{ $item->celular }}</p>
                            </div>
                            <button wire:click="view({{ $item->id }})"
                                class="bg-blue-600 text-white p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $results->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Revisão --}}
    @if ($isViewOpen && $selectedCaptacao)
        <div
            class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 overflow-y-auto p-4 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl p-6 relative animate-fade-in">
                <button wire:click="$set('isViewOpen', false)"
                    class="absolute top-4 right-4 text-3xl font-bold">&times;</button>

                <h3 class="text-xl font-bold mb-6 border-b pb-2">Revisar Captação: {{ $selectedCaptacao->nome }}</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Lado Esquerdo: Fotos --}}
                    <div class="space-y-4">
                        <p class="text-xs font-bold uppercase text-gray-400">Fotos Enviadas (Pasta Captura)</p>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <p class="text-[10px] text-center mb-1">PERFIL</p>
                                <img src="{{ asset($selectedCaptacao->foto) }}"
                                    class="h-24 w-full object-cover rounded-lg border">
                            </div>
                            <div>
                                <p class="text-[10px] text-center mb-1">ID FRENTE</p>
                                <img src="{{ asset($selectedCaptacao->identidade_frente) }}"
                                    class="h-24 w-full object-cover rounded-lg border">
                            </div>
                            <div>
                                <p class="text-[10px] text-center mb-1">ID VERSO</p>
                                <img src="{{ asset($selectedCaptacao->identidade_verso) }}"
                                    class="h-24 w-full object-cover rounded-lg border">
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-xs font-bold uppercase text-blue-500 mb-2">Credenciais
                                ({{ count($selectedCaptacao->credenciais_payload ?? []) }})</p>
                            <div class="space-y-2 h-40 overflow-y-auto pr-2">
                                @foreach ($selectedCaptacao->credenciais_payload ?? [] as $cp)
                                    <div class="p-2 bg-gray-50 dark:bg-gray-800 rounded text-[10px] border flex gap-2">
                                        <img src="{{ asset($cp['foto_frente']) }}"
                                            class="h-10 w-10 object-cover rounded">
                                        <img src="{{ asset($cp['foto_verso']) }}"
                                            class="h-10 w-10 object-cover rounded">
                                        <span class="flex-1 self-center font-bold">Presídio ID:
                                            {{ $cp['presidio_id'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Lado Direito: Dados --}}
                    <div class="space-y-4 text-sm">
                        <p><strong>CPF:</strong> {{ $selectedCaptacao->cpf }}</p>
                        <p><strong>Local:</strong> {{ $selectedCaptacao->bairro }}, {{ $selectedCaptacao->endereco }}
                        </p>
                        <p><strong>Testemunho:</strong> {{ Str::limit($selectedCaptacao->testemunho, 150) }}</p>

                        <div class="flex gap-4 pt-6 border-t mt-6">
                            <button wire:click="approve({{ $selectedCaptacao->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg transition-all">APROVAR
                                E MOVER</button>
                            <button wire:click="confirmDelete({{ $selectedCaptacao->id }})"
                                class="flex-1 bg-red-100 text-red-700 font-bold py-3 rounded-xl hover:bg-red-200 transition-all">
                                REJEITAR / APAGAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Modal de Confirmação de Exclusão --}}
    @if ($confirmingDeletion)
        <div class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-fade-in">
                <div class="text-center">
                    <div
                        class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold dark:text-white">Confirmar Exclusão?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Esta ação é permanente. Todos os dados e fotos enviados serão apagados do servidor.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button wire:click="$set('confirmingDeletion', false)"
                        class="py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-bold">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                        class="py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700">
                        Sim, Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
