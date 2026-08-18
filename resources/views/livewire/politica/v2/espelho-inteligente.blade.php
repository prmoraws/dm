@section('title', 'Política - Espelho - ' . $cidade->nome)
<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Espelho Inteligente</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">{{ $cidade->nome }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Território, operação e resultado eleitoral em camadas separadas e rastreáveis.</p>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
                <a href="{{ route('politica.cidades') }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">← Cidades</a>
                <a href="{{ route('politica.espelho.edit', $cidade) }}" wire:navigate
                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">Editar operacional</a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-3 sm:px-6 lg:px-8">
            <section class="grid grid-cols-2 gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-4 sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">População</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ $panorama['cidade']['populacao'] ? number_format($panorama['cidade']['populacao'], 0, ',', '.') : '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IBGE</p>
                    <p class="mt-1 break-all text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ $panorama['cidade']['ibge_code'] ?: '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cadeiras</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ $panorama['cidade']['cadeiras_camara'] ?: '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Resultados</p>
                    <p class="mt-1 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ $ranking->total() > 0 ? number_format($ranking->total(), 0, ',', '.') : '—' }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">candidatos no recorte</p>
                </div>
            </section>

            <section class="rounded-2xl border border-indigo-200 bg-indigo-50/60 p-4 shadow-sm sm:p-5 dark:border-indigo-900/60 dark:bg-indigo-950/20">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-700 dark:text-indigo-300">Relatório robusto por cidade</p>
                        <h3 class="mt-1 text-lg font-bold text-gray-950 dark:text-white">Candidato favorito do Espelho</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">Selecione o candidato que será o foco do relatório. O PDF e o Excel incluem contexto municipal, espelho operacional, desempenho local, histórico do mesmo cargo, zonas, ranking e auditoria de integridade.</p>
                    </div>

                    <div class="w-full xl:max-w-xl">
                        <label for="candidato-relatorio" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Candidato do relatório</label>
                        <select id="candidato-relatorio" wire:model.live="candidaturaRelatorioId" class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Selecione um candidato</option>
                            @foreach ($candidatosRelatorio as $opcaoRelatorio)
                                <option value="{{ $opcaoRelatorio->id }}">
                                    {{ $opcaoRelatorio->politico?->nome_publico ?? $opcaoRelatorio->nome_urna ?? 'Candidato' }} · {{ $opcaoRelatorio->partido?->sigla ?: (config('politica.migracao_v1.partidos_legacy.'.$opcaoRelatorio->legacy_candidato_id) ?: '—') }} · nº {{ $opcaoRelatorio->numero_urna ?: '—' }}
                                    @if ($opcaoRelatorio->resultado_municipal_id)
                                        · {{ number_format((int) $opcaoRelatorio->votos_no_municipio, 0, ',', '.') }} votos
                                    @else
                                        · sem resultado oficial
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if (session('politica_espelho_relatorio_ok'))
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">{{ session('politica_espelho_relatorio_ok') }}</div>
                @endif
                @if (session('politica_espelho_relatorio_erro'))
                    <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200">{{ session('politica_espelho_relatorio_erro') }}</div>
                @endif

                @if ($candidaturaRelatorio)
                    <div class="mt-4 flex flex-col gap-3 rounded-xl border border-indigo-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between dark:border-indigo-900/60 dark:bg-gray-900/60">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="truncate font-bold text-gray-950 dark:text-white">{{ $candidaturaRelatorio->politico?->nome_publico ?? $candidaturaRelatorio->nome_urna ?? 'Candidato' }}</p>
                                @if ($favoritoRelatorio)
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">{{ $favoritoRelatorio->classificacao === 'favorito' ? 'Favorito salvo' : 'Acompanhamento' }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $cargoSelecionado?->nome ?: 'Cargo' }} · {{ $candidaturaRelatorio->partido?->sigla ?: '—' }} · nº {{ $candidaturaRelatorio->numero_urna ?: '—' }}
                                @if ($candidaturaRelatorio->resultado_municipal_id)
                                    · {{ number_format((int) $candidaturaRelatorio->votos_no_municipio, 0, ',', '.') }} votos neste município
                                @else
                                    · sem resultado municipal oficial
                                @endif
                            </p>
                        </div>
                        <div class="grid shrink-0 grid-cols-1 gap-2 sm:grid-cols-3">
                            <button type="button" wire:click="marcarFavoritoRelatorio" class="inline-flex items-center justify-center rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200 dark:hover:bg-amber-950/50">★ Marcar favorito</button>
                            <a href="{{ route('politica.espelho.relatorio.pdf', ['cidade' => $cidade, 'candidatura' => $candidaturaRelatorio->id]) }}" target="_blank" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">Exportar PDF</a>
                            <a href="{{ route('politica.espelho.relatorio.excel', ['cidade' => $cidade, 'candidatura' => $candidaturaRelatorio->id]) }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Exportar Excel</a>
                        </div>
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-dashed border-indigo-300 p-4 text-sm text-indigo-800 dark:border-indigo-800 dark:text-indigo-200">Selecione um candidato do recorte atual para habilitar o relatório por cidade. Se já houver favorito/acompanhamento salvo para este recorte, ele será selecionado automaticamente.</div>
                @endif
            </section>

            <section class="grid gap-5 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Espelho operacional</h3>
                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">Interno</span>
                    </div>
                    @if ($panorama['operacional'])
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45"><dt class="text-xs text-gray-500 dark:text-gray-400">Presidente local</dt><dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['presidente_local'] ?: 'Não informado' }}</dd></div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45"><dt class="text-xs text-gray-500 dark:text-gray-400">Indicação</dt><dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['indicacao_bispo'] ?: 'Não informada' }}</dd></div>
                            <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45"><dt class="text-xs text-gray-500 dark:text-gray-400">Filiados Republicanos</dt><dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['filiados_republicanos'] !== null ? number_format($panorama['operacional']['filiados_republicanos'], 0, ',', '.') : '—' }}</dd></div>
                            @if ($panorama['operacional']['observacoes'])
                                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/45"><dt class="text-xs text-gray-500 dark:text-gray-400">Observações</dt><dd class="mt-1 whitespace-pre-line break-words text-gray-700 dark:text-gray-300">{{ $panorama['operacional']['observacoes'] }}</dd></div>
                            @endif
                        </dl>
                    @else
                        <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            Nenhum espelho operacional cadastrado para esta cidade.
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5 lg:col-span-2 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Inteligência eleitoral</p>
                            <h3 class="mt-1 font-semibold text-gray-900 dark:text-white">Ranking do território</h3>
                            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                @if ($usaFiltroRepublicanos)
                                    Para {{ $cargoSelecionado?->nome }}, o padrão é exibir todos os candidatos do {{ config('politica.espelho.partido_prioritario', 'REPUBLICANOS') }}. Você pode alternar para todos os partidos.
                                @else
                                    Para {{ $cargoSelecionado?->nome ?? 'este cargo' }}, o espelho exibe todos os candidatos disponíveis.
                                @endif
                            </p>
                        </div>

                        <div class="grid w-full gap-2 sm:grid-cols-2 xl:w-auto {{ $usaFiltroRepublicanos ? 'xl:grid-cols-3' : 'xl:grid-cols-2' }}">
                            <select wire:model.live="eleicaoId" class="w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @forelse ($eleicoes as $eleicao)
                                    <option value="{{ $eleicao->id }}">{{ $eleicao->ano }} · {{ $eleicao->turno }}º turno</option>
                                @empty
                                    <option value="">Sem eleições</option>
                                @endforelse
                            </select>
                            <select wire:model.live="cargoId" class="w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @forelse ($cargos as $cargo)
                                    <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                                @empty
                                    <option value="">Sem cargos</option>
                                @endforelse
                            </select>
                            @if ($usaFiltroRepublicanos)
                                <select wire:model.live="escopoCandidatos" class="w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    <option value="auto">{{ config('politica.espelho.partido_prioritario', 'REPUBLICANOS') }} (padrão)</option>
                                    <option value="republicanos">{{ config('politica.espelho.partido_prioritario', 'REPUBLICANOS') }}</option>
                                    <option value="todos">Todos os partidos</option>
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($partidoFiltro)
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">Filtro: {{ $partidoFiltro }}</span>
                        @else
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Todos os candidatos</span>
                        @endif
                        @if ($cargoSelecionado)
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $cargoSelecionado->nome }}</span>
                        @endif
                    </div>

                    @if ($panorama['eleitoral']['lider'])
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-950/20">
                                <p class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-300">Líder no recorte</p>
                                <p class="mt-1 break-words font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['lider']['politico'] }}</p>
                                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ number_format($panorama['eleitoral']['lider']['votos'], 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Segundo</p>
                                <p class="mt-1 break-words font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['segundo']['politico'] ?? '—' }}</p>
                                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ isset($panorama['eleitoral']['segundo']['votos']) ? number_format($panorama['eleitoral']['segundo']['votos'], 0, ',', '.') : '—' }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Diferença</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['diferenca_votos'] !== null ? number_format($panorama['eleitoral']['diferenca_votos'], 0, ',', '.') : '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">votos</p>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            @if ($ranking->total() > 0)
                                Há candidatos neste recorte, mas nenhum possui votos registrados neste município. Eles continuam listados abaixo com 0 voto.
                            @else
                                Nenhuma candidatura foi carregada para este recorte. A sincronização oficial ampliará a base disponível.
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            @if ($panorama['alertas'] !== [])
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/20">
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Qualidade e origem dos dados</h3>
                    <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-300">
                        @foreach ($panorama['alertas'] as $alerta)<li>• {{ $alerta }}</li>@endforeach
                    </ul>
                </section>
            @endif

            @if ($ranking->count() > 0)
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-1 border-b border-gray-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 dark:border-gray-700">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Candidatos do recorte</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($ranking->total(), 0, ',', '.') }} candidato(s) · {{ max(10, min((int) config('politica.espelho.ranking_por_pagina', 25), 100)) }} por página</p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Página {{ $ranking->currentPage() }} de {{ $ranking->lastPage() }}</span>
                    </div>

                    <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-700">
                        @foreach ($ranking as $resultado)
                            <article class="p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="break-words font-semibold text-gray-900 dark:text-white">{{ $resultado->politico?->nome_publico ?? 'Candidato' }}</p>
                                        <div class="mt-1 flex flex-wrap gap-1.5 text-xs">
                                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $resultado->partido?->sigla ?: (config('politica.migracao_v1.partidos_legacy.'.$resultado->legacy_candidato_id) ?: '—') }}</span>
                                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $resultado->origem ?: '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format((int) $resultado->votos_no_municipio, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">votos</p>
                                    </div>
                                </div>
                                @if ($resultado->politico?->slug)
                                    <a href="{{ route('politica.politicos.show', $resultado->politico->slug) }}" wire:navigate class="mt-3 inline-flex text-sm font-semibold text-indigo-600 dark:text-indigo-400">Abrir perfil →</a>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    <th class="px-5 py-3">#</th>
                                    <th class="px-5 py-3">Político</th>
                                    <th class="px-5 py-3">Partido</th>
                                    <th class="px-5 py-3 text-right">Votos</th>
                                    <th class="px-5 py-3">Origem</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($ranking as $resultado)
                                    <tr class="text-sm transition hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-5 py-3 text-gray-400">{{ $ranking->firstItem() + $loop->index }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $resultado->politico?->nome_publico ?? 'Candidato' }}</td>
                                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $resultado->partido?->sigla ?: (config('politica.migracao_v1.partidos_legacy.'.$resultado->legacy_candidato_id) ?: '—') }}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ number_format((int) $resultado->votos_no_municipio, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $resultado->origem ?: '—' }}</td>
                                        <td class="px-5 py-3 text-right">
                                            @if ($resultado->politico?->slug)
                                                <a href="{{ route('politica.politicos.show', $resultado->politico->slug) }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Perfil</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($ranking->hasPages())
                        <div class="border-t border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">{{ $ranking->links() }}</div>
                    @endif
                </section>
            @endif
        </div>
    </div>
</div>
