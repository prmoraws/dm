@section('title', 'Carros UNP - Edição')

<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8" x-data>
    <div class="max-w-3xl mx-auto">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Atualização de Veículo UNP</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Mantenha os dados do seu carro de guerra sempre
                atualizados.</p>
        </div>

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow">
                {{ session('message') }}
            </div>
        @endif

        <div
            class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-6 md:p-10 border dark:border-gray-700">

            {{-- PASSO 1: VERIFICAÇÃO --}}
            @if ($step == 1)
                <form wire:submit.prevent="verificarIdentidade" class="space-y-6 animate-fade-in">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Selecione seu
                            Nome</label>
                        <select wire:model="pastor_unp_id"
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                            <option value="">-- Selecione na Lista --</option>
                            @foreach ($pastorOptions as $pastor)
                                <option value="{{ $pastor->id }}">{{ $pastor->nome }}</option>
                            @endforeach
                        </select>
                        @error('pastor_unp_id')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Sua Data de
                            Nascimento</label>
                        <input type="date" wire:model="nascimento"
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                        @error('nascimento')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg">
                        VALIDAR E EDITAR VEÍCULO
                    </button>
                </form>
            @endif

            {{-- PASSO 2: FORMULÁRIO DE EDIÇÃO --}}
            @if ($step == 2)
                <form wire:submit.prevent="update" class="space-y-8 animate-fade-in">
                    <div class="flex justify-between items-center border-b dark:border-gray-700 pb-4">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400">Dados Atuais do Veículo</h3>
                        <button type="button" wire:click="$set('step', 1)"
                            class="text-xs text-gray-500 hover:text-red-500 font-bold">Sair / Trocar Pastor</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium dark:text-gray-300">Modelo</label>
                            <input type="text" wire:model="modelo"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Ano</label>
                            <select wire:model="ano"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                                <option value="">Selecione...</option>
                                @foreach ($yearOptions as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Placa</label>
                            <input type="text" wire:model="placa"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">KM Atual</label>
                            <input type="number" wire:model="km"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Bloco</label>
                            <select wire:model="bloco_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white">
                                @foreach ($blocoOptions as $bloco)
                                    <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium dark:text-gray-300">Demanda / Observação</label>
                            <textarea wire:model="demanda" rows="3"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    {{-- SEÇÃO DE FOTOS --}}
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border dark:border-gray-700">
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Atualizar Fotos (Opcional)
                        </h4>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            @php
                                $camposFotos = [
                                    'frente' => 'Frente*',
                                    'tras' => 'Traseira*',
                                    'direita' => 'Lat. Direita',
                                    'esquerda' => 'Lat. Esquerda',
                                    'dentro' => 'Interior',
                                    'cambio' => 'Câmbio',
                                ];
                            @endphp

                            @foreach ($camposFotos as $slug => $label)
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-bold uppercase text-gray-500">{{ $label }}</label>
                                    <div class="relative group">
                                        @if ($this->{'foto_' . $slug})
                                            <img src="{{ $this->{'foto_' . $slug}->temporaryUrl() }}"
                                                class="h-24 w-full object-cover rounded-lg border-2 border-blue-500 shadow-md">
                                        @elseif($this->{'foto_' . $slug . '_atual'})
                                            <img src="{{ asset($this->{'foto_' . $slug . '_atual'}) }}"
                                                class="h-24 w-full object-cover rounded-lg border dark:border-gray-700">
                                        @else
                                            <div
                                                class="h-24 w-full bg-gray-200 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400">
                                                Sem foto</div>
                                        @endif
                                        <input type="file" wire:model="foto_{{ $slug }}"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                            <span wire:loading.remove>SALVAR ATUALIZAÇÕES</span>
                            <span wire:loading>PROCESSANDO...</span>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>
