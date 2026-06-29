@section('title', 'Cadastro UNP')

<div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-200 mb-4">Cadastro UNP</h2>
            <div class="relative pt-1">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200 dark:bg-blue-900">
                    <div style="width:{{ ($step - 1) / ($totalSteps - 1) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8">
            @if(session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Etapas 1 a 6 (sem alterações) --}}
            @if($step < 8)
                @if($step === 1)
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Seja Bem-vindo(a)!</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Estamos felizes com seu interesse em fazer parte. Este formulário levará apenas alguns minutos.</p>
                    <button wire:click="nextStep" type="button" class="mt-6 w-full inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Iniciar Cadastro</button>
                </div>
                @endif
                @if($step === 2)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">1. Sua Igreja e Função</h3>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 border rounded-md dark:border-gray-700">
                            <div>
                                <label for="bloco_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bloco</label>
                                <select wire:model.live="bloco_id" id="bloco_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                    <option value="">Selecione</option>
                                    @foreach($allBlocos as $bloco) <option value="{{ $bloco->id }}">{{ $bloco->nome }}</option> @endforeach
                                </select>
                                @error('bloco_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="regiao_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Região</label>
                                <select wire:model.live="regiao_id" id="regiao_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700" @if(empty($regiaos)) disabled @endif>
                                    <option value="">Selecione</option>
                                    @foreach($regiaos as $regiao) <option value="{{ $regiao->id }}">{{ $regiao->nome }}</option> @endforeach
                                </select>
                                @error('regiao_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="igreja_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Igreja</label>
                                <select wire:model.lazy="igreja_id" id="igreja_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700" @if(empty($igrejas)) disabled @endif>
                                    <option value="">Selecione</option>
                                    @foreach($igrejas as $igreja) <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option> @endforeach
                                </select>
                                @error('igreja_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 border rounded-md dark:border-gray-700">
                            <div>
                                <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                                <select wire:model.lazy="categoria_id" id="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                    <option value="">Selecione</option>
                                    @foreach($allCategorias as $categoria) <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option> @endforeach
                                </select>
                                @error('categoria_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="cargo_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
                                <select wire:model.lazy="cargo_id" id="cargo_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                    <option value="">Selecione</option>
                                    @foreach($allCargos as $cargo) <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option> @endforeach
                                </select>
                                @error('cargo_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="grupo_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @if($selectedBlocoName === 'Catedral') Grupo Catedral @else Grupo @endif
                                </label>
                                <select wire:model.lazy="grupo_id" id="grupo_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" @if($selectedBlocoName !== 'Catedral') disabled @endif>
                                    <option value="">Selecione</option>
                                    @foreach($allGrupos as $grupo) <option value="{{ $grupo->id }}">{{ $grupo->nome }}</option> @endforeach
                                </select>
                                @error('grupo_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($step === 3)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">2. Dados Pessoais</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome Completo</label>
                            <input type="text" wire:model.lazy="nome" id="nome" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('nome') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="celular" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular (com DDD)</label>
                            <input type="text" wire:model.lazy="celular" id="celular" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('celular') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" wire:model.lazy="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>
                            <input type="file" required wire:model="foto" id="foto" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/50 dark:file:text-blue-300 dark:hover:file:bg-blue-800/50">
                            @if ($foto) <img src="{{ $foto->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover rounded-full"> @endif
                            @error('foto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif
                @if($step === 4)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">3. Endereço</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="estado_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                            <select wire:model.live="estado_id" id="estado_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                                <option value="">Selecione</option>
                                @foreach($allEstados as $estado) <option value="{{ $estado->id }}">{{ $estado->nome }}</option> @endforeach
                            </select>
                            @error('estado_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="cidade_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                            <select wire:model.lazy="cidade_id" id="cidade_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700" @if(empty($cidades)) disabled @endif>
                                <option value="">Selecione</option>
                                @foreach($cidades as $cidade) <option value="{{ $cidade->id }}">{{ $cidade->nome }}</option> @endforeach
                            </select>
                            @error('cidade_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço (Rua, Nº)</label>
                            <input type="text" wire:model.lazy="endereco" id="endereco" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('endereco') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label>
                            <input type="text" wire:model.lazy="bairro" id="bairro" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('bairro') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                            <input type="text" wire:model.lazy="cep" id="cep" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                            @error('cep') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif
                @if($step === 5)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">4. Informações Adicionais</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8">
                        <div>
                            <label for="profissao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profissão</label>
                            <input type="text" wire:model.lazy="profissao" id="profissao" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                        </div>
                        <div>
                            <label for="aptidoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aptidões</label>
                            <input type="text" wire:model.lazy="aptidoes" id="aptidoes" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                        </div>
                        <div>
                            <label for="conversao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Conversão</label>
                            <input type="date" wire:model.lazy="conversao" id="conversao" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                        </div>
                        <div>
                            <label for="obra" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Entrada na Obra</label>
                            <input type="date" wire:model.lazy="obra" id="obra" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700">
                        </div>
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Trabalho</p>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="trabalho" value="interno" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Interno</span></label>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="trabalho" value="externo" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Externo</span></label>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Batismo</p>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="batismo" value="aguas" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Águas</span></label>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="batismo" value="espirito_santo" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Espírito Santo</span></label>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Já foi preso?</p>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="preso" value="sim" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Sim</span></label>
                                <label class="flex items-center"><input type="checkbox" wire:model.lazy="preso" value="nao" class="rounded text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"><span class="ml-2">Não</span></label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($step === 6)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">5. Testemunho</h3>
                    <textarea wire:model.lazy="testemunho" rows="6" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700"></textarea>
                    @error('testemunho') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @endif
            @endif

            {{-- Etapa 7: Assinatura com Alpine.js --}}
                @if($step === 7)
                <div 
                    wire:ignore
                    x-data="signaturePad()" {{-- (1) Inicia o componente Alpine --}}
                    x-init="init()"         {{-- (2) Chama a função de inicialização --}}
                >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">6. Assinatura</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Por favor, assine no campo abaixo:</p>
                    
                    <div id="signature-pad-container" class="relative w-full max-w-lg mx-auto border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg touch-none" style="min-height: 250px;">
                        {{-- (3) Adiciona x-ref para que o Alpine encontre o canvas --}}
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                    </div>

                    <div class="flex flex-wrap justify-center items-center gap-4 mt-4">
                        {{-- (4) Botões agora usam @click para chamar as funções do Alpine --}}
                        <button @click="clear()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Limpar</button>
                        <button @click="save()" type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Salvar Assinatura</button>
                        {{-- O botão de tela cheia pode ser adaptado de forma similar se necessário --}}
                    </div>
                    @error('assinatura') <span class="text-red-500 text-sm mt-2 block text-center">{{ $message }}</span> @enderror
                </div>
                @endif

            {{-- Etapa 8: Confirmação --}}
            @if($step === 8)
            <div class="text-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Concluir Cadastro!</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Para enviar sua ficha, Click no botão abaixo "Finalizar Cadastro"!<br>
                    O seu cadastro no UNP – Universal nos Presídios foi concluído com sucesso e será analisado pela Secretaria responsável.

Agradecemos pelo seu interesse e disposição em participar deste projeto.

Por meio do Projeto Universal nos Presídios (UNP), uma nova perspectiva de vida é oferecida àqueles que um dia foram sentenciados pelos seus erros, através da Palavra de Deus, provando que todos têm a oportunidade de recomeçar e trilhar um novo caminho.
                </p>
            </div>
            @endif
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                @if($step > 1 && $step <= $totalSteps)
                    <button wire:click="previousStep" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">Anterior</button>
                @else
                    <div></div> {{-- Placeholder para manter o botão da direita alinhado --}}
                @endif

                @if($step < 8)
                    <button wire:click="nextStep" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Próximo</button>
                @elseif($step === 8)
                    <button wire:click="submit" type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Finalizar Cadastro</button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', () => ({
            signaturePadInstance: null,
            
            init() {
                // Carrega a biblioteca do SignaturePad sob demanda
                if (typeof SignaturePad === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/signature_pad@5.0.10/dist/signature_pad.umd.min.js';
                    script.onload = () => {
                        this.initializePad();
                    };
                    document.head.appendChild(script);
                } else {
                    this.initializePad();
                }
            },

            initializePad() {
                this.resizeCanvas();
                this.signaturePadInstance = new SignaturePad(this.$refs.canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                });
                window.addEventListener('resize', () => this.resizeCanvas());
            },

            resizeCanvas() {
                const canvas = this.$refs.canvas;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                // Define o tamanho do canvas baseado no seu container
                canvas.width = canvas.parentElement.offsetWidth * ratio;
                canvas.height = canvas.parentElement.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                
                if (this.signaturePadInstance) {
                    this.signaturePadInstance.clear();
                }
            },

            clear() {
                if (this.signaturePadInstance) {
                    this.signaturePadInstance.clear();
                }
            },

            save() {
                if (this.signaturePadInstance && !this.signaturePadInstance.isEmpty()) {
                    const dataURL = this.signaturePadInstance.toDataURL('image/png');
                    this.$wire.set('assinatura', dataURL);
                    alert('Assinatura salva!');
                } else {
                    alert('Por favor, forneça sua assinatura.');
                }
            }
        }));
    });
</script>
