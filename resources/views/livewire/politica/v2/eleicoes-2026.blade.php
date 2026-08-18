@section('title', 'Política - Eleições 2026')
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Eleições 2026</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registros oficiais importados do TSE, acompanhamento e preparação para a apuração.</p>
            </div>
            <div class="grid w-full gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.dados-oficiais') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Dados oficiais TSE</a>
                <a href="{{ route('politica.acompanhamento') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Acompanhamento</a>
            </div>
        </div>
    </x-slot>

    @php
        $toneClasses = [
            'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
            'amber' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300',
            'slate' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        ];
    @endphp

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 via-white to-blue-50 p-5 shadow-sm dark:border-indigo-900 dark:from-indigo-950/35 dark:via-gray-900 dark:to-blue-950/25 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-700 dark:text-indigo-300">Painel oficial</p>
                        <h1 class="mt-2 text-2xl font-bold text-gray-950 dark:text-white sm:text-3xl">Eleições 2026</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">Este painel não consulta fontes externas durante a navegação. Ele exibe somente o que já foi sincronizado para o banco local, preservando origem, situação e horário da última atualização.</p>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2 lg:min-w-[330px]">
                        <div class="rounded-xl bg-white/80 px-4 py-3 dark:bg-gray-900/65">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Última sincronização</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $registro['ultima_sincronizacao']?->format('d/m/Y H:i') ?? 'Ainda não sincronizado' }}</p>
                        </div>
                        <div class="rounded-xl bg-white/80 px-4 py-3 dark:bg-gray-900/65">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fonte TSE</p>
                            <p class="mt-1 font-semibold {{ ($fonte['erros_consecutivos'] ?? 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $fonte ? (($fonte['http_status'] ?? 'local').' · '.(($fonte['erros_consecutivos'] ?? 0) > 0 ? 'atenção' : 'saudável')) : 'Ainda não registrada' }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 rounded-xl border px-4 py-3 text-sm {{ $registro['antes_prazo'] ? 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200' : 'border-blue-200 bg-blue-50 text-blue-900 dark:border-blue-900 dark:bg-blue-950/30 dark:text-blue-200' }}">
                    @if ($registro['antes_prazo'])
                        A base ainda está dentro do prazo formal de registros, até {{ $registro['prazo']->format('d/m/Y H:i') }}. Novas sincronizações podem alterar quantidade e situação das candidaturas.
                    @else
                        O prazo formal de registros encerrou. A Justiça Eleitoral ainda pode processar, substituir ou atualizar situações; o painel continuará refletindo a última sincronização disponível.
                    @endif
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Candidaturas oficiais', 'value' => $metricas['candidaturas'], 'hint' => 'TSE 2026 no banco'],
                    ['label' => 'Acompanhadas', 'value' => $metricas['acompanhadas'], 'hint' => 'Prioridade também localizada'],
                    ['label' => 'Partidos', 'value' => $metricas['partidos'], 'hint' => 'No recorte importado'],
                    ['label' => 'Cargos', 'value' => $metricas['cargos'], 'hint' => 'Com registros oficiais'],
                ] as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            @foreach ([['titulo' => 'Presidência', 'bloco' => $presidencia], ['titulo' => 'Governo da Bahia', 'bloco' => $governo]] as $secao)
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $secao['titulo'] }}</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $secao['bloco']['descricao'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $secao['bloco']['total'] }} registro(s)</span>
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">{{ $secao['bloco']['partidos'] }} partido(s)</span>
                            @if ($secao['bloco']['acompanhados'] > 0)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $secao['bloco']['acompanhados'] }} acompanhado(s)</span>
                            @endif
                        </div>
                    </div>
                    <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse ($secao['bloco']['candidatos'] as $candidato)
                            <article class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if ($candidato['foto_url'])
                                            <img src="{{ $candidato['foto_url'] }}" alt="Foto oficial de {{ $candidato['nome_urna'] ?: $candidato['politico'] }}" class="h-12 w-12 shrink-0 rounded-xl object-cover ring-1 ring-black/5 dark:ring-white/10">
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-gray-950 dark:text-white">{{ $candidato['nome_urna'] ?: $candidato['politico'] }}</p>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $candidato['partido'] ?: 'Sem partido' }} @if ($candidato['numero']) · Nº {{ $candidato['numero'] }} @endif</p>
                                        </div>
                                    </div>
                                    @if ($candidato['acompanhado'])
                                        <span class="shrink-0 rounded-full bg-indigo-600 px-2.5 py-1 text-[11px] font-bold text-white">ACOMPANHADO</span>
                                    @endif
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $toneClasses[$candidato['situacao_tom']] ?? $toneClasses['slate'] }}">{{ $candidato['situacao'] }}</span>
                                    @if ($candidato['situacao_codigo'])
                                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-mono text-gray-500 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700">{{ $candidato['situacao_codigo'] }}</span>
                                    @endif
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-gray-200 pt-3 text-xs dark:border-gray-700">
                                    <span class="text-gray-500 dark:text-gray-400">SQ {{ $candidato['tse_sq_candidato'] ?: '—' }}</span>
                                    @if ($candidato['slug'])
                                        <a href="{{ route('politica.politicos.show', $candidato['slug']) }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Abrir perfil →</a>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhum registro oficial deste cargo foi importado para 2026.</div>
                        @endforelse
                    </div>
                </section>
            @endforeach

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-1">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senado · Bahia</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $senado['descricao'] }}</p>
                    </div>
                    <div class="space-y-3 p-4">
                        @forelse ($senado['candidatos'] as $candidato)
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if ($candidato['foto_url'])
                                            <img src="{{ $candidato['foto_url'] }}" alt="Foto oficial de {{ $candidato['nome_urna'] ?: $candidato['politico'] }}" class="h-10 w-10 shrink-0 rounded-lg object-cover ring-1 ring-black/5 dark:ring-white/10">
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-gray-900 dark:text-white">{{ $candidato['nome_urna'] ?: $candidato['politico'] }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $candidato['partido'] }} @if ($candidato['numero']) · Nº {{ $candidato['numero'] }} @endif</p>
                                        </div>
                                    </div>
                                    @if ($candidato['acompanhado'])
                                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-[10px] font-bold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">ACOMPANHADO</span>
                                    @endif
                                </div>
                                <p class="mt-3 text-xs text-gray-600 dark:text-gray-300">{{ $candidato['situacao'] }}</p>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhuma candidatura do recorte foi importada para Senado.</p>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                    <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $republicanos['partido'] }} · Bahia</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recorte econômico oficial para Senado, Câmara Federal e Assembleia Legislativa.</p>
                        </div>
                        <span class="w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">{{ $republicanos['total'] }} candidatura(s)</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($republicanos['grupos'] as $grupo)
                            <details class="group p-4" @if ($loop->first) open @endif>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-xl px-1 py-2">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $grupo['cargo'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $grupo['total'] }} candidatura(s) @if ($grupo['acompanhados']) · {{ $grupo['acompanhados'] }} acompanhada(s) @endif</p>
                                    </div>
                                    <span class="text-sm font-semibold text-indigo-600 group-open:rotate-180 dark:text-indigo-400">⌄</span>
                                </summary>
                                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                    @foreach ($grupo['candidatos'] as $candidato)
                                        <div class="rounded-xl bg-gray-50 px-3 py-3 dark:bg-gray-900/50">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex min-w-0 items-center gap-2.5">
                                                    @if ($candidato['foto_url'])
                                                        <img src="{{ $candidato['foto_url'] }}" alt="Foto oficial de {{ $candidato['nome_urna'] ?: $candidato['politico'] }}" class="h-9 w-9 shrink-0 rounded-lg object-cover ring-1 ring-black/5 dark:ring-white/10">
                                                    @endif
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $candidato['nome_urna'] ?: $candidato['politico'] }}</p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nº {{ $candidato['numero'] ?: '—' }} · {{ $candidato['situacao'] }}</p>
                                                    </div>
                                                </div>
                                                @if ($candidato['slug'])
                                                    <a href="{{ route('politica.politicos.show', $candidato['slug']) }}" wire:navigate class="shrink-0 text-xs font-semibold text-indigo-600 dark:text-indigo-400">Perfil →</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma candidatura {{ $republicanos['partido'] }} foi importada para os cargos legislativos de 2026.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Preparação para apuração</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">A mesma tela poderá receber os snapshots da apuração quando houver dados oficiais disponíveis.</p>
                        </div>
                        <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">Sem polling no navegador</span>
                    </div>
                    <div class="mt-4 rounded-xl border px-4 py-3 text-sm {{ $coletor['live_enabled'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/30 dark:text-emerald-200' : 'border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-300' }}">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <span class="font-semibold">Coletor TSE: {{ $coletor['live_enabled'] ? 'habilitado' : 'protegido / desligado' }}</span>
                            <span class="text-xs">mín. {{ $coletor['poll_seconds'] }} s · máx. {{ $coletor['max_requests_per_cycle'] }} requisições lógicas/ciclo</span>
                        </div>
                        @unless ($coletor['live_enabled'])
                            <p class="mt-1 text-xs opacity-80">Nenhum acesso ao ambiente de resultados é iniciado pelo painel. A habilitação ao vivo exige configuração explícita no servidor.</p>
                        @endunless
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach (['Presidente', 'Governador', 'Senador', 'Deputado Federal'] as $cargo)
                            @php $statusApuracao = $apuracao[$cargo] ?? null; @endphp
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $cargo }}</p>
                                @if ($statusApuracao)
                                    <p class="mt-2 text-sm text-indigo-600 dark:text-indigo-400">{{ $statusApuracao['status'] ?: 'Em atualização' }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $statusApuracao['percentual_secoes'] !== null ? number_format($statusApuracao['percentual_secoes'], 2, ',', '.').'%' : 'Percentual ainda não informado' }}</p>
                                @else
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Aguardando início da apuração.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="rounded-2xl bg-gray-950 p-5 text-white shadow-sm dark:bg-black">
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-300">Princípio do painel</p>
                    <h3 class="mt-2 font-semibold">Registro não é resultado</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-300">Acompanhamento, candidatura oficial e apuração permanecem em camadas diferentes. Enquanto não houver votos oficiais, a interface não transforma ausência de resultado em “0 votos”.</p>
                    @if ($fonte && $fonte['sha256'])
                        <p class="mt-4 break-all border-t border-gray-800 pt-4 font-mono text-[10px] leading-5 text-gray-500">SHA-256: {{ $fonte['sha256'] }}</p>
                    @endif
                </aside>
            </section>
        </div>
    </div>
</div>
