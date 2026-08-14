<div>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Acompanhamento Prioritário</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pessoas acompanhadas independentemente de existir candidatura ativa.</p>
        </div>
    </x-slot>

    <div class="py-5 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-3 sm:px-6 lg:px-8">
            <div class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:grid-cols-3 dark:border-gray-700 dark:bg-gray-800">
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buscar</label>
                    <input wire:model.live.debounce.300ms="busca" type="search" placeholder="Nome do político..." class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Grupo</label>
                    <select wire:model.live="grupo" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach ($grupos as $grupoItem)
                            <option value="{{ $grupoItem }}">{{ $grupoItem }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($acompanhamentos as $item)
                    @php $ultima = $item->politico?->candidaturas?->first(); @endphp
                    <a href="{{ route('politica.politicos.show', $item->politico) }}" wire:navigate class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-700">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                                    {{ mb_strtoupper(mb_substr($item->politico?->nome_publico ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-gray-900 group-hover:text-indigo-700 dark:text-white dark:group-hover:text-indigo-300">{{ $item->politico?->nome_publico }}</h3>
                                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $item->grupo }}</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">P{{ $item->prioridade }}</span>
                        </div>

                        <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-700">
                            @if ($ultima)
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $ultima->cargo?->nome }} · {{ $ultima->eleicao?->ano }}</p>
                                <div class="mt-2 flex items-end justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($ultima->votos_total, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">votos registrados</p>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ultima->partido?->sigla ?: 'Partido não informado' }}</span>
                                </div>
                            @else
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Sem candidatura sincronizada.</p>
                                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">O acompanhamento permanece ativo sem presumir situação eleitoral.</p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-gray-300 p-10 text-center text-gray-500 dark:border-gray-700 dark:text-gray-400">Nenhum acompanhamento encontrado.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
