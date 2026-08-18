@section('title', 'Política - ' . $politico->nome_publico)
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $politico->nome_publico }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perfil político e histórico eleitoral consolidado.</p>
            </div>
            <a href="{{ route('politica.acompanhamento') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-indigo-400 dark:hover:bg-gray-700">← Acompanhamento</a>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-4">
                        @if ($politico->foto_url)
                            <img src="{{ $politico->foto_url }}" alt="{{ $politico->nome_publico }}" class="h-20 w-20 rounded-2xl object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-indigo-100 text-3xl font-bold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">{{ mb_strtoupper(mb_substr($politico->nome_publico, 0, 1)) }}</div>
                        @endif
                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-bold text-gray-900 dark:text-white">{{ $politico->nome_publico }}</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $politico->nome_completo }}</p>
                            @if ($politico->acompanhamento)
                                <span class="mt-2 inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">{{ $politico->acompanhamento->grupo }}</span>
                            @endif
                        </div>
                    </div>
                    @if ($politico->biografia)
                        <p class="mt-5 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $politico->biografia }}</p>
                    @else
                        <p class="mt-5 rounded-lg bg-gray-50 p-3 text-xs leading-5 text-gray-500 dark:bg-gray-900/50 dark:text-gray-400">Biografia e fontes oficiais ainda não sincronizadas. O sistema não completa dados ausentes por suposição.</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Recorte eleitoral</p>
                            <h2 class="mt-1 font-semibold text-gray-900 dark:text-white">Desempenho territorial</h2>
                        </div>
                        @if ($politico->candidaturas->isNotEmpty())
                            <select wire:model.live="candidaturaId" class="w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 sm:w-auto dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @foreach ($politico->candidaturas as $cand)
                                    <option value="{{ $cand->id }}">{{ $cand->eleicao?->ano }} · {{ $cand->cargo?->nome }}{{ $cand->partido?->sigla ? ' · '.$cand->partido->sigla : '' }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    @if ($candidaturaSelecionada && $desempenho)
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Votos totais</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($desempenho['total_votos'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Municípios com votos</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($desempenho['total_municipios_com_votos'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Concentração Top 5</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $desempenho['concentracao_top5_percentual'] !== null ? number_format($desempenho['concentracao_top5_percentual'], 2, ',', '.').'%' : '—' }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Origem: {{ $desempenho['origem'] }}. Dados migrados do legado permanecem identificados até confirmação por fonte oficial.</p>
                    @elseif (!$candidaturaSelecionada)
                        <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                            <p class="font-medium text-gray-700 dark:text-gray-300">Ainda não há histórico eleitoral sincronizado para este político.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">O perfil continua disponível para mandato, documentos e futura candidatura.</p>
                        </div>
                    @else
                        <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                            <p class="font-medium text-gray-700 dark:text-gray-300">Resultados territoriais ainda não disponíveis para esta candidatura.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">O sistema mantém candidatura e votação separadas e não transforma ausência de resultados em “0 votos”.</p>
                        </div>
                    @endif
                </div>
            </section>

            @if ($historicoEspecial)
                <section class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm dark:border-indigo-900/70 dark:bg-gray-800">
                    <div class="border-b border-indigo-100 bg-indigo-50/60 px-4 py-4 sm:px-5 dark:border-indigo-900/60 dark:bg-indigo-950/20">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Histórico oficial especial</p>
                                    <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-200">somente 4 acompanhados</span>
                                </div>
                                <h2 class="mt-1 font-semibold text-gray-900 dark:text-white">Eleições, mandatos e cargos públicos</h2>
                                <p class="mt-1 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">Para Márcio Marinho, Rogéria Santos, José de Arimateia e Jurailton Santos, o sistema mantém um histórico ampliado. Eleições vêm do TSE; mandatos e cargos só entram quando descritos por fonte institucional oficial.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        @if ($politico->filiacoes->isNotEmpty())
                            <div class="mb-5 rounded-2xl border border-violet-200 bg-violet-50/60 p-4 dark:border-violet-900/60 dark:bg-violet-950/20">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wider text-violet-700 dark:text-violet-300">Filiação partidária</p>
                                        <h3 class="mt-1 font-semibold text-gray-900 dark:text-white">Linha do tempo partidária</h3>
                                        <p class="mt-1 text-xs leading-5 text-gray-600 dark:text-gray-300">Filiação é registrada separadamente de candidatura e mandato para preservar mudanças de partido ocorridas durante o exercício do cargo.</p>
                                    </div>
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                                    @foreach ($politico->filiacoes->sortBy('ano_inicio') as $filiacao)
                                        <article class="rounded-xl border border-violet-100 bg-white p-3 shadow-sm dark:border-violet-900/50 dark:bg-gray-900/60">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-base font-bold text-gray-950 dark:text-white">{{ $filiacao->partido?->sigla ?: '—' }}</span>
                                                @if ($filiacao->fonte_oficial)
                                                    <span class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">oficial</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-sm font-semibold text-violet-700 dark:text-violet-300">{{ $filiacao->periodoExibicao() }}</p>
                                            @if ($filiacao->observacoes)
                                                <p class="mt-2 text-xs leading-5 text-gray-600 dark:text-gray-300">{{ $filiacao->observacoes }}</p>
                                            @endif
                                            <div class="mt-3 flex items-end justify-between gap-2 text-[10px] text-gray-500 dark:text-gray-400">
                                                <span>{{ $filiacao->fonteLabel() }}</span>
                                                @if ($filiacao->fonte_url)
                                                    <a href="{{ $filiacao->fonte_url }}" target="_blank" rel="noopener noreferrer" class="shrink-0 font-semibold text-violet-700 hover:underline dark:text-violet-300">Fonte ↗</a>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                                @if ($notaFiliacoes)
                                    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-900 dark:border-amber-900/70 dark:bg-amber-950/30 dark:text-amber-200">{{ $notaFiliacoes }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($linhaDoTempo->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                                <p class="font-medium text-gray-700 dark:text-gray-300">Histórico especial configurado, mas ainda não sincronizado.</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Execute a sincronização oficial para preencher eleições e mandatos sem recorrer a dados presumidos.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($linhaDoTempo as $evento)
                                    @php
                                        $tipoLabel = match ($evento['tipo']) {
                                            'eleicao' => 'Eleição',
                                            'cargo_publico' => 'Cargo público',
                                            default => 'Mandato',
                                        };
                                        $tipoClasses = match ($evento['tipo']) {
                                            'eleicao' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300',
                                            'cargo_publico' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                            default => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                        };
                                    @endphp
                                    <article class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $tipoClasses }}">{{ $tipoLabel }}</span>
                                                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $evento['periodo'] }}</span>
                                                    @if ($evento['oficial'])
                                                        <span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-600 dark:bg-gray-700 dark:text-gray-300">fonte oficial</span>
                                                    @endif
                                                </div>
                                                <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $evento['titulo'] }}</h3>
                                                @if ($evento['detalhes'])
                                                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $evento['detalhes'] }}</p>
                                                @endif
                                                @if ($evento['tipo'] === 'eleicao')
                                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                                        <span class="text-gray-600 dark:text-gray-300">Votos: <strong class="text-gray-900 dark:text-white">{{ $evento['votos'] !== null ? number_format($evento['votos'], 0, ',', '.') : 'aguardando resultado oficial' }}</strong></span>
                                                        @if ($evento['resultado'])
                                                            <span class="text-gray-600 dark:text-gray-300">Resultado: <strong class="text-gray-900 dark:text-white">{{ $evento['resultado'] }}</strong></span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="shrink-0 text-xs text-gray-500 dark:text-gray-400 md:text-right">
                                                <p>{{ $evento['fonte'] }}</p>
                                                @if ($evento['fonte_url'])
                                                    <a href="{{ $evento['fonte_url'] }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Abrir fonte oficial ↗</a>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            @if ($candidaturaOficial2026)
                @php
                    $tomStatus = $candidaturaOficial2026->situacaoRegistroTom();
                    $classesStatus = match ($tomStatus) {
                        'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                        'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
                        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    };
                @endphp
                <section class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm dark:border-emerald-900/70 dark:bg-gray-800">
                    <div class="border-b border-emerald-100 bg-emerald-50/60 px-5 py-4 dark:border-emerald-900/60 dark:bg-emerald-950/20">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Eleições 2026</p>
                                <h2 class="mt-1 font-semibold text-gray-900 dark:text-white">Registro de candidatura localizado no TSE</h2>
                            </div>
                            <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $classesStatus }}">{{ $candidaturaOficial2026->situacaoRegistroExibicao() }}</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50"><p class="text-xs text-gray-500 dark:text-gray-400">Cargo</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $candidaturaOficial2026->cargo?->nome ?: '—' }}</p></div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50"><p class="text-xs text-gray-500 dark:text-gray-400">Partido</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $candidaturaOficial2026->partido?->sigla ?: '—' }}</p></div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50"><p class="text-xs text-gray-500 dark:text-gray-400">Número</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $candidaturaOficial2026->numero_urna ?: '—' }}</p></div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50"><p class="text-xs text-gray-500 dark:text-gray-400">Nome de urna</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $candidaturaOficial2026->nome_urna ?: '—' }}</p></div>
                        </div>
                        <div class="mt-4 grid gap-3 text-xs text-gray-500 sm:grid-cols-3 dark:text-gray-400">
                            <div><span class="font-semibold text-gray-700 dark:text-gray-300">SQ candidato:</span> {{ $candidaturaOficial2026->tse_sq_candidato ?: '—' }}</div>
                            <div><span class="font-semibold text-gray-700 dark:text-gray-300">Sincronização:</span> {{ $candidaturaOficial2026->sincronizado_em?->format('d/m/Y H:i') ?: '—' }}</div>
                            <div><span class="font-semibold text-gray-700 dark:text-gray-300">Origem:</span> Dados Abertos TSE</div>
                        </div>
                        @if ($candidaturaOficial2026->situacaoRegistroEhCodigoTecnico() && $candidaturaOficial2026->situacao_registro)
                            <p class="mt-3 text-xs text-amber-700 dark:text-amber-300">Código técnico recebido do TSE: <strong>{{ $candidaturaOficial2026->situacao_registro }}</strong>. O sistema preserva o valor bruto sem atribuir significado que a fonte ainda não apresentou de forma legível.</p>
                        @endif
                        <div class="mt-4 rounded-xl border px-4 py-3 text-sm leading-6 {{ $contexto2026['antes_prazo'] ? 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200' : 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-900 dark:bg-sky-950/30 dark:text-sky-200' }}">
                            @if ($contexto2026['antes_prazo'])
                                A base de 2026 ainda está em período de recebimento de registros. Prazo formal configurado: <strong>{{ $contexto2026['prazo']->format('d/m/Y H:i') }}</strong>. Uma nova sincronização deve ser feita após esse horário.
                            @else
                                O prazo formal de registro já encerrou. A Justiça Eleitoral pode continuar processando e atualizando situações; mantenha a sincronização TSE como fonte da verdade.
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @if ($desempenho && $desempenho['municipios'] !== [])
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Municípios com maior votação</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Exibindo até 20 municípios para manter a página leve.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <th class="px-5 py-3">#</th><th class="px-5 py-3">Município</th><th class="px-5 py-3 text-right">Votos</th><th class="px-5 py-3 text-right">Participação</th><th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($desempenho['municipios'] as $indice => $municipio)
                                    <tr class="text-sm">
                                        <td class="px-5 py-3 text-gray-400">{{ $indice + 1 }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $municipio['cidade'] }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ number_format($municipio['votos'], 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-right text-gray-500 dark:text-gray-400">{{ $municipio['participacao_nos_votos_do_candidato'] !== null ? number_format($municipio['participacao_nos_votos_do_candidato'], 2, ',', '.').'%' : '—' }}</td>
                                        <td class="px-5 py-3 text-right"><a href="{{ route('politica.espelho.inteligente', $municipio['cidade_id']) }}" wire:navigate class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Espelho</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700"><h2 class="font-semibold text-gray-900 dark:text-white">Histórico de candidaturas</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40"><tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"><th class="px-5 py-3">Eleição</th><th class="px-5 py-3">Cargo</th><th class="px-5 py-3">Partido</th><th class="px-5 py-3 text-right">Votos</th><th class="px-5 py-3">Origem</th></tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($politico->candidaturas as $cand)
                                <tr class="text-sm"><td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $cand->eleicao?->ano }}{{ $cand->eleicao?->turno ? ' · '.$cand->eleicao->turno.'º turno' : '' }}</td><td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $cand->cargo?->nome }}</td><td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $cand->partido?->sigla ?: '—' }}</td><td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ (($cand->resultados_municipais_count ?? 0) > 0 || ($cand->resultados_zonas_count ?? 0) > 0) ? number_format($cand->votos_total, 0, ',', '.') : '—' }}</td><td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $cand->isRegistroOficialTse() ? 'TSE oficial' : $cand->origem }}</td></tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma candidatura registrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
