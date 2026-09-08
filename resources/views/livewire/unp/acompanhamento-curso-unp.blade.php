@section('title', 'Acompanhamento do Curso UNP')

<x-slot name="header">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Curso UNP — Chamada e conclusão</h2>
</x-slot>

<div>
    <div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-900 sm:px-6">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session()->has('message'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">{{ session('message') }}</div>
            @endif

            <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                <div class="grid gap-4 md:grid-cols-[1fr_220px_auto] md:items-end">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Turma</label>
                        <select wire:model.live="turma_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Selecione uma turma</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}">{{ $turma->nome }} — {{ $turma->data_inicio->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                        @error('turma_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Data da aula</label>
                        <input type="date" wire:model.live="data_aula" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                        @error('data_aula')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button wire:click="salvarChamada" @disabled(!$turma_id)
                        class="rounded-lg bg-red-700 px-5 py-2.5 font-semibold text-white hover:bg-red-800 disabled:opacity-50">
                        Salvar chamada
                    </button>
                </div>
            </div>

            @if ($turma_id)
                <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Lista de alunos</h3>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="marcarTodos('presente')" class="rounded-lg bg-green-100 px-3 py-2 text-sm font-semibold text-green-800">Todos presentes</button>
                            <button wire:click="marcarTodos('ausente')" class="rounded-lg bg-red-100 px-3 py-2 text-sm font-semibold text-red-800">Todos ausentes</button>
                            <button wire:click="marcarTodos('justificada')" class="rounded-lg bg-amber-100 px-3 py-2 text-sm font-semibold text-amber-800">Todas justificadas</button>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($matriculas as $matricula)
                            @php
                                $total = $matricula->presencas->count();
                                $presentes = $matricula->presencas->where('situacao', 'presente')->count();
                                $percentual = $total > 0 ? round(($presentes / $total) * 100) : 0;
                                $finalizado = in_array($matricula->situacao, ['aprovado', 'reprovado', 'desistente'], true);
                            @endphp
                            <article class="rounded-xl border border-gray-200 p-4 dark:border-gray-700" wire:key="matricula-{{ $matricula->id }}">
                                <div class="grid gap-4 lg:grid-cols-[1fr_180px_1fr_auto] lg:items-center">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $matricula->captacao->nome }}</p>
                                        <p class="text-sm text-gray-500">{{ $matricula->captacao->celular }}</p>
                                        <p class="mt-1 text-xs font-semibold text-gray-500">Frequência: {{ $presentes }}/{{ $total }} ({{ $percentual }}%)</p>
                                    </div>
                                    <div>
                                        @if (!$finalizado)
                                            <select wire:model="presencas.{{ $matricula->id }}" class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white">
                                                <option value="">Selecione</option>
                                                <option value="presente">Presente</option>
                                                <option value="ausente">Ausente</option>
                                                <option value="justificada">Falta justificada</option>
                                            </select>
                                            @error("presencas.{$matricula->id}")<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                        @else
                                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">{{ str($matricula->situacao)->title() }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if (!$finalizado)
                                            <input wire:model="observacoes.{{ $matricula->id }}" maxlength="500" placeholder="Observação opcional"
                                                class="w-full rounded-lg border-gray-300 text-sm dark:bg-gray-700 dark:text-white">
                                        @elseif ($matricula->observacao_final)
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $matricula->observacao_final }}</p>
                                        @endif
                                    </div>
                                    <div class="flex gap-3">
                                        @if (!$finalizado)
                                            <button wire:click="abrirFinalizacao({{ $matricula->id }})" class="font-semibold text-blue-600">Finalizar</button>
                                        @else
                                            <button wire:click="reabrirAluno({{ $matricula->id }})" wire:confirm="Reabrir este aluno?" class="font-semibold text-amber-600">Reabrir</button>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="py-8 text-center text-gray-500">Esta turma ainda não possui alunos.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($modalFinalizacao)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Resultado final do aluno</h3>
                <div class="mt-5 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Resultado</label>
                        <select wire:model="resultado_final" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Selecione</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="reprovado">Reprovado</option>
                            <option value="desistente">Desistente</option>
                        </select>
                        @error('resultado_final')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Observação</label>
                        <textarea wire:model="observacao_final" rows="4" maxlength="1000" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>
                        @error('observacao_final')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="fecharFinalizacao" class="rounded-lg border border-gray-300 px-4 py-2">Cancelar</button>
                    <button wire:click="finalizarAluno" class="rounded-lg bg-red-700 px-4 py-2 font-semibold text-white">Salvar resultado</button>
                </div>
            </div>
        </div>
    @endif
</div>
