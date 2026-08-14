<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $cidade->nome }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Espelho Inteligente · território, operação e resultado eleitoral separados por origem.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('politica.cidades') }}" wire:navigate class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">← Cidades</a>
                <a href="{{ route('politica.espelho.edit', $cidade) }}" wire:navigate class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Editar operacional</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:grid-cols-4 dark:border-gray-700 dark:bg-gray-800">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">População</p><p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $panorama['cidade']['populacao'] ? number_format($panorama['cidade']['populacao'], 0, ',', '.') : '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IBGE</p><p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $panorama['cidade']['ibge_code'] ?: '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cadeiras</p><p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $panorama['cidade']['cadeiras_camara'] ?: '—' }}</p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dados eleitorais</p><p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ count($panorama['eleitoral']['ranking']) > 0 ? 'Disponíveis' : 'Sem recorte' }}</p></div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between"><h3 class="font-semibold text-gray-900 dark:text-white">Espelho operacional</h3><span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-950/40 dark:text-sky-300">Interno</span></div>
                    @if ($panorama['operacional'])
                        <dl class="mt-4 space-y-3 text-sm">
                            <div><dt class="text-xs text-gray-500 dark:text-gray-400">Presidente local</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['presidente_local'] ?: 'Não informado' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 dark:text-gray-400">Indicação</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['indicacao_bispo'] ?: 'Não informada' }}</dd></div>
                            <div><dt class="text-xs text-gray-500 dark:text-gray-400">Filiados Republicanos</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-white">{{ $panorama['operacional']['filiados_republicanos'] !== null ? number_format($panorama['operacional']['filiados_republicanos'], 0, ',', '.') : '—' }}</dd></div>
                            @if ($panorama['operacional']['observacoes'])<div><dt class="text-xs text-gray-500 dark:text-gray-400">Observações</dt><dd class="mt-0.5 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $panorama['operacional']['observacoes'] }}</dd></div>@endif
                        </dl>
                    @else
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Nenhum espelho operacional cadastrado para esta cidade.</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Inteligência eleitoral</p><h3 class="mt-1 font-semibold text-gray-900 dark:text-white">Ranking do território</h3></div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <select wire:model.live="eleicaoId" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @forelse ($eleicoes as $eleicao)<option value="{{ $eleicao->id }}">{{ $eleicao->ano }} · {{ $eleicao->turno }}º turno</option>@empty<option value="">Sem eleições</option>@endforelse
                            </select>
                            <select wire:model.live="cargoId" class="rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @forelse ($cargos as $cargo)<option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>@empty<option value="">Sem cargos</option>@endforelse
                            </select>
                        </div>
                    </div>

                    @if ($panorama['eleitoral']['lider'])
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-950/20"><p class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-300">Líder</p><p class="mt-1 truncate font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['lider']['politico'] }}</p><p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ number_format($panorama['eleitoral']['lider']['votos'], 0, ',', '.') }}</p></div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50"><p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Segundo</p><p class="mt-1 truncate font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['segundo']['politico'] ?? '—' }}</p><p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ isset($panorama['eleitoral']['segundo']['votos']) ? number_format($panorama['eleitoral']['segundo']['votos'], 0, ',', '.') : '—' }}</p></div>
                            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50"><p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Diferença</p><p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $panorama['eleitoral']['diferenca_votos'] !== null ? number_format($panorama['eleitoral']['diferenca_votos'], 0, ',', '.') : '—' }}</p><p class="text-xs text-gray-500 dark:text-gray-400">votos</p></div>
                        </div>
                    @else
                        <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-7 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhum resultado disponível para este recorte.</div>
                    @endif
                </div>
            </section>

            @if ($panorama['alertas'] !== [])
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/20">
                    <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Qualidade e origem dos dados</h3>
                    <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-300">@foreach ($panorama['alertas'] as $alerta)<li>• {{ $alerta }}</li>@endforeach</ul>
                </section>
            @endif

            @if ($panorama['eleitoral']['ranking'] !== [])
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700"><h3 class="font-semibold text-gray-900 dark:text-white">Resultado do recorte</h3>@if ($panorama['eleitoral']['ranking_limitado'])<span class="text-xs text-gray-500 dark:text-gray-400">Top {{ count($panorama['eleitoral']['ranking']) }} de {{ $panorama['eleitoral']['ranking_total_resultados'] }}</span>@endif</div>
                    <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"><thead class="bg-gray-50 dark:bg-gray-900/40"><tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"><th class="px-5 py-3">#</th><th class="px-5 py-3">Político</th><th class="px-5 py-3">Partido</th><th class="px-5 py-3 text-right">Votos</th><th class="px-5 py-3">Origem</th><th class="px-5 py-3"></th></tr></thead><tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($panorama['eleitoral']['ranking'] as $i => $resultado)
                            <tr class="text-sm"><td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td><td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $resultado['politico'] }}</td><td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $resultado['partido'] ?: '—' }}</td><td class="px-5 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ number_format($resultado['votos'], 0, ',', '.') }}</td><td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $resultado['origem'] }}</td><td class="px-5 py-3 text-right">@if ($resultado['politico_slug'])<a href="{{ route('politica.politicos.show', $resultado['politico_slug']) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400">Perfil</a>@endif</td></tr>
                        @endforeach
                    </tbody></table></div>
                </section>
            @endif
        </div>
    </div>
</div>
