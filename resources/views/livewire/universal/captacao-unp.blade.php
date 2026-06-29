@section('title', 'Captação de Dados UNP')

<div class="bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        <div class="mt-6">
            <a href="/" class="flex items-center space-x-2 group">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="40" viewBox="0 0 440 376"
                    class="transition-transform duration-300 group-hover:scale-110">
                    <path
                        d="M56.53 6.41h152.93v366.88L70.37 178.11h62.28l33.07 45.89V69.06H74.71l-9.85 13.81L87.53 115.2l-62.43 0.5L2 84z"
                        fill="#2563EB" class="dark:fill-blue-400" />
                    <path
                        d="M229.93 6.29h152.83l54.2 76.26-72.48 101.38-0.18-87.61 9.85-13.31-9.67-13.81-23.22-0.25-0.35 148.54-43.63 61.17-1.76-206.77H271.3l1.05 239.85-45.03 61.17z"
                        fill="#2563EB" class="dark:fill-blue-400" />
                </svg>
            </a>
        </div>

        <div
            class="w-full sm:max-w-4xl mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-lg">

            @if (session()->has('submission_error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Ocorreu um Erro</p>
                    <p>{{ session('submission_error') }}</p>
                </div>
            @endif

            @if ($step !== $totalSteps)
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Passo {{ $step }} de
                            {{ $possuiCarro === 'nao' ? $totalSteps - 2 : $totalSteps - 1 }}</span>
                        <span
                            class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ round(($step / ($possuiCarro === 'nao' ? $totalSteps - 2 : $totalSteps - 1)) * 100) }}%
                            Completo</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                            style="width: {{ ($step / ($possuiCarro === 'nao' ? $totalSteps - 2 : $totalSteps - 1)) * 100 }}%">
                        </div>
                    </div>
                </div>
            @endif

            {{-- Conteúdo dos Passos --}}
            <div class="min-h-[450px]">
                {{-- Passo 1: Identificação --}}
                @if ($step === 1)
                    <div wire:key="step-1" class="animate-fade-in">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 1: Sua
                            Identificação</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Por favor, comece preenchendo seu nome
                            completo e CPF.</p>
                        <div class="space-y-4">
                            <div>
                                <label for="nome"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome
                                    Completo</label>
                                <input type="text" id="nome" wire:model.lazy="state.nome"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                    required>
                                @error('state.nome')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="cpf"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF</label>
                                <input type="text" id="cpf" wire:model.lazy="state.cpf" x-mask="999.999.999-99"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                    required>
                                @error('state.cpf')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Passo 2: Dados do Pastor --}}
                @if ($step === 2)
                    <div wire:key="step-2" class="animate-fade-in">
                        <section>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 2: Dados do
                                Pastor</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="bloco_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label>
                                    <select id="bloco_id" wire:model.live="state.bloco_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                        required>
                                        <option value="">Selecione</option>
                                        @foreach ($blocoOptions as $bloco)
                                            <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('state.bloco_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="regiao_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label>
                                    <select id="regiao_id" wire:model.defer="state.regiao_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                        @disabled(empty($regiaoOptions))>
                                        <option value="">Selecione um bloco</option>
                                        @foreach ($regiaoOptions as $regiao)
                                            <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>
                                        @endforeach
                                    </select>
                                    @error('state.regiao_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cargo"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
                                    <select id="cargo" wire:model.defer="state.cargo"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                        required>
                                        <option value="">Selecione</option>
                                        <option value="responsavel">Responsável</option>
                                        <option value="auxiliar">Auxiliar</option>
                                    </select>
                                    @error('state.cargo')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="md:col-span-3">
                                    <label for="nascimento"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                        Nascimento</label>
                                    <input type="date" id="nascimento" wire:model.defer="state.nascimento"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" id="email" wire:model.defer="state.email"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="whatsapp"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Whatsapp</label>
                                    <input type="text" id="whatsapp" wire:model.defer="state.whatsapp"
                                        x-mask="(99) 99999-9999"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"
                                        required>
                                    @error('state.whatsapp')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="telefone"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                                    <input type="text" id="telefone" wire:model.defer="state.telefone"
                                        x-mask="(99) 9999-9999"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="chegada"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempo no
                                        estado/bloco</label>
                                    <input type="text" id="chegada" wire:model.defer="state.chegada"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="entrada"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de
                                        entrada no grupo</label>
                                    <input type="date" id="entrada" wire:model.defer="state.entrada"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div class="flex items-center space-x-4">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Já foi
                                        preso?</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.preso" value="1" class="mr-2"> Sim</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.preso" value="0" class="mr-2"> Não</label>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dias de
                                        trabalho no presídio</label>
                                    <div class="mt-2 grid grid-cols-4 md:grid-cols-7 gap-2">
                                        @foreach (['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB', 'DOM'] as $dia)
                                            <label class="flex items-center space-x-2 text-sm"><input type="checkbox"
                                                    wire:model.defer="state.trabalho"
                                                    value="{{ $dia }}"><span>{{ $dia }}</span></label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label for="foto"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sua Foto
                                        (3x4)</label>
                                    <input type="file" id="foto" wire:model="foto"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                    @if ($foto)
                                        <img src="{{ $foto->temporaryUrl() }}"
                                            class="mt-2 h-24 w-24 object-cover rounded-md">
                                    @endif
                                    @error('foto')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </section>
                    </div>
                @endif

                {{-- Passo 3: Dados da Esposa --}}
                @if ($step === 3)
                    <div wire:key="step-3" class="animate-fade-in">
                        <section>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 3: Dados da
                                Esposa</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2"><label for="nome_esposa"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label><input
                                        type="text" id="nome_esposa" wire:model.defer="state.nome_esposa"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div><label for="obra"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempo de
                                        obra</label><input type="text" id="obra"
                                        wire:model.defer="state.obra"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div><label for="email_esposa"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label><input
                                        type="email" id="email_esposa" wire:model.defer="state.email_esposa"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div><label for="whatsapp_esposa"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Whatsapp</label><input
                                        type="text" id="whatsapp_esposa" wire:model.defer="state.whatsapp_esposa"
                                        x-mask="(99) 99999-9999"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div><label for="telefone_esposa"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label><input
                                        type="text" id="telefone_esposa" wire:model.defer="state.telefone_esposa"
                                        x-mask="(99) 9999-9999"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div><label for="casado"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tempo de
                                        casado</label><input type="text" id="casado"
                                        wire:model.defer="state.casado"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                </div>
                                <div class="flex items-center space-x-4">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Missionária
                                        consagrada?</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.consagrada_esposa" value="1" class="mr-2">
                                        Sim</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.consagrada_esposa" value="0" class="mr-2">
                                        Não</label>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Já foi
                                        presa?</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.preso_esposa" value="1" class="mr-2">
                                        Sim</label>
                                    <label class="flex items-center"><input type="radio"
                                            wire:model.defer="state.preso_esposa" value="0" class="mr-2">
                                        Não</label>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dias de
                                        trabalho no presídio</label>
                                    <div class="mt-2 grid grid-cols-4 md:grid-cols-7 gap-2">
                                        @foreach (['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB', 'DOM'] as $dia)
                                            <label class="flex items-center space-x-2 text-sm"><input type="checkbox"
                                                    wire:model.defer="state.trabalho_esposa"
                                                    value="{{ $dia }}"><span>{{ $dia }}</span></label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label for="foto_esposa"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto da
                                        Esposa (3x4)</label>
                                    <input type="file" id="foto_esposa" wire:model="foto_esposa"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100">
                                    @if ($foto_esposa)
                                        <img src="{{ $foto_esposa->temporaryUrl() }}"
                                            class="mt-2 h-24 w-24 object-cover rounded-md">
                                    @endif
                                    @error('foto_esposa')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </section>
                    </div>
                @endif

                {{-- Passo 4: Pergunta sobre o Carro --}}
                @if ($step === 4)
                    <div wire:key="step-4" class="animate-fade-in">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 4: Veículo</h3>
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Possui veículo a
                                serviço do grupo?</label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center"><input type="radio" wire:model.live="possuiCarro"
                                        value="sim" class="mr-2"> Sim</label>
                                <label class="flex items-center"><input type="radio" wire:model.live="possuiCarro"
                                        value="nao" class="mr-2"> Não</label>
                            </div>
                            @error('possuiCarro')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Passo 5: Dados do Veículo (Condicional) --}}
                @if ($step === 5)
                    <div wire:key="step-5" class="animate-fade-in">
                        <section>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 5: Dados do
                                Veículo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-3"><label for="modelo"
                                        class="block text-sm font-medium">Modelo</label><input type="text"
                                        wire:model.defer="state.modelo"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                    @error('state.modelo')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div><label for="ano" class="block text-sm font-medium">Ano</label><input
                                        type="text" wire:model.defer="state.ano"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                    @error('state.ano')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div><label for="placa" class="block text-sm font-medium">Placa</label><input
                                        type="text" wire:model.defer="state.placa"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700">
                                    @error('state.placa')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div><label for="km" class="block text-sm font-medium">KM</label><input
                                        type="number" wire:model.defer="state.km"
                                        class="mt-1 block w-full rounded-md dark:bg-gray-700"></div>
                                <div class="md:col-span-3"><label for="demanda"
                                        class="block text-sm font-medium">Demanda</label>
                                    <textarea wire:model.defer="state.demanda" rows="3" class="mt-1 block w-full rounded-md dark:bg-gray-700"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                                <div><label class="block text-sm font-medium">Frente*</label><input type="file"
                                        wire:model="foto_frente" class="text-xs">
                                    @if ($foto_frente)
                                        <img src="{{ $foto_frente->temporaryUrl() }}"
                                            class="mt-2 h-24 w-24 object-cover rounded-md">
                                    @endif @error('foto_frente')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div><label class="block text-sm font-medium">Traseira*</label><input type="file"
                                    wire:model="foto_tras" class="text-xs">
                                @if ($foto_tras)
                                    <img src="{{ $foto_tras->temporaryUrl() }}"
                                        class="mt-2 h-24 w-24 object-cover rounded-md">
                                @endif @error('foto_tras')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div><label class="block text-sm font-medium">Direita</label><input type="file"
                                wire:model="foto_direita" class="text-xs">
                            @if ($foto_direita)
                                <img src="{{ $foto_direita->temporaryUrl() }}"
                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                            @endif
                        </div>
                        <div><label class="block text-sm font-medium">Esquerda</label><input type="file"
                                wire:model="foto_esquerda" class="text-xs">
                            @if ($foto_esquerda)
                                <img src="{{ $foto_esquerda->temporaryUrl() }}"
                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                            @endif
                        </div>
                        <div><label class="block text-sm font-medium">Interior</label><input type="file"
                                wire:model="foto_dentro" class="text-xs">
                            @if ($foto_dentro)
                                <img src="{{ $foto_dentro->temporaryUrl() }}"
                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                            @endif
                        </div>
                        <div><label class="block text-sm font-medium">Câmbio</label><input type="file"
                                wire:model="foto_cambio" class="text-xs">
                            @if ($foto_cambio)
                                <img src="{{ $foto_cambio->temporaryUrl() }}"
                                    class="mt-2 h-24 w-24 object-cover rounded-md">
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        @endif

        {{-- Passo 6: Revisão --}}
        {{-- Passo 6: Revisão --}}
        @if ($step === 6)
            <div wire:key="step-6" class="animate-fade-in">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Passo 6: Revise seus
                    Dados</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Por favor, confirme se todas as
                    informações estão corretas antes de enviar.</p>

                {{-- Resumo dos Dados --}}
                <div
                    class="space-y-4 text-sm text-gray-800 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <p><strong>Nome:</strong> {{ $state['nome'] ?? 'N/A' }}</p>
                    <p><strong>CPF:</strong> {{ $state['cpf'] ?? 'N/A' }}</p>
                    <p><strong>Cargo:</strong> <span class="capitalize">{{ $state['cargo'] ?? 'N/A' }}</span>
                    </p>
                    <p><strong>Whatsapp:</strong> {{ $state['whatsapp'] ?? 'N/A' }}</p>
                    @if ($state['nome_esposa'])
                        <p><strong>Esposa:</strong> {{ $state['nome_esposa'] }}</p>
                    @endif
                    @if ($possuiCarro === 'sim')
                        <p><strong>Veículo:</strong> {{ $state['modelo'] }} - {{ $state['placa'] }}</p>
                    @endif
                    <p class="pt-4 text-xs text-gray-500">Uma cópia detalhada de todos os dados será enviada
                        para aprovação.</p>
                </div>

                {{-- Pré-visualização das Fotos Enviadas --}}
                <div class="mt-6 pt-4 border-t dark:border-gray-700">
                    <h4 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Fotos Enviadas:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @if ($foto)
                            <div class="text-center">
                                <img src="{{ $foto->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Foto do Pastor</p>
                            </div>
                        @endif
                        @if ($foto_esposa)
                            <div class="text-center">
                                <img src="{{ $foto_esposa->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Foto da Esposa</p>
                            </div>
                        @endif
                        @if ($foto_frente)
                            <div class="text-center">
                                <img src="{{ $foto_frente->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Frente do Veículo</p>
                            </div>
                        @endif
                        @if ($foto_tras)
                            <div class="text-center">
                                <img src="{{ $foto_tras->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Traseira do Veículo
                                </p>
                            </div>
                        @endif
                        @if ($foto_direita)
                            <div class="text-center">
                                <img src="{{ $foto_direita->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Lateral Direita</p>
                            </div>
                        @endif
                        @if ($foto_esquerda)
                            <div class="text-center">
                                <img src="{{ $foto_esquerda->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Lateral Esquerda</p>
                            </div>
                        @endif
                        @if ($foto_dentro)
                            <div class="text-center">
                                <img src="{{ $foto_dentro->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Interior</p>
                            </div>
                        @endif
                        @if ($foto_cambio)
                            <div class="text-center">
                                <img src="{{ $foto_cambio->temporaryUrl() }}"
                                    class="w-full h-32 object-cover rounded-md border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Câmbio</e m>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Passo 7: Agradecimento --}}
        @if ($step === $totalSteps)
            <div wire:key="step-7"
                class="text-center flex flex-col items-center justify-center h-full animate-fade-in">
                <svg class="w-16 h-16 text-green-500 mb-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Obrigado!</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Seu cadastro foi enviado com sucesso e está
                    pendente de aprovação.</p>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Entraremos em contato em breve.</p>
            </div>
        @endif
    </div>

    {{-- Botões de Navegação --}}
    @if ($step < $totalSteps)
        <div class="flex justify-between items-center mt-8 pt-6 border-t dark:border-gray-700">
            <div>
                @if ($step > 1)
                    <button wire:click="previousStep"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-semibold py-2 px-4 rounded-lg">&larr;
                        Voltar</button>
                @endif
            </div>
            <div>
                @if ($step < 6)
                    <button wire:click="nextStep" wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg">Próximo
                        Passo &rarr;</button>
                @elseif ($step === 6)
                    <button wire:click="submit" wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-lg">
                        <span wire:loading.remove>Enviar Cadastro</span>
                        <span wire:loading>Enviando...</span>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
</div>
<style>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
</div>
