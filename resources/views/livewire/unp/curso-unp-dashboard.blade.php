@section('title', 'Dashboard do Curso UNP')

<x-slot name="header">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Curso Preparatório de Voluntários</h2>
</x-slot>

<div class="min-h-screen bg-gray-100 px-4 py-8 dark:bg-gray-900 sm:px-6">
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Captações', 'valor' => $totalCaptacoes, 'detalhe' => $captacoesPendentes.' pendentes', 'cor' => 'red'],
                ['label' => 'Turmas', 'valor' => $totalTurmas, 'detalhe' => $turmasAtivas.' ativas', 'cor' => 'blue'],
                ['label' => 'Matrículas', 'valor' => $totalMatriculas, 'detalhe' => ($situacoes['cursando'] ?? 0).' cursando', 'cor' => 'green'],
                ['label' => 'Presentes hoje', 'valor' => $presentesHoje, 'detalhe' => 'registros de presença', 'cor' => 'amber'],
            ] as $card)
                <div class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $card['valor'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $card['detalhe'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Resultados dos alunos</h3>
                    <a href="{{ route('curso-unp.acompanhamento') }}" class="text-sm font-semibold text-red-700">Abrir acompanhamento</a>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach (['matriculado' => 'Matriculados', 'cursando' => 'Cursando', 'aprovado' => 'Aprovados', 'reprovado' => 'Reprovados', 'desistente' => 'Desistentes'] as $chave => $rotulo)
                        <div class="rounded-lg bg-gray-50 p-4 text-center dark:bg-gray-700">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $situacoes[$chave] ?? 0 }}</p>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-300">{{ $rotulo }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Turmas recentes</h3>
                    <a href="{{ route('curso-unp.turmas') }}" class="text-sm font-semibold text-red-700">Gerenciar</a>
                </div>
                <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($turmasRecentes as $turma)
                        <div class="flex items-center justify-between gap-3 py-3">
                            <div><p class="font-semibold text-gray-900 dark:text-white">{{ $turma->nome }}</p><p class="text-xs text-gray-500">{{ $turma->data_inicio->format('d/m/Y') }} · {{ $turma->local }}</p></div>
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $turma->matriculas_count }} alunos</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-gray-500">Nenhuma turma cadastrada.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl bg-white p-5 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Captações recentes</h3>
                <a href="{{ route('curso-unp.captacoes') }}" class="text-sm font-semibold text-red-700">Analisar captações</a>
            </div>
            <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($captacoesRecentes as $captacao)
                    <div class="flex flex-col justify-between gap-2 py-3 sm:flex-row sm:items-center">
                        <div><p class="font-semibold text-gray-900 dark:text-white">{{ $captacao->nome }}</p><p class="text-xs text-gray-500">{{ $captacao->igreja?->nome ?? 'Sem igreja' }} · {{ $captacao->created_at->format('d/m/Y H:i') }}</p></div>
                        <span class="w-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ str($captacao->status)->title() }}</span>
                    </div>
                @empty
                    <p class="py-6 text-center text-gray-500">Nenhuma captação recebida.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
