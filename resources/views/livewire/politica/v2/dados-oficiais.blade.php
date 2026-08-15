<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Fonte oficial</p>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Dados Abertos do TSE</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Candidaturas e resultados importados em CLI, com rastreabilidade e sem carga externa nas páginas.</p>
            </div>
            <a href="{{ route('politica.dashboard') }}" wire:navigate class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">← Dashboard</a>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-3 sm:px-6 lg:px-8">
            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Candidaturas oficiais', 'value' => $metricas['candidaturas_oficiais']],
                    ['label' => 'Políticos vinculados', 'value' => $metricas['politicos_com_tse']],
                    ['label' => 'Municípios c/ código TSE', 'value' => $metricas['municipios_com_codigo_tse']],
                    ['label' => 'Importações concluídas', 'value' => $metricas['importacoes_ok']],
                ] as $card)
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($card['value'], 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Armazenamento Política</p>
                        <div class="mt-2 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $armazenamento['politica_human'] }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">de {{ $armazenamento['database_human'] }} no banco completo</span>
                        </div>
                        @unless ($armazenamento['exact'])
                            <p class="mt-2 text-xs text-amber-600 dark:text-amber-400">Medição exata disponível em MySQL/MariaDB. O ambiente atual usa {{ $armazenamento['driver'] }}.</p>
                        @endunless
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm sm:min-w-80">
                        <div class="rounded-xl bg-amber-50 px-3 py-2 dark:bg-amber-950/30">
                            <p class="text-xs text-amber-700 dark:text-amber-300">Aviso</p>
                            <p class="font-semibold text-amber-900 dark:text-amber-100">{{ $armazenamento['warning_human'] }}</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 px-3 py-2 dark:bg-rose-950/30">
                            <p class="text-xs text-rose-700 dark:text-rose-300">Bloqueio</p>
                            <p class="font-semibold text-rose-900 dark:text-rose-100">{{ $armazenamento['hard_limit_human'] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Cobertura oficial no banco</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Quantidade de candidaturas TSE por ano e cargo.</p>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($porAno as $ano => $itens)
                                <div class="p-5">
                                    <div class="mb-3 flex items-center justify-between">
                                        <strong class="text-gray-900 dark:text-white">{{ $ano }}</strong>
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">{{ number_format($itens->sum('total'), 0, ',', '.') }} candidaturas</span>
                                    </div>
                                    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                                        @foreach ($itens as $item)
                                            <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-gray-900/60">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->cargo }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($item->total, 0, ',', '.') }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">Nenhuma candidatura oficial importada ainda.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Últimas importações</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auditoria de cada processamento de arquivo oficial.</p>
                        </div>
                        <div class="hidden overflow-x-auto md:block">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/60">
                                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        <th class="px-4 py-3">Ano</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Escopo</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Selecionadas</th><th class="px-4 py-3">Concluída</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($importacoes as $item)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $item->ano }}</td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->tipo }}</td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->escopo }}</td>
                                            <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->status === 'concluida' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : ($item->status === 'falhou' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300') }}">{{ $item->status }}</span></td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ number_format($item->linhas_selecionadas, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $item->concluida_em?->format('d/m/Y H:i') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-700">
                            @forelse ($importacoes as $item)
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-2">
                                        <strong class="text-gray-900 dark:text-white">{{ $item->ano }} · {{ $item->tipo }}</strong>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->status }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item->escopo }} · {{ number_format($item->linhas_selecionadas, 0, ',', '.') }} selecionadas</p>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">Sem histórico de importação.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Arquivos monitorados</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($fontes as $fonte)
                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/60">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="break-all text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $fonte->tipo_arquivo }}</span>
                                        <span class="text-xs {{ $fonte->erros_consecutivos ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $fonte->http_status ?? 'local' }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $fonte->ultimo_sucesso_em?->format('d/m/Y H:i') ?? 'Ainda não baixado' }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">A primeira sincronização registrará as fontes automaticamente.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 dark:border-indigo-900 dark:bg-indigo-950/30">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Escopo econômico</p>
                        <h3 class="mt-2 font-semibold text-indigo-950 dark:text-indigo-100">Guardar somente o que o Espelho precisa</h3>
                        <p class="mt-2 text-sm leading-6 text-indigo-900/80 dark:text-indigo-200">REPUBLICANOS: vereador, prefeito, deputados e senador. Todos os partidos: governador e presidente. Resultados territoriais ficam em município e zona, sem importar seções completas.</p>
                    </div>

                    <div class="rounded-2xl bg-gray-950 p-5 text-white shadow-sm dark:bg-black">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-300">Execução segura</p>
                        <h3 class="mt-2 font-semibold">Importação pesada fica fora do navegador</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-300">O usuário consulta apenas o MySQL local. Downloads e CSVs grandes são processados por comando Artisan, em streaming e removidos após uma importação bem-sucedida.</p>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</div>
