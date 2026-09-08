@section('title', 'Turmas do Curso UNP')

<x-slot name="header">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Curso UNP — Turmas</h2>
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
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="grid flex-1 gap-3 sm:grid-cols-2">
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar turma, local ou instrutor"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <select wire:model.live="statusFiltro"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Todos os status</option>
                            <option value="planejamento">Planejamento</option>
                            <option value="aberta">Aberta</option>
                            <option value="em_andamento">Em andamento</option>
                            <option value="encerrada">Encerrada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <button type="button" wire:click="criar"
                        class="rounded-lg bg-red-700 px-5 py-2.5 font-semibold text-white hover:bg-red-800">
                        Nova turma
                    </button>
                </div>
            </div>

            <div class="hidden overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800 md:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Turma</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Período</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Instrutor</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Alunos</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-600 dark:text-gray-300">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($turmas as $turma)
                            <tr wire:key="turma-{{ $turma->id }}">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $turma->nome }}</p>
                                    <p class="text-sm text-gray-500">{{ $turma->local }} · {{ $turma->dias_horarios }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $turma->data_inicio->format('d/m/Y') }} a {{ $turma->data_fim->format('d/m/Y') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $turma->instrutor?->nome ?? 'Não definido' }}</td>
                                <td class="px-5 py-4 text-center text-sm text-gray-700 dark:text-gray-300">{{ $turma->matriculas_count }}{{ $turma->limite_alunos ? ' / '.$turma->limite_alunos : '' }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ str($turma->status)->replace('_', ' ')->title() }}</td>
                                <td class="px-5 py-4 text-right">
                                    <button wire:click="editar({{ $turma->id }})" class="font-semibold text-blue-600 hover:text-blue-800">Editar</button>
                                    <button wire:click="confirmarExclusao({{ $turma->id }})" class="ml-3 font-semibold text-red-600 hover:text-red-800">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Nenhuma turma encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 md:hidden">
                @forelse ($turmas as $turma)
                    <article class="rounded-xl bg-white p-5 shadow dark:bg-gray-800" wire:key="turma-card-{{ $turma->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $turma->nome }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $turma->local }}</p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ str($turma->status)->replace('_', ' ')->title() }}</span>
                        </div>
                        <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <p>{{ $turma->data_inicio->format('d/m/Y') }} a {{ $turma->data_fim->format('d/m/Y') }}</p>
                            <p>{{ $turma->dias_horarios }}</p>
                            <p>Alunos: {{ $turma->matriculas_count }}{{ $turma->limite_alunos ? ' / '.$turma->limite_alunos : '' }}</p>
                        </div>
                        <div class="mt-4 flex gap-4">
                            <button wire:click="editar({{ $turma->id }})" class="font-semibold text-blue-600">Editar</button>
                            <button wire:click="confirmarExclusao({{ $turma->id }})" class="font-semibold text-red-600">Excluir</button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl bg-white p-8 text-center text-gray-500 shadow">Nenhuma turma encontrada.</div>
                @endforelse
            </div>

            {{ $turmas->links() }}
        </div>
    </div>

    @if ($modalAberto)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4">
            <div class="mx-auto my-8 max-w-3xl rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $turmaId ? 'Editar turma' : 'Nova turma' }}</h3>
                    <button type="button" wire:click="fecharModal" class="text-2xl text-gray-500">&times;</button>
                </div>
                <form wire:submit.prevent="salvar" class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</label>
                        <input wire:model="nome" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('nome')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data de início</label>
                        <input type="date" wire:model="data_inicio" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('data_inicio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data de término</label>
                        <input type="date" wire:model="data_fim" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('data_fim')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Dias e horários</label>
                        <input wire:model="dias_horarios" placeholder="Ex.: sábados, 14h às 17h" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('dias_horarios')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Local</label>
                        <input wire:model="local" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('local')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Instrutor</label>
                        <select wire:model="instrutor_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Não definido</option>
                            @foreach ($instrutores as $instrutor)<option value="{{ $instrutor->id }}">{{ $instrutor->nome }}</option>@endforeach
                        </select>
                        @error('instrutor_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Limite de alunos</label>
                        <input type="number" min="1" wire:model="limite_alunos" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('limite_alunos')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                        <select wire:model="status" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="planejamento">Planejamento</option>
                            <option value="aberta">Aberta</option>
                            <option value="em_andamento">Em andamento</option>
                            <option value="encerrada">Encerrada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Link do WhatsApp da turma</label>
                        <input type="url" wire:model="link_whatsapp" placeholder="https://chat.whatsapp.com/..." class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('link_whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-200 pt-5 sm:col-span-2">
                        <button type="button" wire:click="fecharModal" class="rounded-lg border border-gray-300 px-5 py-2.5 font-semibold text-gray-700">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-red-700 px-5 py-2.5 font-semibold text-white hover:bg-red-800">Salvar turma</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($confirmarExclusaoId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Excluir turma?</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">A exclusão só será permitida se não houver matrículas.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="$set('confirmarExclusaoId', null)" class="rounded-lg border border-gray-300 px-4 py-2">Cancelar</button>
                    <button wire:click="excluir" class="rounded-lg bg-red-700 px-4 py-2 font-semibold text-white">Excluir</button>
                </div>
            </div>
        </div>
    @endif
</div>
