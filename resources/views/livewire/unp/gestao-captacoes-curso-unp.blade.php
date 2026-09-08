@section('title', 'Captações do Curso UNP')

<x-slot name="header">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Curso UNP — Gestão de captações</h2>
</x-slot>

<div>
    <div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-900 sm:px-6">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session()->has('message'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                <div class="grid gap-3 sm:grid-cols-2">
                    <input type="search" wire:model.live.debounce.300ms="search"
                        placeholder="Nome, celular, protocolo ou igreja"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <select wire:model.live="status"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos os status</option>
                        <option value="pendente">Pendentes</option>
                        <option value="aprovado">Aprovados</option>
                        <option value="rejeitado">Rejeitados</option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800">
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Candidato</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Igreja</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Status/Turma</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($captacoes as $captacao)
                                <tr wire:key="captacao-{{ $captacao->id }}">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $captacao->nome }}</p>
                                        <p class="text-sm text-gray-500">{{ $captacao->celular }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $captacao->igreja?->nome ?? 'Não encontrada' }}
                                        <p class="text-xs text-gray-500">{{ $captacao->bloco?->nome }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ str($captacao->status)->title() }}</span>
                                        @if ($captacao->matriculas->isNotEmpty())
                                            <p class="text-xs text-gray-500">{{ $captacao->matriculas->first()->turma?->nome }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <button wire:click="visualizar({{ $captacao->id }})" class="font-semibold text-blue-600">Ver</button>
                                        @if ($captacao->status === 'pendente')
                                            <button wire:click="abrirAprovacao({{ $captacao->id }})" class="ml-3 font-semibold text-green-600">Aprovar</button>
                                            <button wire:click="abrirRejeicao({{ $captacao->id }})" class="ml-3 font-semibold text-amber-600">Rejeitar</button>
                                        @endif
                                        @if ($captacao->status !== 'aprovado')
                                            <button wire:click="excluir({{ $captacao->id }})" wire:confirm="Excluir esta inscrição?" class="ml-3 font-semibold text-red-600">Excluir</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Nenhuma inscrição encontrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 p-4 md:hidden">
                    @forelse ($captacoes as $captacao)
                        <article class="rounded-xl border border-gray-200 p-4 dark:border-gray-700" wire:key="captacao-card-{{ $captacao->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div><h3 class="font-bold text-gray-900 dark:text-white">{{ $captacao->nome }}</h3><p class="text-sm text-gray-500">{{ $captacao->celular }}</p></div>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold">{{ str($captacao->status)->title() }}</span>
                            </div>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $captacao->igreja?->nome }}</p>
                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                <button wire:click="visualizar({{ $captacao->id }})" class="font-semibold text-blue-600">Ver</button>
                                @if ($captacao->status === 'pendente')
                                    <button wire:click="abrirAprovacao({{ $captacao->id }})" class="font-semibold text-green-600">Aprovar</button>
                                    <button wire:click="abrirRejeicao({{ $captacao->id }})" class="font-semibold text-amber-600">Rejeitar</button>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="py-8 text-center text-gray-500">Nenhuma inscrição encontrada.</p>
                    @endforelse
                </div>
            </div>

            {{ $captacoes->links() }}
        </div>
    </div>

    @if ($modalVisualizar && $selecionado)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4">
            <div class="mx-auto my-8 max-w-3xl rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dados da inscrição</h3>
                    <button wire:click="fecharModais" class="text-2xl text-gray-500">&times;</button>
                </div>
                <div class="mt-6 grid gap-5 sm:grid-cols-[auto_1fr]">
                    <img src="{{ Storage::disk('public_disk')->url($selecionado->foto) }}" alt="Foto de {{ $selecionado->nome }}"
                        class="h-32 w-32 rounded-xl object-cover shadow">
                    <dl class="grid gap-3 text-sm sm:grid-cols-2">
                        <div><dt class="font-semibold text-gray-500">Nome</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Celular</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->celular }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Bloco</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->bloco?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Região</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->regiao?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Igreja</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->igreja?->nome }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Estado civil</dt><dd class="text-gray-900 dark:text-white">{{ str($selecionado->estado_civil)->title() }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Batizado nas águas</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->batizado_aguas ? 'Sim' : 'Não' }}{{ $selecionado->data_batismo_aguas ? ' — '.$selecionado->data_batismo_aguas->format('d/m/Y') : '' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Batizado no Espírito Santo</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->batizado_espirito_santo ? 'Sim' : 'Não' }}{{ $selecionado->data_batismo_espirito_santo ? ' — '.$selecionado->data_batismo_espirito_santo->format('d/m/Y') : '' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="font-semibold text-gray-500">Endereço</dt><dd class="text-gray-900 dark:text-white">{{ $selecionado->endereco_completo }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    @endif

    @if ($modalAprovar && $selecionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Aprovar e matricular</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Selecione a turma de {{ $selecionado->nome }}.</p>
                <select wire:model="turma_id" class="mt-5 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                    <option value="">Selecione a turma</option>
                    @foreach ($turmas as $turma)
                        <option value="{{ $turma->id }}">
                            {{ $turma->nome }} — {{ $turma->matriculas_count }}{{ $turma->limite_alunos ? '/'.$turma->limite_alunos : '' }} alunos
                        </option>
                    @endforeach
                </select>
                @error('turma_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @if ($turmas->isEmpty())<p class="mt-3 text-sm text-amber-700">Crie ou abra uma turma antes de aprovar inscrições.</p>@endif
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="fecharModais" class="rounded-lg border border-gray-300 px-4 py-2">Cancelar</button>
                    <button wire:click="aprovar" @disabled($turmas->isEmpty()) class="rounded-lg bg-green-700 px-4 py-2 font-semibold text-white disabled:opacity-50">Aprovar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($modalRejeitar && $selecionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rejeitar inscrição</h3>
                <textarea wire:model="motivo_rejeicao" rows="4" maxlength="1000" placeholder="Informe o motivo"
                    class="mt-5 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>
                @error('motivo_rejeicao')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="fecharModais" class="rounded-lg border border-gray-300 px-4 py-2">Cancelar</button>
                    <button wire:click="rejeitar" class="rounded-lg bg-red-700 px-4 py-2 font-semibold text-white">Confirmar rejeição</button>
                </div>
            </div>
        </div>
    @endif
</div>
