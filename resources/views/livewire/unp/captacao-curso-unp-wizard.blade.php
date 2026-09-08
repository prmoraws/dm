@section('title', 'Inscrição de novos voluntários da UNP')

<div class="min-h-screen bg-slate-100 px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-3xl">
        <div class="overflow-hidden rounded-2xl bg-white shadow-xl">
            <header class="bg-gradient-to-r from-red-800 to-red-600 px-6 py-7 text-white">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/unp/unp-logo.png') }}" alt="Universal nos Presídios"
                        class="h-20 w-36 shrink-0 rounded-lg bg-white p-2 object-contain sm:h-24 sm:w-44">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-red-100">Curso Preparatório de Voluntários</p>
                        <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Inscrição de novos voluntários da UNP</h1>
                        <p class="mt-2 text-sm text-red-100">Preencha seus dados para solicitar a participação.</p>
                    </div>
                </div>
            </header>

            @if ($enviado)
                <section class="space-y-6 p-6 text-center sm:p-10">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-3xl text-green-700">✓</div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Inscrição enviada com sucesso</h2>
                        <p class="mt-2 text-slate-600">Sua solicitação será analisada pela equipe da UNP.</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Protocolo</p>
                        <p class="mt-1 break-all font-mono text-sm font-bold text-slate-800">{{ $protocolo }}</p>
                    </div>
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-3 font-bold text-white transition hover:bg-green-700">
                        Entrar no grupo do WhatsApp
                    </a>
                    <p class="text-xs text-slate-500">Ao abrir o WhatsApp, confirme a entrada no grupo.</p>
                </section>
            @else
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-center justify-between text-sm font-semibold text-slate-600">
                        <span>Etapa {{ $step }} de {{ $totalSteps }}</span>
                        <span>{{ (int) (($step / $totalSteps) * 100) }}%</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-red-700 transition-all" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                    </div>
                </div>

                <form wire:submit.prevent="{{ $step === $totalSteps ? 'submit' : 'nextStep' }}" class="p-6 sm:p-8">
                    @if (session()->has('error'))
                        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
                    @endif

                    @if ($step === 1)
                        <div class="space-y-5">
                            <h2 class="text-xl font-bold text-slate-900">Privacidade e autorização</h2>
                            <p class="text-sm leading-6 text-slate-600">
                                Seus dados e sua foto serão utilizados pela UNP exclusivamente para analisar sua inscrição,
                                organizar as turmas e acompanhar sua participação no curso.
                            </p>
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4">
                                <input type="checkbox" wire:model="lgpd_aceito" class="mt-1 rounded border-slate-300 text-red-700 focus:ring-red-600">
                                <span class="text-sm text-slate-700">Autorizo o tratamento dos meus dados para esta finalidade.</span>
                            </label>
                            @error('lgpd_aceito') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @elseif ($step === 2)
                        <div class="space-y-5">
                            <h2 class="text-xl font-bold text-slate-900">Igreja</h2>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Bloco</label>
                                    <select wire:model.live="bloco_id" class="mt-1 w-full rounded-lg border-slate-300">
                                        <option value="">Selecione</option>
                                        @foreach ($blocos as $bloco)<option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>@endforeach
                                    </select>
                                    @error('bloco_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Região</label>
                                    <select wire:model.live="regiao_id" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$bloco_id)>
                                        <option value="">Selecione</option>
                                        @foreach ($regioes as $regiao)<option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>@endforeach
                                    </select>
                                    @error('regiao_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="text-sm font-semibold text-slate-700">Igreja</label>
                                    <select wire:model="igreja_id" class="mt-1 w-full rounded-lg border-slate-300" @disabled(!$regiao_id)>
                                        <option value="">Selecione</option>
                                        @foreach ($igrejas as $igreja)<option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>@endforeach
                                    </select>
                                    @error('igreja_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @elseif ($step === 3)
                        <div class="space-y-5">
                            <h2 class="text-xl font-bold text-slate-900">Identificação</h2>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Nome completo</label>
                                <input type="text" wire:model="nome" autocomplete="name" class="mt-1 w-full rounded-lg border-slate-300">
                                @error('nome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Celular com DDD</label>
                                <input type="tel" wire:model="celular" autocomplete="tel" inputmode="tel" class="mt-1 w-full rounded-lg border-slate-300">
                                @error('celular') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div wire:key="curso-unp-foto">
                                <label class="text-sm font-semibold text-slate-700">Foto</label>
                                <input type="file" wire:model="foto" accept="image/jpeg,image/png"
                                    class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-red-50 file:px-4 file:py-2 file:font-semibold file:text-red-700">
                                <p class="mt-1 text-xs text-slate-500">Envie uma foto nítida, de frente, em JPG ou PNG.</p>
                                <div wire:loading wire:target="foto" class="mt-2 text-sm text-slate-500">Carregando foto...</div>
                                @if ($foto)<img src="{{ $foto->temporaryUrl() }}" alt="Pré-visualização" class="mt-3 h-28 w-28 rounded-full object-cover shadow">@endif
                                @error('foto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @elseif ($step === 4)
                        <div class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-900">Vida espiritual</h2>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">É batizado nas águas?</label>
                                    <select wire:model.live="batizado_aguas" class="mt-1 w-full rounded-lg border-slate-300">
                                        <option value="0">Não</option><option value="1">Sim</option>
                                    </select>
                                </div>
                                @if ($batizado_aguas)
                                    <div>
                                        <label class="text-sm font-semibold text-slate-700">Data do batismo nas águas</label>
                                        <input type="date" wire:model="data_batismo_aguas" class="mt-1 w-full rounded-lg border-slate-300">
                                        @error('data_batismo_aguas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">É batizado no Espírito Santo?</label>
                                    <select wire:model.live="batizado_espirito_santo" class="mt-1 w-full rounded-lg border-slate-300">
                                        <option value="0">Não</option><option value="1">Sim</option>
                                    </select>
                                </div>
                                @if ($batizado_espirito_santo)
                                    <div>
                                        <label class="text-sm font-semibold text-slate-700">Data do batismo no Espírito Santo</label>
                                        <input type="date" wire:model="data_batismo_espirito_santo" class="mt-1 w-full rounded-lg border-slate-300">
                                        @error('data_batismo_espirito_santo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Mês em que entrou na igreja</label>
                                    <select wire:model="mes_ingresso_igreja" class="mt-1 w-full rounded-lg border-slate-300">
                                        <option value="">Selecione</option>
                                        @foreach ([1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'] as $numero => $mes)
                                            <option value="{{ $numero }}">{{ $mes }}</option>
                                        @endforeach
                                    </select>
                                    @error('mes_ingresso_igreja') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Ano em que entrou na igreja</label>
                                    <input type="number" wire:model="ano_ingresso_igreja" min="1900" max="{{ now()->year }}" inputmode="numeric" class="mt-1 w-full rounded-lg border-slate-300">
                                    @error('ano_ingresso_igreja') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @elseif ($step === 5)
                        <div class="space-y-6">
                            <h2 class="text-xl font-bold text-slate-900">Estado civil e endereço</h2>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Situação atual</label>
                                <select wire:model.live="estado_civil" class="mt-1 w-full rounded-lg border-slate-300">
                                    <option value="">Selecione</option>
                                    <option value="solteiro">Solteiro(a)</option>
                                    <option value="casado">Casado(a)</option>
                                    <option value="viuvo">Viúvo(a)</option>
                                    <option value="divorciado">Divorciado(a)</option>
                                    <option value="namorando">Namorando</option>
                                </select>
                                @error('estado_civil') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @if ($estado_civil === 'casado')
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-4">
                                        <input type="checkbox" wire:model="casado_civil" class="rounded border-slate-300 text-red-700">
                                        <span class="text-sm text-slate-700">Casado no civil</span>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-4">
                                        <input type="checkbox" wire:model="casado_igreja" class="rounded border-slate-300 text-red-700">
                                        <span class="text-sm text-slate-700">Casado na igreja</span>
                                    </label>
                                </div>
                            @endif
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Endereço completo</label>
                                <textarea wire:model="endereco_completo" rows="4" maxlength="1000"
                                    placeholder="Rua, número, complemento, bairro, cidade, estado e CEP"
                                    class="mt-1 w-full rounded-lg border-slate-300"></textarea>
                                @error('endereco_completo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                                Revise os dados antes de enviar. Após o envio, a equipe da UNP analisará sua inscrição.
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex items-center justify-between gap-4 border-t border-slate-200 pt-6">
                        @if ($step > 1)
                            <button type="button" wire:click="previousStep" class="rounded-lg border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">Voltar</button>
                        @else
                            <span></span>
                        @endif
                        <button type="submit" wire:loading.attr="disabled"
                            class="rounded-lg bg-red-700 px-6 py-2.5 font-bold text-white hover:bg-red-800 disabled:opacity-50">
                            {{ $step === $totalSteps ? 'Enviar inscrição' : 'Continuar' }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
