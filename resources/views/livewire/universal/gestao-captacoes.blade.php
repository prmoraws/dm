@section('title', 'Gestão de Captações')

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestão de Captações UNP
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <x-validation-errors class="mb-4" />
                @if (session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                        <p>{{ session('message') }}</p>
                    </div>
                @endif
                @if (session()->has('error'))
                     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <div class="w-full md:w-1/3">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nome, email ou celular..." class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-900 p-1 rounded-lg">
                        <button wire:click="setFilterStatus('pendente')" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filterStatus === 'pendente' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">Pendentes</button>
                        <button wire:click="setFilterStatus('aprovado')" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filterStatus === 'aprovado' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">Aprovados</button>
                        <button wire:click="setFilterStatus('rejeitado')" class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ $filterStatus === 'rejeitado' ? 'bg-blue-600 text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">Rejeitados</button>
                    </div>
                </div>

                {{-- Tabela para Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Celular / Igreja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($captacoes as $captacao)
                                <tr wire:key="captacao-{{ $captacao->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $captacao->nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ $captacao->celular }}</div>
                                        <div class="text-xs">{{ $captacao->igreja->nome ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $captacao->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <div class="flex justify-center items-center space-x-3">
                                            {{-- ALTERAÇÃO: Adicionado botão de impressão para Aprovados --}}
                                            @if($filterStatus === 'aprovado')
                                                @php
                                                    // Busca a pessoa correspondente pelo email para gerar o link de impressão.
                                                    $pessoa = \App\Models\Universal\Pessoa::where('email', $captacao->email)->first();
                                                @endphp
                                                @if($pessoa)
                                                    <a href="{{ route('secretaria.pessoas.print.ficha', $pessoa->id) }}" target="_blank" class="text-gray-400 hover:text-purple-500 transition" title="Imprimir Ficha">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    </a>
                                                @endif
                                            @endif

                                            <button wire:click="view({{ $captacao->id }})" class="text-gray-400 hover:text-blue-500 transition" title="Visualizar"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                            
                                            @if($filterStatus === 'pendente')
                                                <button wire:click="approve({{ $captacao->id }})" wire:confirm="Tem certeza que deseja aprovar este cadastro? Um novo registro de Pessoa será criado." class="text-gray-400 hover:text-green-500 transition" title="Aprovar"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>
                                                <button wire:click="confirmRejection({{ $captacao->id }})" class="text-gray-400 hover:text-red-500 transition" title="Rejeitar"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4h4a2 2 0 002-2V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v0m12 0L9 7m6 0v12"></path></svg></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum registro encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards para Mobile --}}
                <div class="md:hidden space-y-4">
                    @forelse ($captacoes as $captacao)
                        <div wire:key="captacao-mobile-{{ $captacao->id }}" class="bg-white dark:bg-gray-800/50 rounded-lg shadow p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $captacao->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $captacao->celular }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $captacao->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Igreja: {{ $captacao->igreja->nome ?? 'N/A' }}</div>
                            <div class="border-t dark:border-gray-700 pt-3 flex justify-end items-center space-x-4">
                                {{-- ALTERAÇÃO: Adicionado botão de impressão para Aprovados na visão mobile --}}
                                @if($filterStatus === 'aprovado')
                                    @php
                                        $pessoa = \App\Models\Universal\Pessoa::where('email', $captacao->email)->first();
                                    @endphp
                                    @if($pessoa)
                                        <a href="{{ route('secretaria.pessoas.print.ficha', $pessoa->id) }}" target="_blank" class="text-purple-500 font-medium text-sm">Imprimir</a>
                                    @endif
                                @endif

                                @if($filterStatus === 'pendente')
                                    <button wire:click="confirmRejection({{ $captacao->id }})" class="text-red-500 font-medium text-sm">Rejeitar</button>
                                    <button wire:click="approve({{ $captacao->id }})" wire:confirm="Aprovar este cadastro?" class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm font-semibold shadow">Aprovar</button>
                                @endif
                                <button wire:click="view({{ $captacao->id }})" class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm font-semibold shadow">Visualizar</button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-sm text-gray-500 py-8">Nenhum registro encontrado.</div>
                    @endforelse
                </div>

                <div class="mt-6">{{ $captacoes->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Modal de Visualização --}}
    @if($isViewOpen && $selectedCaptacao)
    <div x-data="{ open: @entangle('isViewOpen'), tab: 'pessoal' }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm z-50 flex justify-center items-center p-4" x-cloak>
        <div @click.outside="open = false" class="bg-gray-100 dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <img src="{{ $selectedCaptacao->foto ? asset($selectedCaptacao->foto) : 'https://ui-avatars.com/api/?name='.urlencode($selectedCaptacao->nome).'&background=random' }}" class="h-16 w-16 object-cover rounded-full border-2 border-white dark:border-gray-600">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $selectedCaptacao->nome }}</h3>
                        <span @class([
                            'px-2 py-0.5 text-xs font-semibold rounded-full',
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' => $selectedCaptacao->status === 'pendente',
                            'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' => $selectedCaptacao->status === 'aprovado',
                            'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' => $selectedCaptacao->status === 'rejeitado',
                        ])>{{ ucfirst($selectedCaptacao->status) }}</span>
                    </div>
                </div>
                <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition text-2xl">&times;</button>
            </div>
            <div class="p-6 flex-grow overflow-y-auto">
                <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                    <nav class="-mb-px flex space-x-6">
                        <button @click="tab = 'pessoal'" :class="{'border-blue-500 text-blue-600': tab === 'pessoal', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-600': tab !== 'pessoal'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">Pessoal & Contato</button>
                        <button @click="tab = 'eclesiastico'" :class="{'border-blue-500 text-blue-600': tab === 'eclesiastico', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-600': tab !== 'eclesiastico'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">Eclesiástico</button>
                        <button @click="tab = 'jornada'" :class="{'border-blue-500 text-blue-600': tab === 'jornada', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:border-gray-600': tab !== 'jornada'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">Jornada</button>
                    </nav>
                </div>

                <div x-show="tab === 'pessoal'">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="sm:col-span-1 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">Nome</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->nome }}</dd></div></div>
                        <div class="sm:col-span-1 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">Celular</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->celular }}</dd></div></div>
                        <div class="sm:col-span-1 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">Email</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->email ?: 'Não informado' }}</dd></div></div>
                        <div class="sm:col-span-1 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 21h14a2 2 0 002-2v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">Profissão</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->profissao ?: 'Não informado' }}</dd></div></div>
                        
                        {{-- CORREÇÃO: Campo de CEP adicionado --}}
                        <div class="sm:col-span-1 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">CEP</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->cep ?: 'Não informado' }}</dd></div></div>
                        
                        <div class="sm:col-span-2 flex items-start gap-3"><svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg><div><dt class="font-semibold text-gray-800 dark:text-gray-200">Endereço</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->endereco }}, {{ $selectedCaptacao->bairro }} - {{ $selectedCaptacao->cidade?->nome }} / {{ $selectedCaptacao->cidade?->estado?->uf }}</dd></div></div>
                    </dl>
                </div>
                <div x-show="tab === 'eclesiastico'">
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Bloco</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->bloco?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Região</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->regiao?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Igreja</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->igreja?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Categoria</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->categoria?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Cargo</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->cargo?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Grupo</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->grupo?->nome ?: 'N/A' }}</dd></div>
                    </dl>
                </div>
                <div x-show="tab === 'jornada'">
                     <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        {{-- CORREÇÃO: Campo de Aptidões adicionado --}}
                        <div class="sm:col-span-2"><dt class="font-semibold text-gray-800 dark:text-gray-200">Aptidões</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->aptidoes ?: 'Não informado' }}</dd></div>

                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Conversão</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->conversao ? $selectedCaptacao->conversao->format('d/m/Y') : 'Não informado' }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Entrada na Obra</dt><dd class="text-gray-600 dark:text-gray-400">{{ $selectedCaptacao->obra ? $selectedCaptacao->obra->format('d/m/Y') : 'Não informado' }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Trabalho</dt><dd class="text-gray-600 dark:text-gray-400 capitalize">{{ !empty($selectedCaptacao->trabalho) ? implode(', ', $selectedCaptacao->trabalho) : 'Não informado' }}</dd></div>
                        <div><dt class="font-semibold text-gray-800 dark:text-gray-200">Batismo</dt><dd class="text-gray-600 dark:text-gray-400 capitalize">{{ !empty($selectedCaptacao->batismo) ? implode(', ', $selectedCaptacao->batismo) : 'Não informado' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="font-semibold text-gray-800 dark:text-gray-200">Situação</dt><dd class="text-gray-600 dark:text-gray-400 capitalize">{{ !empty($selectedCaptacao->preso) ? implode(', ', $selectedCaptacao->preso) : 'Não informado' }}</dd></div>
                        @if($selectedCaptacao->testemunho)
                        <div class="sm:col-span-2"><dt class="font-semibold text-gray-800 dark:text-gray-200">Testemunho</dt><dd class="text-gray-600 dark:text-gray-400 italic mt-1">"{{ $selectedCaptacao->testemunho }}"</dd></div>
                        @endif
                    </dl>
                </div>
            </div>
            
            {{-- Rodapé do Modal com botões reativos e alinhamento responsivo flex-col-reverse sm:flex-row --}}
            <div class="p-4 bg-gray-200 dark:bg-gray-800/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <x-secondary-button wire:click="closeViewModal" class="w-full sm:w-auto justify-center">Fechar</x-secondary-button>
                
                @if($selectedCaptacao->status === 'pendente')
                    <x-danger-button wire:click="confirmRejection({{ $selectedCaptacao->id }})" class="w-full sm:w-auto justify-center">Rejeitar</x-danger-button>
                    <x-button wire:click="approve({{ $selectedCaptacao->id }})" wire:confirm="Tem certeza que deseja aprovar este cadastro? Um novo registro de Pessoa será criado." class="w-full sm:w-auto justify-center bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold">Aprovar</x-button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Modal de Rejeição --}}
    @if($isRejectionModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex justify-center items-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar Rejeição</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Tem certeza que deseja rejeitar e excluir permanentemente o cadastro de **{{ $selectedCaptacao->nome }}**?</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-2">
                <x-secondary-button @click="$wire.isRejectionModalOpen = false">Cancelar</x-secondary-button>
                <x-danger-button wire:click="reject">Sim, Excluir</x-danger-button>
            </div>
        </div>
    </div>
    @endif
</div>