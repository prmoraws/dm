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
                    @else
                        <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                            <p class="font-medium text-gray-700 dark:text-gray-300">Ainda não há histórico eleitoral sincronizado para este político.</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">O perfil continua disponível para mandato, documentos e futura candidatura.</p>
                        </div>
                    @endif
                </div>
            </section>

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
                                <tr class="text-sm"><td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $cand->eleicao?->ano }}{{ $cand->eleicao?->turno ? ' · '.$cand->eleicao->turno.'º turno' : '' }}</td><td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $cand->cargo?->nome }}</td><td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $cand->partido?->sigla ?: '—' }}</td><td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ number_format($cand->votos_total, 0, ',', '.') }}</td><td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $cand->origem }}</td></tr>
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
