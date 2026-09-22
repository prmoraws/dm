@section('title', 'Alunos do Curso UNP')

<x-slot name="header">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Curso UNP — Alunos</h2>
</x-slot>

<div>
    <div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-900 sm:px-6">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session()->has('message'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('message') }}
                </div>
            @endif

            <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                <div class="grid gap-4 lg:grid-cols-[1fr_260px_220px]">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pesquisar aluno</label>
                        <input wire:model.live.debounce.400ms="search" type="search"
                            placeholder="Nome, celular ou igreja"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Turma</label>
                        <select wire:model.live="turmaFiltro" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Todas as turmas</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}">{{ $turma->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Situação</label>
                        <select wire:model.live="situacaoFiltro" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">Todas</option>
                            <option value="matriculado">Matriculado</option>
                            <option value="cursando">Cursando</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="reprovado">Reprovado</option>
                            <option value="desistente">Desistente</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/60">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Aluno</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Turma</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Situação</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-gray-500">Frequência</th>
                                <th class="px-5 py-3 text-right text-xs font-bold uppercase text-gray-500">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($alunos as $matricula)
                                @php
                                    $frequencia = $matricula->presencas_count > 0
                                        ? round(($matricula->presentes_count / $matricula->presencas_count) * 100)
                                        : 0;
                                @endphp
                                <tr wire:key="aluno-{{ $matricula->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ Storage::disk('public_disk')->url($matricula->captacao->foto) }}"
                                                alt="Foto de {{ $matricula->captacao->nome }}"
                                                class="h-11 w-11 rounded-full object-cover">
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ $matricula->captacao->nome }}</p>
                                                <p class="text-xs text-gray-500">{{ $matricula->captacao->celular }} · {{ $matricula->captacao->igreja?->nome ?? 'Sem igreja' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $matricula->turma->nome }}
                                        <p class="text-xs text-gray-500">{{ $matricula->turma->data_inicio->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            {{ str($matricula->situacao)->replace('_', ' ')->title() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <span class="font-bold">{{ $frequencia }}%</span>
                                        <p class="text-xs text-gray-500">{{ $matricula->presentes_count }}/{{ $matricula->presencas_count }} presenças</p>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm">
                                        <button wire:click="visualizar({{ $matricula->id }})" class="font-semibold text-blue-600">Ver</button>
                                        <button wire:click="editar({{ $matricula->id }})" class="ml-3 font-semibold text-amber-600">Editar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-500">Nenhum aluno encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 p-4 dark:border-gray-700">
                    {{ $alunos->links() }}
                </div>
            </div>
        </div>
    </div>

    @if ($modalVisualizar && $aluno)
        @php
            $total = $aluno->presencas->count();
            $presentes = $aluno->presencas->where('situacao', 'presente')->count();
            $ausentes = $aluno->presencas->where('situacao', 'ausente')->count();
            $justificadas = $aluno->presencas->where('situacao', 'justificada')->count();
            $percentual = $total > 0 ? round(($presentes / $total) * 100) : 0;
        @endphp
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4">
            <div class="mx-auto my-8 max-w-5xl rounded-xl bg-white shadow-2xl dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Ficha do aluno</h3>
                        <p class="text-sm text-gray-500">{{ $aluno->turma->nome }} · {{ str($aluno->situacao)->title() }}</p>
                    </div>
                    <button wire:click="fecharModais" class="text-2xl text-gray-500">&times;</button>
                </div>

                <div class="space-y-6 p-6">
                    <div class="grid gap-6 lg:grid-cols-[160px_1fr]">
                        <img src="{{ Storage::disk('public_disk')->url($aluno->captacao->foto) }}"
                            alt="Foto de {{ $aluno->captacao->nome }}"
                            class="h-40 w-40 rounded-xl object-cover shadow">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div><p class="text-xs font-bold uppercase text-gray-500">Nome</p><p class="font-semibold text-gray-900 dark:text-white">{{ $aluno->captacao->nome }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Celular</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->celular }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Igreja</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->igreja?->nome ?? 'Não informada' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Bloco / região</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->bloco?->nome }} / {{ $aluno->captacao->regiao?->nome }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Batismo nas águas</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->batizado_aguas ? 'Sim' : 'Não' }}{{ $aluno->captacao->data_batismo_aguas ? ' · '.$aluno->captacao->data_batismo_aguas->format('d/m/Y') : '' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Espírito Santo</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->batizado_espirito_santo ? 'Sim' : 'Não' }}{{ $aluno->captacao->data_batismo_espirito_santo ? ' · '.$aluno->captacao->data_batismo_espirito_santo->format('d/m/Y') : '' }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Estado civil</p><p class="text-gray-800 dark:text-gray-200">{{ str($aluno->captacao->estado_civil)->title() }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Ingresso na igreja</p><p class="text-gray-800 dark:text-gray-200">{{ str_pad($aluno->captacao->mes_ingresso_igreja, 2, '0', STR_PAD_LEFT) }}/{{ $aluno->captacao->ano_ingresso_igreja }}</p></div>
                            <div><p class="text-xs font-bold uppercase text-gray-500">Instrutor</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->turma->instrutor?->nome ?? 'Não definido' }}</p></div>
                            <div class="sm:col-span-2 lg:col-span-3"><p class="text-xs font-bold uppercase text-gray-500">Endereço</p><p class="text-gray-800 dark:text-gray-200">{{ $aluno->captacao->endereco_completo }}</p></div>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-4">
                        <div class="rounded-lg bg-blue-50 p-4"><p class="text-xs font-bold text-blue-700">Frequência</p><p class="text-2xl font-bold text-blue-900">{{ $percentual }}%</p></div>
                        <div class="rounded-lg bg-green-50 p-4"><p class="text-xs font-bold text-green-700">Presentes</p><p class="text-2xl font-bold text-green-900">{{ $presentes }}</p></div>
                        <div class="rounded-lg bg-red-50 p-4"><p class="text-xs font-bold text-red-700">Ausentes</p><p class="text-2xl font-bold text-red-900">{{ $ausentes }}</p></div>
                        <div class="rounded-lg bg-amber-50 p-4"><p class="text-xs font-bold text-amber-700">Justificadas</p><p class="text-2xl font-bold text-amber-900">{{ $justificadas }}</p></div>
                    </div>

                    <section>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">Histórico de presenças</h4>
                        <div class="mt-3 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-4 py-3 text-left">Data</th><th class="px-4 py-3 text-left">Situação</th><th class="px-4 py-3 text-left">Observação</th></tr></thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse ($aluno->presencas as $presenca)
                                        <tr><td class="px-4 py-3">{{ $presenca->data_aula->format('d/m/Y') }}</td><td class="px-4 py-3 font-semibold">{{ str($presenca->situacao)->title() }}</td><td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $presenca->observacao ?: '—' }}</td></tr>
                                    @empty
                                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Nenhuma chamada registrada.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    @if ($aluno->observacao_final)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <p class="text-xs font-bold uppercase text-gray-500">Observação final</p>
                            <p class="mt-1 text-gray-800 dark:text-gray-200">{{ $aluno->observacao_final }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-gray-200 p-6 dark:border-gray-700">
                    <a href="{{ route('curso-unp.acompanhamento') }}" class="rounded-lg border border-gray-300 px-4 py-2 font-semibold">Abrir chamada</a>
                    <button wire:click="editar({{ $aluno->id }})" class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white">Editar aluno</button>
                    <button wire:click="fecharModais" class="rounded-lg bg-gray-700 px-4 py-2 font-semibold text-white">Fechar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($modalEditar)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4">
            <div class="mx-auto my-8 max-w-4xl rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Editar dados do aluno</h3>
                    <button wire:click="fecharModais" class="text-2xl text-gray-500">&times;</button>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div><label class="text-sm font-semibold">Nome</label><input wire:model="nome" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('nome')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Celular</label><input wire:model="celular" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('celular')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Bloco</label><select wire:model.live="bloco_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Selecione</option>@foreach($blocos as $bloco)<option value="{{ $bloco->id }}">{{ $bloco->nome }}</option>@endforeach</select>@error('bloco_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Região</label><select wire:model.live="regiao_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Selecione</option>@foreach($regioes as $regiao)<option value="{{ $regiao->id }}">{{ $regiao->nome }}</option>@endforeach</select>@error('regiao_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Igreja</label><select wire:model="igreja_id" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Selecione</option>@foreach($igrejas as $igreja)<option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>@endforeach</select>@error('igreja_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Estado civil</label><select wire:model.live="estado_civil" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Selecione</option><option value="solteiro">Solteiro</option><option value="casado">Casado</option><option value="viuvo">Viúvo</option><option value="divorciado">Divorciado</option><option value="namorando">Namorando</option></select>@error('estado_civil')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Batizado nas águas?</label><select wire:model.live="batizado_aguas" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="0">Não</option><option value="1">Sim</option></select>@error('batizado_aguas')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    @if ($batizado_aguas)<div><label class="text-sm font-semibold">Data do batismo nas águas</label><input type="date" wire:model="data_batismo_aguas" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('data_batismo_aguas')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>@endif
                    <div><label class="text-sm font-semibold">Batizado no Espírito Santo?</label><select wire:model.live="batizado_espirito_santo" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"><option value="0">Não</option><option value="1">Sim</option></select>@error('batizado_espirito_santo')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    @if ($batizado_espirito_santo)<div><label class="text-sm font-semibold">Data do batismo no Espírito Santo</label><input type="date" wire:model="data_batismo_espirito_santo" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('data_batismo_espirito_santo')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>@endif
                    @if ($estado_civil === 'casado')
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="casado_civil" class="rounded border-gray-300"> Casado no civil</label>
                        <label class="flex items-center gap-2"><input type="checkbox" wire:model="casado_igreja" class="rounded border-gray-300"> Casado na igreja</label>
                    @endif
                    <div><label class="text-sm font-semibold">Mês de ingresso na igreja</label><input type="number" min="1" max="12" wire:model="mes_ingresso_igreja" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('mes_ingresso_igreja')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div><label class="text-sm font-semibold">Ano de ingresso na igreja</label><input type="number" min="1900" max="{{ now()->year }}" wire:model="ano_ingresso_igreja" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">@error('ano_ingresso_igreja')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label class="text-sm font-semibold">Endereço completo</label><textarea wire:model="endereco_completo" rows="3" class="mt-1 w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white"></textarea>@error('endereco_completo')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="fecharModais" class="rounded-lg border border-gray-300 px-4 py-2">Cancelar</button>
                    <button wire:click="salvar" class="rounded-lg bg-red-700 px-5 py-2 font-semibold text-white">Salvar alterações</button>
                </div>
            </div>
        </div>
    @endif
</div>
