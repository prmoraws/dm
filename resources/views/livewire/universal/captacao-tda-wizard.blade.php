<div class="min-h-screen bg-gray-100 py-6 px-3">
    <div class="mx-auto max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl">
        <header class="bg-red-700 px-6 py-6 text-white">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/tda/tda-logo.svg') }}" alt="TDA" class="h-14 w-28 shrink-0 rounded bg-white p-1 object-contain">
                <div><p class="text-sm font-semibold uppercase tracking-widest">Auxiliares da Terapia do Amor</p><h1 class="mt-1 text-2xl font-bold">Ficha de Cadastro TDA</h1></div>
            </div>
            @unless($enviado)
                <div class="mt-5 h-2 overflow-hidden rounded-full bg-red-950/40">
                    <div class="h-full bg-white transition-all" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                </div>
                <p class="mt-2 text-sm">Etapa {{ $step }} de {{ $totalSteps }}</p>
            @endunless
        </header>

        <main class="p-5 sm:p-8">
            @if (session('error'))
                <div class="mb-5 rounded-lg bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
            @endif

            @if ($enviado)
                <section class="py-12 text-center">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-3xl text-green-700">✓</div>
                    <h2 class="text-2xl font-bold text-gray-900">Cadastro enviado com sucesso!</h2>
                    <p class="mx-auto mt-3 max-w-xl text-gray-600">Sua ficha foi recebida e ficará pendente para análise da equipe responsável.</p>
                </section>
            @else
                <form wire:submit.prevent="submit" class="space-y-6">
                    <div wire:key="tda-step-{{ $step }}" class="space-y-6">
                    @if ($step === 1)
                        <x-tda-title titulo="Privacidade e consentimento" descricao="Leia as informações antes de iniciar." />
                        <div class="max-h-72 overflow-y-auto rounded-xl border bg-gray-50 p-5 text-sm leading-6 text-gray-700">
                            Ao preencher esta ficha, você confirma o consentimento para o cadastro como voluntário do grupo Auxiliares da Terapia do Amor da Igreja Universal do Reino de Deus. Os dados serão utilizados para gerenciar os voluntários e possibilitar contatos sobre as atividades realizadas. Você poderá solicitar acesso, retificação, exclusão, limitação, portabilidade ou revogação do consentimento. Consulte a Política de Privacidade em <a class="font-semibold text-red-700 underline" href="https://www.universal.org/politica-de-privacidade/" target="_blank" rel="noopener noreferrer">universal.org/politica-de-privacidade</a>.
                        </div>
                        <label class="flex items-start gap-3 rounded-xl border p-4">
                            <input type="checkbox" wire:model="lgpd_aceito" class="mt-1 rounded border-gray-300 text-red-700 focus:ring-red-600">
                            <span class="text-sm text-gray-700">Li e concordo com a Política de Privacidade e com o tratamento dos dados para as finalidades informadas.</span>
                        </label>
                        @error('lgpd_aceito') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    @elseif ($step === 2)
                        <x-tda-title titulo="Igreja e grupo" descricao="Informe onde você participa do grupo." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-tda-select campo="bloco_id" rotulo="Bloco" obrigatorio>
                                <option value="">Selecione</option>@foreach($allBlocos as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                            </x-tda-select>
                            <x-tda-select campo="regiao_id" rotulo="Região" obrigatorio>
                                <option value="">Selecione</option>@foreach($regiaos as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                            </x-tda-select>
                            <x-tda-select campo="igreja_id" rotulo="Igreja" obrigatorio>
                                <option value="">Selecione</option>@foreach($igrejas as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach
                            </x-tda-select>
                            <div class="sm:col-span-2"><x-tda-input campo="endereco_igreja" rotulo="Endereço completo da igreja" obrigatorio /></div>
                            <x-tda-input campo="data_ingresso_grupo" rotulo="Data de ingresso no grupo" tipo="date" />
                            <div class="sm:col-span-2">
                                <x-tda-select campo="funcao_grupo" rotulo="Função no grupo" obrigatorio>
                                    <option value="">Selecione</option>
                                    <option value="Membro">Membro</option>
                                    <option value="Secretaria">Secretaria</option>
                                    <option value="Mídia">Mídia</option>
                                    <option value="Obreiro">Obreiro</option>
                                </x-tda-select>
                            </div>
                        </div>
                    @elseif ($step === 3)
                        <x-tda-title titulo="Dados pessoais" descricao="Preencha os seus dados de identificação." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2"><x-tda-input campo="nome" rotulo="Nome completo" obrigatorio /></div>
                            <x-tda-input campo="nacionalidade" rotulo="Nacionalidade" obrigatorio />
                            <x-tda-input campo="data_nascimento" rotulo="Data de nascimento" tipo="date" obrigatorio />
                            <x-tda-select campo="estado_civil" rotulo="Estado civil" obrigatorio><option value="">Selecione</option><option value="solteiro">Solteiro(a)</option><option value="casado">Casado(a)</option><option value="divorciado">Divorciado(a)</option><option value="viuvo">Viúvo(a)</option><option value="uniao_estavel">União estável</option></x-tda-select>
                            <x-tda-input campo="rg" rotulo="RG/CIN" />
                            <x-tda-input campo="cpf" rotulo="CPF" obrigatorio placeholder="Somente números ou com pontuação" />
                            <x-tda-select campo="sexo" rotulo="Sexo" obrigatorio><option value="">Selecione</option><option value="feminino">Feminino</option><option value="masculino">Masculino</option></x-tda-select>
                            <div wire:key="tda-foto-upload">
                                <label for="foto" class="mb-1 block text-sm font-semibold text-gray-700">Foto <span class="text-red-600">*</span></label>
                                <input id="foto" type="file" required wire:model="foto" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-red-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-red-700 hover:file:bg-red-100">
                                <div wire:loading wire:target="foto" class="mt-2 text-sm text-gray-500">Carregando pré-visualização...</div>
                                @if ($foto)
                                    <img src="{{ $foto->temporaryUrl() }}" alt="Pré-visualização da foto" class="mt-3 h-24 w-24 rounded-full border-2 border-white object-cover shadow-md">
                                @endif
                                @error('foto')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @elseif ($step === 4)
                        <x-tda-title titulo="Contato e endereço" descricao="Informe seus meios de contato e residência." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-tda-input campo="celular" rotulo="Celular/WhatsApp" obrigatorio />
                            <x-tda-input campo="email" rotulo="E-mail" tipo="email" />
                            <x-tda-input campo="facebook" rotulo="Facebook" />
                            <x-tda-input campo="instagram" rotulo="Instagram" />
                            <div class="sm:col-span-2"><x-tda-input campo="endereco" rotulo="Endereço" obrigatorio /></div>
                            <x-tda-input campo="numero" rotulo="Número" obrigatorio />
                            <x-tda-input campo="complemento" rotulo="Complemento" />
                            <x-tda-input campo="cep" rotulo="CEP" />
                            <x-tda-input campo="bairro" rotulo="Bairro" obrigatorio />
                            <x-tda-select campo="estado_id" rotulo="Estado" obrigatorio><option value="">Selecione</option>@foreach($allEstados as $item)<option value="{{ $item->id }}">{{ $item->nome }} ({{ $item->uf }})</option>@endforeach</x-tda-select>
                            <x-tda-select campo="cidade_id" rotulo="Cidade" obrigatorio><option value="">Selecione</option>@foreach($cidades as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach</x-tda-select>
                        </div>
                    @elseif ($step === 5)
                        <x-tda-title titulo="Família e emergência" descricao="Complete os dados pessoais e o contato de emergência." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-tda-input campo="escolaridade" rotulo="Grau de escolaridade" obrigatorio />
                            <x-tda-input campo="profissao" rotulo="Profissão" />
                            <x-tda-radio-sim-nao campo="tem_filhos" rotulo="Tem filhos?" />
                            @if($tem_filhos)<x-tda-input campo="quantidade_filhos" rotulo="Quantidade de filhos" tipo="number" obrigatorio /><x-tda-input campo="idade_filhos" rotulo="Idades dos filhos" obrigatorio placeholder="Ex.: 5, 9 e 14 anos" />@endif
                            <div class="sm:col-span-2 border-t pt-5"><h3 class="font-bold text-gray-800">Contato de emergência</h3></div>
                            <x-tda-input campo="emergencia_nome" rotulo="Nome" obrigatorio />
                            <x-tda-input campo="emergencia_celular" rotulo="Celular/WhatsApp" obrigatorio />
                            <x-tda-input campo="emergencia_facebook" rotulo="Facebook" />
                            <x-tda-input campo="emergencia_instagram" rotulo="Instagram" />
                        </div>
                    @elseif ($step === 6)
                        <x-tda-title titulo="Dados espirituais" descricao="Informe sua situação e trajetória na IURD." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-tda-select campo="condicao_atual" rotulo="Condição atual" obrigatorio><option value="">Selecione</option>@foreach(['membro'=>'Membro','cpo'=>'CPO','colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita','auxiliar'=>'Auxiliar'] as $valor=>$rotulo)<option value="{{ $valor }}">{{ $rotulo }}</option>@endforeach</x-tda-select>
                            <x-tda-input campo="inicio_iurd" rotulo="Início na IURD" tipo="date" obrigatorio />
                            <x-tda-radio-sim-nao campo="batizado_aguas" rotulo="Batizado nas águas na IURD?" />
                            @if($batizado_aguas)<x-tda-input campo="data_batismo_aguas" rotulo="Data do batismo nas águas" tipo="date" obrigatorio />@endif
                            <x-tda-radio-sim-nao campo="batizado_espirito_santo" rotulo="Batizado com o Espírito Santo?" />
                            @if($batizado_espirito_santo)<x-tda-input campo="data_batismo_espirito_santo" rotulo="Data do batismo com o Espírito Santo" tipo="date" obrigatorio />@endif
                            <x-tda-radio-sim-nao campo="ja_se_afastou" rotulo="Já se afastou?" />
                        </div>
                    @elseif ($step === 7)
                        <x-tda-title titulo="Participação semanal" descricao="Marque todos os dias correspondentes." />
                        <x-tda-dias campo="dias_reunioes" rotulo="Em quais dias participa das reuniões da igreja?" />
                        <x-tda-dias campo="dias_evangelizacao" rotulo="Em quais dias evangeliza?" />
                    @elseif ($step === 8)
                        <x-tda-title titulo="Atividades e graduações" descricao="Preencha as informações que correspondem à sua situação." />
                        @if($sexo === 'feminino')
                            <div class="grid gap-5 sm:grid-cols-2"><x-tda-radio-sim-nao campo="godllywood_autoajuda" rotulo="Participa do Godllywood Autoajuda?" /><x-tda-radio-sim-nao campo="meditacao_univer" rotulo="Assiste à meditação da Palavra no Univer?" /></div>
                        @else
                            <div class="grid gap-5 sm:grid-cols-2"><x-tda-radio-sim-nao campo="intellimen_reunioes" rotulo="Participa das reuniões do IntelliMen?" /><x-tda-radio-sim-nao campo="intellimen_desafios" rotulo="Já fez ou faz os desafios do IntelliMen?" /></div>
                        @endif
                        <div class="mt-6 space-y-4 border-t pt-5">
                            @foreach(['colaborador'=>'Colaborador','obreiro'=>'Obreiro','levita'=>'Levita'] as $campo=>$rotulo)
                                <div class="grid items-end gap-4 sm:grid-cols-2"><label class="flex items-center gap-3 rounded-lg border p-3"><input type="checkbox" wire:model.live="{{ $campo }}" class="rounded text-red-700 focus:ring-red-600"><span>{{ $rotulo }}</span></label>@if($$campo)<x-tda-input campo="data_graduacao_{{ $campo }}" rotulo="Data da graduação" tipo="date" obrigatorio />@endif</div>
                            @endforeach
                        </div>
                        <div class="mt-6"><x-tda-dias campo="dias_trabalho_reuniao" rotulo="Em quais dias trabalha na reunião?" opcional /></div>
                    @elseif ($step === 9)
                        <x-tda-termo-documento tipo="adesao_servico_voluntario" :aceito="$aceite_adesao" />
                    @elseif ($step === 10)
                        <x-tda-termo-documento tipo="cessao_imagem_voz" :aceito="$aceite_imagem_voz" />
                        @if($this->menorDeIdade())
                            <section class="space-y-5 rounded-xl border border-amber-300 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950/30">
                                <div><h3 class="font-bold text-gray-900 dark:text-white">Responsável legal</h3><p class="text-sm text-gray-600 dark:text-gray-300">Obrigatório porque o participante tem menos de 18 anos.</p></div>
                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2"><x-tda-input campo="responsavel_nome" rotulo="Nome completo" obrigatorio /></div>
                                    <x-tda-input campo="responsavel_nacionalidade" rotulo="Nacionalidade" obrigatorio />
                                    <x-tda-select campo="responsavel_estado_civil" rotulo="Estado civil" obrigatorio>
                                        <option value="">Selecione</option>
                                        <option value="solteiro">Solteiro(a)</option>
                                        <option value="casado">Casado(a)</option>
                                        <option value="divorciado">Divorciado(a)</option>
                                        <option value="viuvo">Viúvo(a)</option>
                                        <option value="uniao_estavel">União estável</option>
                                    </x-tda-select>
                                    <x-tda-input campo="responsavel_profissao" rotulo="Profissão" obrigatorio />
                                    <x-tda-input campo="responsavel_data_nascimento" rotulo="Data de nascimento" tipo="date" obrigatorio />
                                    <x-tda-input campo="responsavel_rg" rotulo="RG/CIN" obrigatorio />
                                    <x-tda-input campo="responsavel_cpf" rotulo="CPF" obrigatorio />
                                    <div class="sm:col-span-2"><x-tda-input campo="responsavel_endereco" rotulo="Endereço" obrigatorio /></div>
                                    <x-tda-input campo="responsavel_numero" rotulo="Número" obrigatorio />
                                    <x-tda-input campo="responsavel_complemento" rotulo="Complemento" />
                                    <x-tda-input campo="responsavel_bairro" rotulo="Bairro" obrigatorio />
                                    <x-tda-input campo="responsavel_cep" rotulo="CEP" obrigatorio />
                                    <x-tda-select campo="responsavel_estado_id" rotulo="Estado" obrigatorio><option value="">Selecione</option>@foreach($allEstados as $item)<option value="{{ $item->id }}">{{ $item->nome }} ({{ $item->uf }})</option>@endforeach</x-tda-select>
                                    <x-tda-select campo="responsavel_cidade_id" rotulo="Cidade" obrigatorio><option value="">Selecione</option>@foreach($responsavelCidades as $item)<option value="{{ $item->id }}">{{ $item->nome }}</option>@endforeach</x-tda-select>
                                </div>
                            </section>
                        @endif
                    @elseif ($step === 11)
                        <x-tda-termo-documento tipo="utilizacao_uniforme" :aceito="$aceite_uniforme" />
                    @elseif ($step === 12)
                        <x-tda-title titulo="Assinatura dos termos" descricao="Assine uma vez para confirmar os três documentos aceitos." />
                        <div x-data="{
                            desenhando: false,
                            ctx: null,
                            iniciar() {
                                const c = this.$refs.canvas;
                                const escala = window.devicePixelRatio || 1;
                                c.width = c.clientWidth * escala; c.height = c.clientHeight * escala;
                                this.ctx = c.getContext('2d'); this.ctx.scale(escala, escala);
                                this.ctx.lineWidth = 2.5; this.ctx.lineCap = 'round'; this.ctx.strokeStyle = '#111827';
                            },
                            ponto(e) { const r=this.$refs.canvas.getBoundingClientRect(); const p=e.touches?.[0] || e; return [p.clientX-r.left,p.clientY-r.top]; },
                            comecar(e) { e.preventDefault(); this.desenhando=true; const [x,y]=this.ponto(e); this.ctx.beginPath(); this.ctx.moveTo(x,y); },
                            mover(e) { if(!this.desenhando) return; e.preventDefault(); const [x,y]=this.ponto(e); this.ctx.lineTo(x,y); this.ctx.stroke(); },
                            terminar() { if(!this.desenhando) return; this.desenhando=false; $wire.set('assinatura', this.$refs.canvas.toDataURL('image/png')); },
                            limpar() { this.ctx.clearRect(0,0,this.$refs.canvas.width,this.$refs.canvas.height); $wire.set('assinatura', null); }
                        }" x-init="iniciar()" class="space-y-3">
                            <div class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-white dark:border-gray-600">
                                <canvas x-ref="canvas" @pointerdown="comecar" @pointermove="mover" @pointerup="terminar" @pointercancel="terminar" @pointerleave="terminar" class="h-64 w-full touch-none cursor-crosshair" aria-label="Área para assinatura"></canvas>
                            </div>
                            <div class="flex items-center justify-between gap-3"><p class="text-sm text-gray-600 dark:text-gray-300">Use o dedo, mouse ou caneta digital.</p><button type="button" @click="limpar" class="rounded-lg border px-4 py-2 text-sm font-semibold dark:border-gray-600 dark:text-gray-200">Limpar</button></div>
                            @error('assinatura')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 text-sm dark:bg-gray-800 dark:text-gray-200">
                            Responsável pela igreja: <strong>{{ config('tda.pastor_responsavel.nome') }}</strong>
                        </div>
                    @elseif ($step === 13)
                        <x-tda-title titulo="Testemunha do Termo de Adesão" descricao="A testemunha deve informar seus dados e assinar abaixo." />
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-tda-input campo="testemunha_nome" rotulo="Nome completo da testemunha" obrigatorio />
                            <x-tda-input campo="testemunha_rg" rotulo="RG/CIN da testemunha" obrigatorio />
                        </div>
                        <div x-data="{
                            desenhando: false, ctx: null,
                            iniciar() {
                                const c=this.$refs.canvas; const escala=window.devicePixelRatio||1;
                                c.width=c.clientWidth*escala; c.height=c.clientHeight*escala;
                                this.ctx=c.getContext('2d'); this.ctx.scale(escala,escala);
                                this.ctx.lineWidth=2.5; this.ctx.lineCap='round'; this.ctx.strokeStyle='#111827';
                            },
                            ponto(e){const r=this.$refs.canvas.getBoundingClientRect();const p=e.touches?.[0]||e;return[p.clientX-r.left,p.clientY-r.top]},
                            comecar(e){e.preventDefault();this.desenhando=true;const[x,y]=this.ponto(e);this.ctx.beginPath();this.ctx.moveTo(x,y)},
                            mover(e){if(!this.desenhando)return;e.preventDefault();const[x,y]=this.ponto(e);this.ctx.lineTo(x,y);this.ctx.stroke()},
                            terminar(){if(!this.desenhando)return;this.desenhando=false;$wire.set('testemunha_assinatura',this.$refs.canvas.toDataURL('image/png'))},
                            limpar(){this.ctx.clearRect(0,0,this.$refs.canvas.width,this.$refs.canvas.height);$wire.set('testemunha_assinatura',null)}
                        }" x-init="iniciar()" class="space-y-3">
                            <div class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-white">
                                <canvas x-ref="canvas" @pointerdown="comecar" @pointermove="mover" @pointerup="terminar" @pointercancel="terminar" @pointerleave="terminar" class="h-64 w-full touch-none cursor-crosshair" aria-label="Área para assinatura da testemunha"></canvas>
                            </div>
                            <div class="flex items-center justify-between gap-3"><p class="text-sm text-gray-600">A testemunha deve assinar usando o dedo, mouse ou caneta digital.</p><button type="button" @click="limpar" class="rounded-lg border px-4 py-2 text-sm font-semibold">Limpar</button></div>
                            @error('testemunha_assinatura')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @elseif ($step === 14)
                        <x-tda-title titulo="Revisão e envio" descricao="Confira os dados principais antes de enviar." />
                        <dl class="grid gap-4 rounded-xl bg-gray-50 p-5 sm:grid-cols-2">
                            <div><dt class="text-xs uppercase text-gray-500">Nome</dt><dd class="font-semibold">{{ $nome }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Celular</dt><dd class="font-semibold">{{ $celular }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">CPF</dt><dd class="font-semibold">{{ $cpf }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Condição</dt><dd class="font-semibold">{{ ucfirst($condicao_atual) }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Bloco</dt><dd class="font-semibold">{{ optional($allBlocos->find($bloco_id))->nome }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Igreja</dt><dd class="font-semibold">{{ optional(collect($igrejas)->firstWhere('id', (int)$igreja_id))->nome }}</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Termos</dt><dd class="font-semibold text-green-700">3 aceitos e assinados</dd></div>
                            <div><dt class="text-xs uppercase text-gray-500">Testemunha</dt><dd class="font-semibold">{{ $testemunha_nome }} — RG/CIN {{ $testemunha_rg }}</dd></div>
                        </dl>
                        @error('cpf')<p class="rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>@enderror
                    @endif
                    </div>

                    <div class="sticky bottom-0 z-20 -mx-5 flex items-center justify-between gap-3 border-t bg-white/95 px-5 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.08)] backdrop-blur sm:-mx-8 sm:px-8">
                        @if($step > 1)<button type="button" wire:click="previousStep" wire:loading.attr="disabled" class="rounded-lg border px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">Voltar</button>@else<span></span>@endif
                        @if(in_array($step, [9, 10, 11], true))
                            @php($tipoTermo = [9 => 'adesao_servico_voluntario', 10 => 'cessao_imagem_voz', 11 => 'utilizacao_uniforme'][$step])
                            <button type="button" wire:click="aceitarTermo('{{ $tipoTermo }}')" wire:loading.attr="disabled" class="rounded-lg bg-green-700 px-6 py-3 font-bold text-white shadow hover:bg-green-800 disabled:opacity-50">Li e aceito este termo</button>
                        @elseif($step < $totalSteps)
                            <button type="button" wire:click="nextStep" wire:loading.attr="disabled" class="rounded-lg bg-red-700 px-6 py-3 font-semibold text-white hover:bg-red-800">Avançar</button>
                        @else
                            <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="min-w-[180px] rounded-lg bg-green-700 px-6 py-3 font-bold text-white shadow-lg hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:opacity-50"><span wire:loading.remove wire:target="submit">Enviar cadastro</span><span wire:loading wire:target="submit">Enviando...</span></button>
                        @endif
                    </div>
                </form>
            @endif
        </main>
    </div>
</div>
