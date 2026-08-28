@section('title', 'Cadastro de Credenciado')

<div class="min-h-screen bg-slate-100 px-3 py-5 text-slate-900 dark:bg-slate-950 dark:text-slate-100 sm:px-6 sm:py-8">
    <div class="mx-auto max-w-5xl" x-data>
        <header class="mb-5 overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-indigo-800 to-slate-900 p-5 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.2em] text-blue-200">Captação segura</p>
                    <h1 class="mt-2 text-2xl font-black sm:text-3xl">Cadastro de credenciado</h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-100">Preencha as etapas com atenção. Os dados serão analisados antes de integrarem o cadastro oficial.</p>
                </div>
                <div class="shrink-0 rounded-2xl bg-white/10 px-4 py-3 text-center backdrop-blur">
                    <strong class="block text-2xl">{{ $progressPercentage }}%</strong>
                    <span class="text-xs text-blue-100">concluído</span>
                </div>
            </div>
            <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/15">
                <div class="h-full rounded-full bg-cyan-300 transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
            </div>
        </header>

        <nav class="mb-5 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900" aria-label="Progresso">
            <ol class="flex min-w-max gap-2">
                @foreach($stepTitles as $position => $title)
                    <li class="flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold {{ $currentStep === $position + 1 ? 'bg-blue-600 text-white' : ($currentStep > $position + 1 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400') }}">
                        <span>{{ $position + 1 }}</span><span>{{ $title }}</span>
                    </li>
                @endforeach
            </ol>
        </nav>

        @if($errorMessage)
            <div class="mb-5 rounded-2xl border border-red-300 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-300" role="alert">{{ $errorMessage }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200" role="alert">
                <p class="font-black">Revise os campos destacados antes de continuar.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form wire:submit="submit" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <div class="p-5 sm:p-8">
                @if($currentStep === 1)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Vínculo com a igreja</h2><p class="text-sm text-slate-500 dark:text-slate-400">Selecione a estrutura organizacional correta.</p></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-captacao-select label="Bloco" model="bloco_id" :items="$allBlocos" live />
                            <x-captacao-select label="Região" model="regiao_id" :items="$regiaos" live />
                            <div class="sm:col-span-2"><x-captacao-select label="Igreja" model="igreja_id" :items="$igrejas" /></div>
                            <x-captacao-select label="Categoria" model="categoria_id" :items="$allCategorias" />
                            <x-captacao-select label="Cargo / função" model="cargo_id" :items="$allCargos" />
                            <div class="sm:col-span-2"><x-captacao-select label="Grupo (opcional)" model="grupo_id" :items="$allGrupos" /></div>
                        </div>
                    </section>
                @elseif($currentStep === 2)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Dados pessoais</h2><p class="text-sm text-slate-500 dark:text-slate-400">CPF e contatos são normalizados e validados no envio.</p></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2"><x-captacao-input label="Nome completo" model="nome" autocomplete="name" /></div>
                            <x-captacao-input label="CPF" model="cpf" inputmode="numeric" autocomplete="off" />
                            <x-captacao-input label="Celular" model="celular" inputmode="tel" autocomplete="tel" />
                            <x-captacao-input label="Telefone (opcional)" model="telefone" inputmode="tel" />
                            <x-captacao-input label="E-mail (opcional)" model="email" type="email" autocomplete="email" />
                        </div>
                    </section>
                @elseif($currentStep === 3)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Endereço</h2><p class="text-sm text-slate-500 dark:text-slate-400">Informe o endereço atual para conferência cadastral.</p></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-captacao-select label="Estado" model="estado_id" :items="$allEstados" live />
                            <x-captacao-select label="Cidade" model="cidade_id" :items="$cidades" />
                            <div class="sm:col-span-2"><x-captacao-input label="Endereço" model="endereco" autocomplete="street-address" /></div>
                            <x-captacao-input label="Bairro" model="bairro" />
                            <x-captacao-input label="CEP (opcional)" model="cep" inputmode="numeric" autocomplete="postal-code" />
                        </div>
                    </section>
                @elseif($currentStep === 4)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Perfil profissional</h2><p class="text-sm text-slate-500 dark:text-slate-400">Essas informações auxiliam na organização das atividades.</p></div>
                        <x-captacao-input label="Profissão (opcional)" model="profissao" />
                        <x-captacao-textarea label="Aptidões e habilidades (opcional)" model="aptidoes" rows="6" />
                    </section>
                @elseif($currentStep === 5)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Histórico ministerial</h2><p class="text-sm text-slate-500 dark:text-slate-400">Responda somente o que se aplica ao seu histórico.</p></div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="space-y-1"><span class="text-sm font-bold">Data de conversão (opcional)</span><input type="date" wire:model="conversao" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950"></label>
                            <label class="space-y-1"><span class="text-sm font-bold">Entrada na obra (opcional)</span><input type="date" wire:model="obra" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950"></label>
                            <div class="sm:col-span-2"><x-captacao-textarea label="Testemunho (opcional)" model="testemunho" rows="6" /></div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                <legend class="px-1 text-sm font-bold">Batismo</legend>
                                <label class="mt-2 flex items-center gap-2"><input type="checkbox" wire:model="batismo" value="aguas" class="rounded text-blue-600"><span class="text-sm">Nas águas</span></label>
                                <label class="mt-3 flex items-center gap-2"><input type="checkbox" wire:model="batismo" value="espirito" class="rounded text-blue-600"><span class="text-sm">No Espírito Santo</span></label>
                            </fieldset>
                            <fieldset class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                                <legend class="px-1 text-sm font-bold">Já esteve preso?</legend>
                                <label class="mt-2 flex items-center gap-2"><input type="radio" wire:model="preso.0" value="Sim" class="text-blue-600"><span class="text-sm">Sim</span></label>
                                <label class="mt-3 flex items-center gap-2"><input type="radio" wire:model="preso.0" value="Não" class="text-blue-600"><span class="text-sm">Não</span></label>
                            </fieldset>
                        </div>
                    </section>
                @elseif($currentStep === 6)
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Foto e documentos</h2><p class="text-sm text-slate-500 dark:text-slate-400">JPG, PNG ou WebP, com no máximo 5 MB por arquivo. Fotografe com boa iluminação.</p></div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            @foreach(['foto' => 'Foto de perfil', 'identidade_frente' => 'Identidade — frente', 'identidade_verso' => 'Identidade — verso'] as $field => $label)
                                <label class="group flex min-h-44 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-4 text-center transition hover:border-blue-500 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-950 dark:hover:border-blue-500">
                                    <span class="font-bold">{{ $label }}</span>
                                    <span class="mt-2 text-xs text-slate-500">Toque ou clique para selecionar</span>
                                    @if($this->{$field})<span class="mt-3 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">Arquivo selecionado</span>@endif
                                    <input type="file" wire:model="{{ $field }}" accept="image/jpeg,image/png,image/webp" class="sr-only">
                                </label>
                            @endforeach
                        </div>
                    </section>
                @else
                    <section class="space-y-6">
                        <div><h2 class="text-xl font-black">Credenciais por presídio</h2><p class="text-sm text-slate-500 dark:text-slate-400">Inclua somente unidades com as quais exista vínculo. Renovação é opcional.</p></div>
                        @error('credenciais')<p class="rounded-xl bg-red-50 p-3 text-sm font-bold text-red-700">{{ $message }}</p>@enderror
                        <div class="space-y-5">
                            @forelse($credenciais as $index => $credencial)
                                <article wire:key="captacao-credencial-{{ $index }}" class="relative rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950 sm:p-5">
                                    <button type="button" wire:click="removeCredencial({{ $index }})" class="absolute right-3 top-3 rounded-lg bg-red-100 px-3 py-1.5 text-xs font-black text-red-700 dark:bg-red-950 dark:text-red-300">Remover</button>
                                    <div class="grid gap-4 pt-8 sm:grid-cols-2 lg:grid-cols-3">
                                        <label class="space-y-1 sm:col-span-2 lg:col-span-3"><span class="text-sm font-bold">Presídio</span><select wire:model="credenciais.{{ $index }}.presidio_id" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900"><option value="">Selecione</option>@foreach($allPresidios as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach</select></label>
                                        <label class="space-y-1"><span class="text-sm font-bold">Primeira credencial</span><input type="date" wire:model="credenciais.{{ $index }}.data_primeira_credencial" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900"></label>
                                        <label class="space-y-1"><span class="text-sm font-bold">Renovação (opcional)</span><input type="date" wire:model="credenciais.{{ $index }}.data_renovacao" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900"></label>
                                        <label class="space-y-1"><span class="text-sm font-bold">Validade</span><input type="date" wire:model="credenciais.{{ $index }}.data_vencimento" class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900"></label>
                                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900 sm:col-span-2 lg:col-span-3"><input type="checkbox" wire:model="credenciais.{{ $index }}.unidade_nao_faz" class="rounded border-slate-300 text-blue-600"><span class="text-sm font-semibold">Esta unidade não emite credencial</span></label>
                                        <label class="cursor-pointer rounded-xl border-2 border-dashed border-slate-300 p-4 text-center text-sm font-bold dark:border-slate-700"><span>Foto da frente</span>@if($credencial['foto_frente'] ?? null)<span class="mt-1 block text-xs text-emerald-600">Selecionada</span>@endif<input type="file" wire:model="credenciais.{{ $index }}.foto_frente" accept="image/jpeg,image/png,image/webp" class="sr-only"></label>
                                        <label class="cursor-pointer rounded-xl border-2 border-dashed border-slate-300 p-4 text-center text-sm font-bold dark:border-slate-700"><span>Foto do verso</span>@if($credencial['foto_verso'] ?? null)<span class="mt-1 block text-xs text-emerald-600">Selecionada</span>@endif<input type="file" wire:model="credenciais.{{ $index }}.foto_verso" accept="image/jpeg,image/png,image/webp" class="sr-only"></label>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700">Nenhuma credencial informada.</div>
                            @endforelse
                        </div>
                        @if(count($credenciais) < 10)<button type="button" wire:click="addCredencial" class="w-full rounded-xl border-2 border-dashed border-blue-300 p-3 text-sm font-black text-blue-700 transition hover:bg-blue-50 dark:border-blue-800 dark:text-blue-300 dark:hover:bg-blue-950">+ Adicionar outro presídio</button>@endif
                        <label class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40">
                            <input type="checkbox" wire:model="aceitePrivacidade" class="mt-1 rounded border-slate-300 text-blue-600">
                            <span class="text-sm text-blue-950 dark:text-blue-100"><strong>Confirmo que os dados e arquivos são verdadeiros</strong> e autorizo seu uso para análise e gestão do credenciamento, com acesso restrito aos responsáveis.</span>
                        </label>
                        @error('aceitePrivacidade')<p class="text-sm font-bold text-red-600">{{ $message }}</p>@enderror
                    </section>
                @endif
            </div>

            <footer class="sticky bottom-0 z-20 flex items-center justify-between gap-3 border-t border-slate-200 bg-white/95 p-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 sm:px-8">
                <button type="button" wire:click="prevStep" @disabled($currentStep === 1) class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-black disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700">Voltar</button>
                <span class="hidden text-xs font-bold text-slate-500 sm:block">Etapa {{ $currentStep }} de {{ $totalSteps }}</span>
                @if($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white shadow transition hover:bg-blue-700">Continuar</button>
                @else
                    <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow transition hover:bg-emerald-700 disabled:opacity-60">Enviar para análise</button>
                @endif
            </footer>
        </form>
    </div>

    <div wire:loading.flex wire:target="nextStep,submit,foto,identidade_frente,identidade_verso,credenciais" class="fixed inset-0 z-[100] items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
        <div class="rounded-2xl bg-white px-6 py-5 text-center font-black text-slate-800 shadow-2xl dark:bg-slate-900 dark:text-white">Processando com segurança…</div>
    </div>
</div>
