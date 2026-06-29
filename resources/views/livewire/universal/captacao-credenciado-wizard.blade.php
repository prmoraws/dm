   @section('title', 'Cadastro Credencial')
   <div class="max-w-4xl mx-auto my-8 p-4" x-data>

       {{-- SEÇÃO DE DEBUG (Remova após os testes) --}}
       @if ($errors->any())
           <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
               <strong>Atenção:</strong>
               <ul>
                   @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                   @endforeach
               </ul>
           </div>
       @endif

       {{-- Barra de Progresso --}}
       @php $percentage = round((($currentStep - 1) / $totalSteps) * 100); @endphp
       <div class="mb-8">
           <div class="flex justify-between mb-2">
               <span class="text-xs font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">Passo {{ $currentStep }}
                   de {{ $totalSteps }}</span>
               <span class="text-xs font-bold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">{{ $percentage }}%
                   Concluído</span>
           </div>
           <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
               <div class="bg-blue-600 h-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
           </div>
       </div>

       <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-6 md:p-10 border dark:border-gray-700">
           <form wire:submit.prevent="submit">

               {{-- PASSO 1: DADOS DA IGREJA --}}
               @if ($currentStep == 1)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Dados da Igreja</h3>
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <div>
                               <label class="text-sm dark:text-gray-300">Bloco</label>
                               <select wire:model.live="bloco_id"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                                   <option value="">Selecione</option>
                                   @foreach ($allBlocos as $b)
                                       <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Região</label>
                               <select wire:model.live="regiao_id"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300"
                                   @empty($regiaos) disabled @endempty>
                                   <option value="">Selecione</option>
                                   @foreach ($regiaos as $r)
                                       <option value="{{ $r->id }}">{{ $r->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div class="md:col-span-2">
                               <label class="text-sm dark:text-gray-300">Igreja</label>
                               <select wire:model="igreja_id"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300"
                                   @empty($igrejas) disabled @endempty>
                                   <option value="">Selecione</option>
                                   @foreach ($igrejas as $i)
                                       <option value="{{ $i->id }}">{{ $i->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Categoria</label>
                               <select wire:model="categoria_id"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                                   <option value="">Selecione</option>
                                   @foreach ($allCategorias as $c)
                                       <option value="{{ $c->id }}">{{ $c->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Cargo</label>
                               <select wire:model="cargo_id"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                                   <option value="">Selecione</option>
                                   @foreach ($allCargos as $cg)
                                       <option value="{{ $cg->id }}">{{ $cg->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           {{-- Campo Grupo --}}
                           <div class="md:col-span-2" x-data="{
                               isCatedral: false,
                               init() {
                                   // Força o estado inicial: Desmarcado e valor 11
                                   this.isCatedral = false;
                                   $wire.set('grupo_id', 11);
                               },
                               toggleCatedral() {
                                   this.isCatedral = !this.isCatedral;
                                   if (this.isCatedral) {
                                       // Se marcou: Libera e limpa o campo para o usuário selecionar
                                       $wire.set('grupo_id', '');
                                   } else {
                                       // Se desmarcou: Trava e volta para 11
                                       $wire.set('grupo_id', 11);
                                   }
                               }
                           }" x-init="init()">

                               <div class="flex items-center mb-2">
                                   <input type="checkbox" id="chk_catedral"
                                       class="rounded text-blue-600 focus:ring-blue-500 mr-2 w-5 h-5 cursor-pointer"
                                       :checked="isCatedral" @change="toggleCatedral()">
                                   <label for="chk_catedral"
                                       class="text-sm font-bold text-blue-600 cursor-pointer select-none">
                                       Grupo da Catedral
                                   </label>
                               </div>

                               <select wire:model.live="grupo_id"
                                   class="w-full rounded-lg border-gray-300 shadow-sm
                                          dark:bg-gray-900 dark:text-white dark:border-gray-600
                                          disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                   :disabled="!isCatedral">
                                   <option value="">Selecione o Grupo...</option>
                                   @foreach ($allGrupos as $g)
                                       <option value="{{ $g->id }}">{{ $g->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                   </div>
               @endif

               {{-- PASSO 2: DADOS PESSOAIS --}}
               @if ($currentStep == 2)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Informações Pessoais</h3>
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <div class="md:col-span-2">
                               <label class="text-sm dark:text-gray-300">Nome Completo</label>
                               <input type="text" wire:model="nome"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">CPF</label>
                               <input type="text" wire:model="cpf" x-mask="999.999.999-99"
                                   placeholder="000.000.000-00"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Celular (WhatsApp)</label>
                               <input type="text" wire:model="celular" x-mask="(99) 99999-9999"
                                   placeholder="(00) 00000-0000"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Telefone Fixo</label>
                               <input type="text" wire:model="telefone" x-mask="(99) 9999-9999"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">E-mail</label>
                               <input type="email" wire:model="email"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                       </div>
                   </div>
               @endif

               {{-- PASSO 3: ENDEREÇO --}}
               @if ($currentStep == 3)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Endereço</h3>
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <div>
                               <label class="text-sm dark:text-gray-300">CEP</label>
                               <input type="text" wire:model="cep" x-mask="99999-999"
                                   class="w-full rounded-lg dark:bg-gray-900 dark:text-white border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Estado</label>
                               <select wire:model.live="estado_id"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                                   <option value="">Selecione</option>
                                   @foreach ($allEstados as $e)
                                       <option value="{{ $e->id }}">{{ $e->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div class="md:col-span-2">
                               <label class="text-sm dark:text-gray-300">Cidade</label>
                               <select wire:model="cidade_id" class="w-full rounded-lg dark:bg-gray-900 border-gray-300"
                                   @empty($cidades) disabled @endempty>
                                   <option value="">Selecione</option>
                                   @foreach ($cidades as $c)
                                       <option value="{{ $c->id }}">{{ $c->nome }}</option>
                                   @endforeach
                               </select>
                           </div>
                           <div class="md:col-span-2">
                               <label class="text-sm dark:text-gray-300">Endereço (Rua e Número)</label>
                               <input type="text" wire:model="endereco"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                           </div>
                           <div class="md:col-span-2">
                               <label class="text-sm dark:text-gray-300">Bairro</label>
                               <input type="text" wire:model="bairro"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                           </div>
                       </div>
                   </div>
               @endif

               {{-- PASSO 4: PROFISSIONAL --}}
               @if ($currentStep == 4)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Profissional e Aptidões</h3>
                       <div class="space-y-4">
                           <div>
                               <label class="text-sm dark:text-gray-300">Sua Profissão</label>
                               <input type="text" wire:model="profissao"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Aptidões / Habilidades</label>
                               <textarea wire:model="aptidoes" rows="3" placeholder="Ex: Informática, Música, Eletricista..."
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300"></textarea>
                           </div>
                       </div>
                   </div>
               @endif

               {{-- PASSO 5: VIDA CRISTÃ E HISTÓRICO PRISIONAL --}}
               @if ($currentStep == 5)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Vida Cristã e Histórico</h3>
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           <div>
                               <label class="text-sm dark:text-gray-300">Data de Conversão</label>
                               <input type="date" wire:model="conversao"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                           </div>
                           <div>
                               <label class="text-sm dark:text-gray-300">Data de Entrada na Obra</label>
                               <input type="date" wire:model="obra"
                                   class="w-full rounded-lg dark:bg-gray-900 border-gray-300">
                           </div>
                           <div class="md:col-span-2">
                               <label class="block text-sm font-bold dark:text-gray-300 mb-2">Batismo</label>
                               <div class="flex gap-4">
                                   <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                           value="aguas" class="rounded text-blue-600"><span
                                           class="ml-2 text-sm dark:text-gray-400">Nas Águas</span></label>
                                   <label class="flex items-center"><input type="checkbox" wire:model="batismo"
                                           value="espirito" class="rounded text-blue-600"><span
                                           class="ml-2 text-sm dark:text-gray-400">No Espírito Santo</span></label>
                               </div>
                           </div>

                           {{-- PARTE DO PRESO MOVIDA PARA CÁ --}}
                           <div
                               class="md:col-span-2 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100">
                               <label class="block text-sm font-bold text-red-600 dark:text-red-400 mb-2">Já esteve
                                   preso?</label>
                               <div class="flex gap-6">
                                   <label class="flex items-center">
                                       <input type="radio" wire:model="preso" value="Sim"
                                           class="text-red-600 focus:ring-red-500">
                                       <span class="ml-2 text-sm dark:text-gray-300">Sim, já estive preso</span>
                                   </label>
                                   <label class="flex items-center">
                                       <input type="radio" wire:model="preso" value="Não"
                                           class="text-red-600 focus:ring-red-500">
                                       <span class="ml-2 text-sm dark:text-gray-300">Nunca estive preso</span>
                                   </label>
                               </div>
                           </div>
                       </div>
                   </div>
               @endif

               {{-- PASSO 6: TESTEMUNHO (Antigo Passo 7) --}}
               @if ($currentStep == 6)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-6 border-b pb-2">Seu Testemunho</h3>
                       <textarea wire:model="testemunho" rows="8" placeholder="Escreva resumidamente sua mudança de vida..."
                           class="w-full rounded-xl dark:bg-gray-900 border-gray-300"></textarea>
                   </div>
               @endif

               {{-- PASSO 7: DOCUMENTOS --}}
               @if ($currentStep == 7)
                   <div class="animate-fade-in">
                       <h3 class="text-xl font-bold dark:text-white mb-2 border-b pb-2">Fotos e Credenciais</h3>
                       <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                           Envie sua foto 3x4 e adicione suas credenciais de acesso aos presídios.
                       </p>

                       {{-- FOTO DE PERFIL E RG --}}
                       <div
                           class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 mb-8 border border-gray-200 dark:border-gray-700">
                           <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 flex items-center">
                               <span
                                   class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                               Dados Básicos
                           </h4>

                           {{-- Foto de Perfil (3x4) --}}
                           <div class="flex flex-col items-center mb-6">
                               <div class="relative group cursor-pointer">
                                   @if ($foto)
                                       <img src="{{ $foto->temporaryUrl() }}"
                                           class="h-32 w-24 object-cover rounded shadow-lg border-2 border-white">
                                   @else
                                       <div
                                           class="h-32 w-24 rounded bg-gray-200 dark:bg-gray-700 flex flex-col items-center justify-center border-2 border-dashed border-gray-400 shadow-sm">
                                           <svg class="w-8 h-8 text-gray-400 mb-1" fill="none"
                                               stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                               </path>
                                           </svg>
                                           <span class="text-[10px] text-gray-500 font-bold">3x4</span>
                                       </div>
                                   @endif
                                   <label
                                       class="absolute -bottom-3 -right-3 bg-blue-600 text-white p-2 rounded-full shadow-md cursor-pointer hover:bg-blue-700 transition z-10">
                                       <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                           viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                               d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                           </path>
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                               d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                       </svg>
                                       <input type="file" wire:model="foto" class="hidden">
                                   </label>
                               </div>
                               <span class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-3">Foto 3x4</span>
                           </div>

                           {{-- RG Frente e Verso --}}
                           <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                               {{-- RG Frente --}}
                               <label
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors {{ $identidade_frente ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600' }}">
                                   <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                       @if ($identidade_frente)
                                           <svg class="w-8 h-8 text-green-500 mb-2" fill="none"
                                               stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M5 13l4 4L19 7"></path>
                                           </svg>
                                           <p class="text-sm text-green-600 font-bold">RG Frente OK</p>
                                       @else
                                           <span
                                               class="text-xs font-bold uppercase text-gray-400 mb-1">Documento</span>
                                           <span class="text-sm text-gray-600 dark:text-gray-300 font-bold">Enviar
                                               Frente</span>
                                       @endif
                                   </div>
                                   <input type="file" wire:model="identidade_frente" class="hidden" />
                               </label>

                               {{-- RG Verso --}}
                               <label
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors {{ $identidade_verso ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600' }}">
                                   <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                       @if ($identidade_verso)
                                           <svg class="w-8 h-8 text-green-500 mb-2" fill="none"
                                               stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M5 13l4 4L19 7"></path>
                                           </svg>
                                           <p class="text-sm text-green-600 font-bold">RG Verso OK</p>
                                       @else
                                           <span
                                               class="text-xs font-bold uppercase text-gray-400 mb-1">Documento</span>
                                           <span class="text-sm text-gray-600 dark:text-gray-300 font-bold">Enviar
                                               Verso</span>
                                       @endif
                                   </div>
                                   <input type="file" wire:model="identidade_verso" class="hidden" />
                               </label>
                           </div>
                       </div>

                       {{-- SEÇÃO CREDENCIAIS PRESÍDIO --}}
                       <div class="border-t pt-6">
                           <h4 class="font-bold text-gray-700 dark:text-gray-200 text-lg flex items-center mb-6">
                               <span
                                   class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                               Credenciais de Presídios
                           </h4>

                           <div class="space-y-6">
                               @foreach ($credenciais as $index => $cred)
                                   <div
                                       class="p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative animate-fade-in-up">

                                       <button type="button" wire:click="removeCredencial({{ $index }})"
                                           class="absolute -top-3 -right-3 bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:bg-red-600 z-10"
                                           title="Remover">
                                           <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                               viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M6 18L18 6M6 6l12 12"></path>
                                           </svg>
                                       </button>

                                       <div class="space-y-4">
                                           <div>
                                               <label
                                                   class="block text-xs font-bold text-blue-600 uppercase mb-1">Passo
                                                   A: Selecione a Unidade</label>
                                               <select wire:model.live="credenciais.{{ $index }}.presidio_id"
                                                   class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:text-white dark:border-gray-600 p-3 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                   <option value="">Toque para selecionar o Presídio...</option>
                                                   @foreach ($allPresidios as $p)
                                                       <option value="{{ $p->id }}">{{ $p->nome }}
                                                       </option>
                                                   @endforeach
                                               </select>
                                           </div>

                                           @if (!empty($credenciais[$index]['presidio_id']))
                                               <div class="animate-fade-in">
                                                   <label
                                                       class="block text-xs font-bold text-blue-600 uppercase mb-2 mt-4">Passo
                                                       B: Fotos da Credencial</label>
                                                   <div class="grid grid-cols-2 gap-3">
                                                       {{-- Foto Frente --}}
                                                       <label
                                                           class="relative flex flex-col items-center justify-center h-24 rounded-lg border-2 border-dashed cursor-pointer transition-all
                                            {{ isset($credenciais[$index]['foto_frente']) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:border-gray-600' }}">
                                                           <div class="text-center p-2">
                                                               @if (isset($credenciais[$index]['foto_frente']))
                                                                   <svg class="w-6 h-6 text-green-500 mx-auto"
                                                                       fill="none" stroke="currentColor"
                                                                       viewBox="0 0 24 24">
                                                                       <path stroke-linecap="round"
                                                                           stroke-linejoin="round" stroke-width="2"
                                                                           d="M5 13l4 4L19 7"></path>
                                                                   </svg>
                                                                   <span
                                                                       class="text-xs text-green-700 font-bold block mt-1">Frente
                                                                       OK</span>
                                                               @else
                                                                   <span
                                                                       class="text-xs text-gray-500 font-bold block">FRENTE</span>
                                                                   <span
                                                                       class="text-[10px] text-gray-400 block mt-1">Toque
                                                                       para enviar</span>
                                                               @endif
                                                           </div>
                                                           <input type="file"
                                                               wire:model="credenciais.{{ $index }}.foto_frente"
                                                               class="hidden">
                                                       </label>

                                                       {{-- Foto Verso --}}
                                                       <label
                                                           class="relative flex flex-col items-center justify-center h-24 rounded-lg border-2 border-dashed cursor-pointer transition-all
                                            {{ isset($credenciais[$index]['foto_verso']) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:border-gray-600' }}">
                                                           <div class="text-center p-2">
                                                               @if (isset($credenciais[$index]['foto_verso']))
                                                                   <svg class="w-6 h-6 text-green-500 mx-auto"
                                                                       fill="none" stroke="currentColor"
                                                                       viewBox="0 0 24 24">
                                                                       <path stroke-linecap="round"
                                                                           stroke-linejoin="round" stroke-width="2"
                                                                           d="M5 13l4 4L19 7"></path>
                                                                   </svg>
                                                                   <span
                                                                       class="text-xs text-green-700 font-bold block mt-1">Verso
                                                                       OK</span>
                                                               @else
                                                                   <span
                                                                       class="text-xs text-gray-500 font-bold block">VERSO</span>
                                                                   <span
                                                                       class="text-[10px] text-gray-400 block mt-1">Toque
                                                                       para enviar</span>
                                                               @endif
                                                           </div>
                                                           <input type="file"
                                                               wire:model="credenciais.{{ $index }}.foto_verso"
                                                               class="hidden">
                                                       </label>
                                                   </div>
                                               </div>
                                           @else
                                               <div
                                                   class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 rounded-lg p-3 flex items-center text-yellow-700 dark:text-yellow-500">
                                                   <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none"
                                                       stroke="currentColor" viewBox="0 0 24 24">
                                                       <path stroke-linecap="round" stroke-linejoin="round"
                                                           stroke-width="2"
                                                           d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                       </path>
                                                   </svg>
                                                   <span class="text-xs">Selecione o presídio para liberar o envio das
                                                       fotos.</span>
                                               </div>
                                           @endif
                                       </div>
                                   </div>
                               @endforeach

                               {{-- BOTÃO DE ADICIONAR (Lógica condicional) --}}
                               <div class="mt-6">
                                   @if (count($credenciais) == 0)
                                       <button type="button" wire:click="addCredencial"
                                           class="w-full py-4 border-2 border-dashed border-blue-300 bg-blue-50 text-blue-600 rounded-xl font-bold hover:bg-blue-100 transition flex flex-col items-center justify-center">
                                           <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor"
                                               viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z">
                                               </path>
                                           </svg>
                                           Adicionar Minha Primeira Credencial
                                       </button>
                                   @else
                                       <button type="button" wire:click="addCredencial"
                                           class="w-full py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-bold hover:bg-gray-200 transition flex items-center justify-center border border-gray-300">
                                           <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                               viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                   d="M12 4v16m8-8H4"></path>
                                           </svg>
                                           Adicionar Outra Credencial
                                       </button>
                                   @endif
                               </div>
                           </div>
                       </div>
                   </div>
               @endif

               {{-- Navegação com Proteção de Upload --}}
               <div class="flex justify-between mt-10 pt-6 border-t border-gray-100">

                   {{-- Botão Anterior --}}
                   @if ($currentStep > 1)
                       <button type="button" wire:click="prevStep" wire:loading.attr="disabled"
                           class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 disabled:opacity-50">
                           Anterior
                       </button>
                   @else
                       <div></div>
                   @endif

                   {{-- Lógica do Botão Próximo / Finalizar --}}
                   @if ($currentStep < $totalSteps)
                       {{-- Botão Próximo --}}
                       <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                           wire:target="foto, identidade_frente, identidade_verso, credenciais"
                           class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-wait flex items-center">

                           {{-- Texto normal --}}
                           <span wire:loading.remove
                               wire:target="foto, identidade_frente, identidade_verso, credenciais">
                               Próximo
                           </span>

                           {{-- Texto durante upload --}}
                           <span wire:loading wire:target="foto, identidade_frente, identidade_verso, credenciais"
                               class="flex items-center">
                               <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                   xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10"
                                       stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor"
                                       d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                   </path>
                               </svg>
                               Enviando Foto...
                           </span>
                       </button>
                   @else
                       {{-- Botão Finalizar --}}
                       <button type="submit" wire:loading.attr="disabled"
                           wire:target="foto, identidade_frente, identidade_verso, credenciais, submit"
                           class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold shadow-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-wait flex items-center">

                           <span wire:loading.remove
                               wire:target="foto, identidade_frente, identidade_verso, credenciais, submit">
                               Finalizar Envio
                           </span>

                           <span wire:loading wire:target="foto, identidade_frente, identidade_verso, credenciais"
                               class="flex items-center">
                               <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                   xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10"
                                       stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor"
                                       d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                   </path>
                               </svg>
                               Aguarde o Upload...
                           </span>

                           <span wire:loading wire:target="submit" class="flex items-center">
                               <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                   xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                   <circle class="opacity-25" cx="12" cy="12" r="10"
                                       stroke="currentColor" stroke-width="4"></circle>
                                   <path class="opacity-75" fill="currentColor"
                                       d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                   </path>
                               </svg>
                               Salvando...
                           </span>
                       </button>
                   @endif
               </div>
           </form>
       </div>
   </div>
