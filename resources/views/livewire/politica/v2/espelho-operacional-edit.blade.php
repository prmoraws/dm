<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600 dark:text-sky-400">Espelho operacional</p>
                <h2 class="mt-1 text-xl font-semibold leading-tight text-gray-900 dark:text-white">{{ $cidade->nome }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informações internas separadas dos resultados eleitorais oficiais.</p>
            </div>
            <a href="{{ route('politica.espelho.inteligente', $cidade) }}" wire:navigate
                class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                ← Voltar ao espelho
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="save" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-4 py-4 sm:px-6 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Dados de acompanhamento local</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A revisão atualiza apenas o espelho interno; nenhum dado eleitoral é sobrescrito.</p>
                </div>

                <div class="grid gap-5 p-4 sm:p-6 md:grid-cols-2">
                    <div>
                        <label for="presidente_local" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Presidente local</label>
                        <input id="presidente_local" wire:model="presidente_local" type="text" autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Nome do responsável local">
                        @error('presidente_local') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="indicacao_bispo" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Indicação</label>
                        <input id="indicacao_bispo" wire:model="indicacao_bispo" type="text" autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Responsável / indicação">
                        @error('indicacao_bispo') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="filiados_republicanos" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Filiados Republicanos</label>
                        <input id="filiados_republicanos" wire:model="filiados_republicanos" type="number" min="0" inputmode="numeric"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="0">
                        @error('filiados_republicanos') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="observacoes" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Observações</label>
                        <textarea id="observacoes" wire:model="observacoes" rows="6"
                            class="mt-1.5 w-full rounded-xl border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            placeholder="Contexto local, necessidades, histórico e observações operacionais..."></textarea>
                        @error('observacoes') <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6 dark:border-gray-700 dark:bg-gray-900/40">
                    <a href="{{ route('politica.espelho.inteligente', $cidade) }}" wire:navigate
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        Cancelar
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-wait disabled:opacity-60">
                        <span wire:loading.remove wire:target="save">Salvar espelho</span>
                        <span wire:loading wire:target="save">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
