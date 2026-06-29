@section('title', 'Carros UNP')

<x-slot name="header">
    <div class="flex items-center space-x-3 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="capitalize font-semibold text-2xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('Carros UNP') }}
        </h2>
    </div>
</x-slot>

<div>
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
                            placeholder="Buscar por modelo ou placa..."
                            class="w-full px-4 py-2 rounded-md border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    <button wire:click="create"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Carro
                    </button>
                </div>

                @if ($isOpen)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 flex items-start justify-center pt-10">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                                {{ $carro_id ? 'Editar' : 'Cadastrar' }} Carro</h2>
                            <form wire:submit.prevent="store" class="space-y-6">
                                <section>
                                    <h3
                                        class="text-lg font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                        Dados do Veículo</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="pastor_unp_id"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pastor
                                                Responsável</label>
                                            <select id="pastor_unp_id" wire:model.defer="pastor_unp_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                required>
                                                <option value="">Selecione...</option>
                                                @foreach ($pastorOptions as $pastor)
                                                    <option value="{{ $pastor->id }}">{{ $pastor->nome }}</option>
                                                @endforeach
                                            </select>
                                            @error('pastor_unp_id')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="bloco_id"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label>
                                            <select id="bloco_id" wire:model.defer="bloco_id"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                required>
                                                <option value="">Selecione...</option>
                                                @foreach ($blocoOptions as $bloco)
                                                    <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                                @endforeach
                                            </select>
                                            @error('bloco_id')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-3"><label for="modelo"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modelo
                                                do Veículo</label><input type="text" id="modelo"
                                                wire:model.defer="modelo"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                required>
                                            @error('modelo')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="ano"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ano</label>
                                            <select id="ano" wire:model.defer="ano"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                required>
                                                <option value="">Selecione...</option>
                                                @foreach ($yearOptions as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                            @error('ano')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div><label for="placa"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Placa</label><input
                                                type="text" id="placa" wire:model.defer="placa"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"
                                                required>
                                            @error('placa')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div><label for="km"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">KM</label><input
                                                type="number" id="km" wire:model.defer="km"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800">
                                            @error('km')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-3"><label for="demanda"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Demanda</label>
                                            <textarea id="demanda" wire:model.defer="demanda" rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-800"></textarea>
                                        </div>
                                    </div>
                                </section>
                                <section>
                                    <h3
                                        class="text-lg font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                        Fotos do Veículo</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        {{-- Trecho Corrigido para a Foto da Frente --}}
                                        <div>
                                            <label for="foto_frente"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frente*</label>
                                            <input type="file" id="foto_frente" wire:model="foto_frente"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">

                                            @if ($foto_frente && is_object($foto_frente))
                                                <img src="{{ $foto_frente->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_frente_atual)
                                                <img src="{{ asset($foto_frente_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @endif

                                            @error('foto_frente')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        {{-- Foto Trás --}}
                                        <div>
                                            <label for="foto_tras"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Traseira*</label>
                                            <input type="file" id="foto_tras" wire:model="foto_tras"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                            @if ($foto_tras)
                                                <img src="{{ $foto_tras->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_tras_atual)
                                                <img src="{{ asset($foto_tras_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @endif
                                            @error('foto_tras')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        {{-- Foto Direita --}}
                                        <div>
                                            <label for="foto_direita"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lateral
                                                Direita</label>
                                            <input type="file" id="foto_direita" wire:model="foto_direita"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                            @if ($foto_direita)
                                                <img src="{{ $foto_direita->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_direita_atual)
                                                <img src="{{ asset($foto_direita_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @endif
                                        </div>
                                        {{-- Foto Esquerda --}}
                                        <div>
                                            <label for="foto_esquerda"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lateral
                                                Esquerda</label>
                                            <input type="file" id="foto_esquerda" wire:model="foto_esquerda"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                            @if ($foto_esquerda)
                                                <img src="{{ $foto_esquerda->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_esquerda_atual)
                                                <img src="{{ asset($foto_esquerda_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @endif
                                        </div>
                                        {{-- Foto Dentro --}}
                                        <div>
                                            <label for="foto_dentro"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interior</label>
                                            <input type="file" id="foto_dentro" wire:model="foto_dentro"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                            @if ($foto_dentro)
                                                <img src="{{ $foto_dentro->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_dentro_atual)
                                                <img src="{{ asset($foto_dentro_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @endif
                                        </div>
                                        {{-- Foto Câmbio --}}
                                        <div>
                                            <label for="foto_cambio"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Câmbio</label>
                                            <input type="file" id="foto_cambio" wire:model="foto_cambio"
                                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                            @if ($foto_cambio)
                                                <img src="{{ $foto_cambio->temporaryUrl() }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                                            @elseif ($foto_cambio_atual)
                                                <img src="{{ asset($foto_cambio_atual) }}"
                                                    class="mt-2 h-24 w-24 object-cover rounded-md">
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

                @if ($isViewOpen && $selectedCarro)
                    <div class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 overflow-y-auto p-4 sm:p-10 flex flex-col items-center justify-start pt-10">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-4xl p-6">
                            <div class="flex justify-between items-start pb-4 border-b dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                    {{ $selectedCarro->modelo }} - {{ $selectedCarro->placa }}</h2>
                                <button wire:click="closeViewModal"
                                    class="p-2 -m-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">&times;</button>
                            </div>
                            <div class="mt-6 space-y-6">
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700 dark:text-gray-300">
                                    <div>
                                        <h4 class="font-semibold text-blue-500">Detalhes do Veículo</h4>
                                        <div class="border-t my-1 dark:border-gray-700"></div>
                                        <p><strong>Modelo:</strong> {{ $selectedCarro->modelo }}</p>
                                        <p><strong>Ano:</strong> {{ $selectedCarro->ano }}</p>
                                        <p><strong>Placa:</strong> {{ $selectedCarro->placa }}</p>
                                        <p><strong>KM:</strong> {{ number_format($selectedCarro->km, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-blue-500">Responsáveis</h4>
                                        <div class="border-t my-1 dark:border-gray-700"></div>
                                        <p><strong>Pastor:</strong> {{ $selectedCarro->pastorUnp->nome ?? 'N/A' }}</p>
                                        <p><strong>Bloco:</strong> {{ $selectedCarro->bloco->nome ?? 'N/A' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="font-semibold text-blue-500">Demanda</h4>
                                        <div class="border-t my-1 dark:border-gray-700"></div>
                                        <p class="whitespace-pre-wrap">
                                            {{ $selectedCarro->demanda ?? 'Nenhuma demanda registrada.' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-500">Fotos</h4>
                                    <div class="border-t my-1 dark:border-gray-700"></div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-2">
                                        @if ($selectedCarro->foto_frente)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_frente) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Frente</p>
                                            </div>
                                        @endif
                                        @if ($selectedCarro->foto_tras)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_tras) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Traseira</p>
                                            </div>
                                        @endif
                                        @if ($selectedCarro->foto_direita)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_direita) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Direita</p>
                                            </div>
                                        @endif
                                        @if ($selectedCarro->foto_esquerda)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_esquerda) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Esquerda</p>
                                            </div>
                                        @endif
                                        @if ($selectedCarro->foto_dentro)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_dentro) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Interior</p>
                                            </div>
                                        @endif
                                        @if ($selectedCarro->foto_cambio)
                                            <div class="text-center"><img
                                                    src="{{ asset($selectedCarro->foto_cambio) }}"
                                                    class="w-full h-32 object-cover rounded-md">
                                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Câmbio</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($confirmDeleteId)
                    <div
                        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-md z-50 flex items-center justify-center p-4">
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg shadow-2xl w-full max-w-md p-6 mx-auto">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmar Exclusão</h3>
                            <p class="my-4 text-gray-600 dark:text-gray-300">Tem certeza que deseja apagar este
                                registro?</p>
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
                                <th class="py-3 px-6 text-left">Modelo</th>
                                <th class="py-3 px-6 text-left">Placa</th>
                                <th class="py-3 px-6 text-left">Pastor Responsável</th>
                                <th class="py-3 px-6 text-left">Bloco</th>
                                <th class="py-3 px-6 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm">
                            @forelse($results as $index => $carro)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 animate-slide-up"
                                    style="--delay: {{ ($index % 10) * 0.05 }}s;">
                                    <td class="py-3 px-6 font-semibold">{{ $carro->modelo }}</td>
                                    <td class="py-3 px-6">{{ $carro->placa }}</td>
                                    <td class="py-3 px-6">{{ $carro->pastorUnp->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6">{{ $carro->bloco->nome ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <button wire:click="view({{ $carro->id }})"
                                                class="w-5 transform hover:text-green-500"><svg fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="edit({{ $carro->id }})"
                                                class="w-5 transform hover:text-blue-500"><svg fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg></button>
                                            <button wire:click="confirmDelete({{ $carro->id }})"
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
                                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">Nenhum registro
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden space-y-4">
                    @forelse($results as $index => $carro)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 animate-slide-up"
                            style="--delay: {{ ($index % 10) * 0.05 }}s;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 space-y-1">
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $carro->modelo }}
                                        ({{ $carro->placa }})
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Pastor:
                                        {{ $carro->pastorUnp->nome ?? 'N/A' }}</p>
                                </div>
                                <div class="flex flex-col space-y-4 ml-4">
                                    <button wire:click="view({{ $carro->id }})" class="w-5 text-gray-500"><svg
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg></button>
                                    <button wire:click="edit({{ $carro->id }})" class="w-5 text-gray-500"><svg
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg></button>
                                    <button wire:click="confirmDelete({{ $carro->id }})"
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
