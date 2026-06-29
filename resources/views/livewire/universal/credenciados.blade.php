@section('title', 'Credenciados')

@use(Carbon\Carbon)

<div>
    <x-slot name="header">
        <div class="flex items-center space-x-3 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
                {{ __('Credenciados') }}
            </h2>
        </div>
    </x-slot>

    <div
        class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Alertas de Mensagem --}}
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                    class="bg-teal-50 dark:bg-teal-900/50 border-l-4 border-teal-500 rounded-lg shadow-lg my-6 p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-teal-800 dark:text-teal-200">{{ session('message') }}</p>
                        <button @click="show = false" class="text-teal-600 dark:text-teal-400">&times;</button>
                    </div>
                </div>
            @endif

            {{-- Card Principal --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="w-full md:w-1/3">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar Credenciado..."
                            class="w-full px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Novo Credenciado
                    </button>
                </div>

                {{-- TABELA DESKTOP (Invisível em celulares) --}}
                <div class="hidden md:block overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                    <table class="w-full table-auto">
                        <thead
                            class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Nome</th>
                                <th class="py-3 px-6 text-left">Presídio(s)</th>
                                <th class="py-3 px-6 text-left">Igreja</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse ($results as $index => $credenciado)
                                <tr wire:key="cred-{{ $credenciado->id }}"
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all animate-slide-up"
                                    style="--delay: {{ (($index % 10) + 1) * 0.05 }}s;">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                        {{ $credenciado->nome }}</td>
                                    <td class="px-6 py-4">
                                        @foreach ($credenciado->credencialPresidios as $cp)
                                            <span
                                                class="block text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded mb-1 w-max">
                                                {{ $cp->presidio->nome ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $credenciado->igreja->nome ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{-- Botões de Ação --}}
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $credenciado->id }})"
                                                class="hover:text-green-500"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $credenciado->id }})"
                                                class="hover:text-blue-500"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                                    </path>
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $credenciado->id }})"
                                                class="hover:text-red-500"><svg class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                                    </path>
                                                </svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum registro.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- LISTA MOBILE (Visível apenas em celulares) --}}
                <div class="md:hidden space-y-4 mt-6">
                    @forelse ($results as $index => $credenciado)
                        <div wire:key="mobile-cred-{{ $credenciado->id }}"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ (($index % 10) + 1) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ $credenciado->nome }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Unidades:</strong>
                                        {{ $credenciado->credencialPresidios->map(fn($cp) => $cp->presidio->nome)->join(', ') ?: 'Nenhuma' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Igreja:
                                        {{ $credenciado->igreja->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $credenciado->id }})"
                                        class="text-gray-500 hover:text-green-500"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $credenciado->id }})"
                                        class="text-gray-500 hover:text-blue-500"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $credenciado->id }})"
                                        class="text-gray-500 hover:text-red-500"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12">
                                            </path>
                                        </svg></button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 text-center text-gray-500">
                            Nenhum registro encontrado.</div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $results->links() }}</div>
            </div>

            {{-- Modal Principal (Cadastro/Edição) --}}
            @if ($isOpen)
                <div x-data="{ open: @json($isOpen) }" x-show="open" x-on:keydown.escape.window="$wire.closeModal()"
                    x-transition
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center">
                    <div
                        class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-5xl p-6 md:p-8 mx-auto my-8 animate-fade-in">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            {{ $credenciado_id ? 'Editar Credenciado' : 'Novo Credenciado' }}</h3>

                        {{-- Erros --}}
                        @if ($errorMessage)
                            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                                {{ $errorMessage }}</div>
                        @endif

                        <form wire:submit.prevent="store" enctype="multipart/form-data" class="space-y-6">

                            {{-- Seção 1: Liderança e Grupos --}}
                            <section class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Liderança e Grupos</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Bloco</label>
                                        <select wire:model.live="bloco_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">Selecione</option>
                                            @foreach ($allBlocos as $bloco)
                                                <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Região</label>
                                        <select wire:model.live="regiao_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @if (empty($regiaos)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($regiaos as $regiao)
                                                <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Igreja</label>
                                        <select wire:model="igreja_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @if (empty($igrejas)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($igrejas as $igreja)
                                                <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Categoria</label>
                                        <select wire:model="categoria_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">Selecione</option>
                                            @foreach ($allCategorias as $categoria)
                                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Cargo</label>
                                        <select wire:model="cargo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">Selecione</option>
                                            @foreach ($allCargos as $cargo)
                                                <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Grupo</label>
                                        <select wire:model="grupo_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">Selecione</option>
                                            @foreach ($allGrupos as $grupo)
                                                <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </section>

                            {{-- Seção 2: Informações Pessoais --}}
                            <section class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Informações Pessoais</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2"><label class="block text-sm dark:text-gray-300">Nome
                                            Completo</label>
                                        <input type="text" wire:model="nome"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">Celular</label>
                                        <input type="text" wire:model="celular"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">Email</label>
                                        <input type="email" wire:model="email"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">Profissão</label>
                                        <input type="text" wire:model="profissao"
                                            class="mt-1 w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">Foto Perfil</label>
                                        <input type="file" wire:model="foto" class="text-xs mt-1">
                                        @if ($fotoAtual && !$foto)
                                            <img src="{{ asset($fotoAtual) }}" class="h-10 mt-1 rounded">
                                        @endif
                                    </div>
                                </div>
                            </section>

                            {{-- NOVO: Seção de Identidade (RG/CNH) --}}
                            <section
                                class="bg-yellow-50 dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-yellow-500">
                                <h4
                                    class="text-md font-semibold text-yellow-700 dark:text-yellow-400 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                    Documento de Identidade
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium dark:text-gray-300">Frente do
                                            Documento</label>
                                        <input type="file" wire:model="identidade_frente"
                                            class="mt-1 text-sm text-gray-500 file:bg-yellow-100 file:text-yellow-700 file:rounded-full file:border-0 file:px-4 file:py-1">
                                        @if ($idFrenteAtual && !$identidade_frente)
                                            <img src="{{ asset($idFrenteAtual) }}" class="mt-2 h-24 rounded shadow">
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium dark:text-gray-300">Verso do
                                            Documento</label>
                                        <input type="file" wire:model="identidade_verso"
                                            class="mt-1 text-sm text-gray-500 file:bg-yellow-100 file:text-yellow-700 file:rounded-full file:border-0 file:px-4 file:py-1">
                                        @if ($idVersoAtual && !$identidade_verso)
                                            <img src="{{ asset($idVersoAtual) }}" class="mt-2 h-24 rounded shadow">
                                        @endif
                                    </div>
                                </div>
                            </section>

                            {{-- Seção 3: Endereço e Localização --}}
                            <section class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Endereço</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2"><label
                                            class="block text-sm dark:text-gray-300">Endereço</label>
                                        <input type="text" wire:model="endereco"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">Bairro</label>
                                        <input type="text" wire:model="bairro"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Estado</label>
                                        <select wire:model.live="estado_id"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            <option value="">Selecione</option>
                                            @foreach ($allEstados as $estado)
                                                <option value="{{ $estado->id }}">{{ $estado->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-medium dark:text-gray-300">Cidade</label>
                                        <select wire:model="cidade_id"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white"
                                            @if (empty($cidades)) disabled @endif>
                                            <option value="">Selecione</option>
                                            @foreach ($cidades as $cidade)
                                                <option value="{{ $cidade->id }}">{{ $cidade->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div><label class="block text-sm dark:text-gray-300">CEP</label>
                                        <input type="text" wire:model="cep"
                                            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </section>

                            {{-- Seção 4: Outras Informações (Checkboxes idênticos ao Pessoas) --}}
                            <section class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                                <h4
                                    class="text-md font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-1 mb-4">
                                    Outras Informações</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium dark:text-gray-300">Trabalho</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="interno" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Interno</span></label>
                                        <label class="flex items-center"><input type="checkbox" wire:model="trabalho"
                                                value="externo" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Externo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium dark:text-gray-300">Batismo</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                                value="aguas" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Nas Águas</span></label>
                                        <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                                value="espirito" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Com Espírito
                                                Santo</span></label>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium dark:text-gray-300">Preso</p>
                                        <label class="flex items-center"><input type="checkbox" wire:model="preso"
                                                value="preso" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Já foi Preso(a)</span></label>
                                        <label class="flex items-center"><input type="checkbox" wire:model="preso"
                                                value="familiar" class="rounded text-indigo-600 shadow-sm"><span
                                                class="ml-2 text-sm dark:text-gray-400">Familiar Preso</span></label>
                                    </div>
                                </div>
                            </section>

                            {{-- NOVO: Seção de Credenciais Dinâmicas --}}
                            <section
                                class="bg-blue-50 dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-blue-600">
                                <div class="flex justify-between items-center mb-6">
                                    <h4
                                        class="text-md font-semibold text-blue-700 dark:text-blue-400 flex items-center gap-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Credenciais por Presídio (Máx. 10)
                                    </h4>
                                    <button type="button" wire:click="addCredencial"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-full transition-all shadow-md">
                                        + ADICIONAR NOVA CREDENCIAL
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    @foreach ($credenciais as $index => $cred)
                                        <div
                                            class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 relative shadow-sm hover:shadow-md transition-shadow">
                                            <button type="button" wire:click="removeCredencial({{ $index }})"
                                                class="absolute top-3 right-3 text-red-400 hover:text-red-600 transition-colors"
                                                title="Remover esta credencial">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div class="md:col-span-1">
                                                    <label
                                                        class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Unidade
                                                        Prisional</label>
                                                    <select wire:model="credenciais.{{ $index }}.presidio_id"
                                                        class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white focus:ring-blue-500 shadow-sm">
                                                        <option value="">Selecione o presídio...</option>
                                                        @foreach ($allPresidios as $presidio)
                                                            <option value="{{ $presidio->id }}">{{ $presidio->nome }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Foto
                                                        Frente</label>
                                                    <input type="file"
                                                        wire:model="credenciais.{{ $index }}.foto_frente"
                                                        class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700">
                                                    @if ($cred['foto_frente_atual'])
                                                        <img src="{{ asset($cred['foto_frente_atual']) }}"
                                                            class="h-16 mt-2 rounded shadow-sm border">
                                                    @endif
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Foto
                                                        Verso</label>
                                                    <input type="file"
                                                        wire:model="credenciais.{{ $index }}.foto_verso"
                                                        class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700">
                                                    @if ($cred['foto_verso_atual'])
                                                        <img src="{{ asset($cred['foto_verso_atual']) }}"
                                                            class="h-16 mt-2 rounded shadow-sm border">
                                                    @endif
                                                </div>
                                            </div>
                                            <div wire:loading
                                                wire:target="credenciais.{{ $index }}.foto_frente, credenciais.{{ $index }}.foto_verso"
                                                class="text-[10px] text-blue-500 mt-2 font-bold italic animate-pulse">
                                                Carregando imagem...</div>
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($credenciais) === 0)
                                    <div class="text-center py-6 text-gray-400 italic text-sm">Nenhuma credencial
                                        adicionada até o momento.</div>
                                @endif
                            </section>

                            {{-- Botões de Ação do Form --}}
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" wire:click="closeModal"
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-bold py-2 px-6 rounded-lg transition-all shadow-sm">
                                    CANCELAR
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg transition-all shadow-md focus:outline-none flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $credenciado_id ? 'ATUALIZAR DADOS' : 'FINALIZAR CADASTRO' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if ($isViewOpen && $selectedCredenciado)
                <div wire:key="view-modal-cred" x-data="{ open: @json($isViewOpen) }" x-show="open"
                    x-on:keydown.escape.window="$wire.closeViewModal()" x-transition
                    @click.self="$wire.closeViewModal()"
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-center justify-center">

                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl p-6 md:p-8 mx-auto my-8 relative border border-gray-200 dark:border-gray-700">

                        {{-- Botão Fechar --}}
                        <button wire:click="closeViewModal"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="flex flex-col md:flex-row gap-8">
                            {{-- Coluna Esquerda: Foto e Identidade --}}
                            <div class="md:w-1/3 flex flex-col items-center">
                                <div class="relative group">
                                    <img src="{{ $selectedCredenciado->foto ? asset($selectedCredenciado->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($selectedCredenciado->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                        alt="Foto de {{ $selectedCredenciado->nome }}"
                                        class="h-48 w-48 rounded-2xl object-cover shadow-2xl border-4 border-white dark:border-gray-800 mb-6">
                                </div>

                                <div class="w-full space-y-4">
                                    <h4
                                        class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-1">
                                        Documentos de Identidade</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-500 mb-1">FRENTE</p>
                                            @if ($selectedCredenciado->identidade_frente)
                                                <a href="{{ asset($selectedCredenciado->identidade_frente) }}"
                                                    target="_blank" class="block hover:opacity-75 transition">
                                                    <img src="{{ asset($selectedCredenciado->identidade_frente) }}"
                                                        class="h-20 w-full object-cover rounded-lg border shadow-sm">
                                                </a>
                                            @else
                                                <div
                                                    class="h-20 w-full bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">
                                                    N/A</div>
                                            @endif
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-500 mb-1">VERSO</p>
                                            @if ($selectedCredenciado->identidade_verso)
                                                <a href="{{ asset($selectedCredenciado->identidade_verso) }}"
                                                    target="_blank" class="block hover:opacity-75 transition">
                                                    <img src="{{ asset($selectedCredenciado->identidade_verso) }}"
                                                        class="h-20 w-full object-cover rounded-lg border shadow-sm">
                                                </a>
                                            @else
                                                <div
                                                    class="h-20 w-full bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 text-[10px]">
                                                    N/A</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Coluna Direita: Informações e Credenciais --}}
                            <div class="md:w-2/3 space-y-6">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $selectedCredenciado->nome }}</h2>
                                    <p class="text-blue-600 dark:text-blue-400 font-medium">
                                        {{ $selectedCredenciado->cargo->nome ?? 'Membro' }} •
                                        {{ $selectedCredenciado->igreja->nome ?? 'Sem Igreja' }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                        <p class="text-gray-500 text-xs">WhatsApp / Celular</p>
                                        <p class="font-semibold dark:text-gray-200">
                                            {{ $selectedCredenciado->celular }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                                        <p class="text-gray-500 text-xs">E-mail</p>
                                        <p class="font-semibold dark:text-gray-200 truncate">
                                            {{ $selectedCredenciado->email ?? 'Não informado' }}</p>
                                    </div>
                                </div>

                                {{-- Seção de Endereço --}}
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-1 mb-2">
                                        Localização</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $selectedCredenciado->endereco }}, {{ $selectedCredenciado->bairro }}<br>
                                        {{ optional($selectedCredenciado->cidade)->nome }} -
                                        {{ optional(optional($selectedCredenciado->cidade)->estado)->uf }}
                                    </p>
                                </div>

                                {{-- Seção de Credenciais de Presídios --}}
                                <div>
                                    <h4
                                        class="text-xs font-bold text-blue-500 uppercase tracking-widest border-b border-blue-200 pb-1 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Credenciais UNP ({{ $selectedCredenciado->credencialPresidios->count() }})
                                    </h4>

                                    <div class="space-y-2 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                        @forelse($selectedCredenciado->credencialPresidios as $cp)
                                            <div
                                                class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                                                <div class="flex items-center gap-3">
                                                    <div class="bg-blue-600 text-white p-2 rounded-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                            {{ $cp->presidio->nome }}</p>
                                                        <div class="flex gap-3 mt-1">
                                                            @if ($cp->foto_frente)
                                                                <a href="{{ asset($cp->foto_frente) }}"
                                                                    target="_blank"
                                                                    class="text-[10px] text-blue-600 hover:underline">Ver
                                                                    Frente</a>
                                                            @endif
                                                            @if ($cp->foto_verso)
                                                                <a href="{{ asset($cp->foto_verso) }}"
                                                                    target="_blank"
                                                                    class="text-[10px] text-blue-600 hover:underline">Ver
                                                                    Verso</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-[10px] font-bold text-blue-500 bg-white dark:bg-gray-800 px-2 py-1 rounded-full shadow-sm">ATIVO</span>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500 italic">Nenhuma credencial de presídio
                                                registrada.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Testemunho Rápido --}}
                                @if ($selectedCredenciado->testemunho)
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/80 p-4 rounded-xl italic text-sm text-gray-600 dark:text-gray-400 border-l-4 border-gray-300">
                                        "{{ Str::limit($selectedCredenciado->testemunho, 200) }}"
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal de Exclusão --}}
            @if ($confirmDeleteId)
                <div
                    class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto animate-fade-in">
                        <div class="flex items-center gap-3 mb-4 text-red-600">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold">Excluir Registro?</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Esta ação removerá permanentemente o
                            credenciado e todas as suas credenciais vinculadas.</p>
                        <div class="flex justify-end gap-3">
                            <button wire:click="$set('confirmDeleteId', null)"
                                class="bg-gray-200 dark:bg-gray-700 px-5 py-2 rounded-lg font-bold">CANCELAR</button>
                            <button wire:click="delete"
                                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-bold">EXCLUIR
                                AGORA</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Estilos de Animação (Copiados do Pessoas) --}}
    <style>
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        .animate-slide-up {
            opacity: 0;
            animation: slideUp 0.4s ease-out forwards;
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
                transform: translateY(15px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</div>
