@section('title', 'Dashboard TDA')

<div class="min-h-screen bg-gray-100 px-4 py-8 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/tda/tda-logo.svg') }}" alt="TDA" class="h-10 w-20 object-contain">
            <div>
                <h2 class="text-2xl font-bold">Dashboard TDA</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Terapia do Amor — visão estadual</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-3 rounded-2xl bg-white p-5 shadow-sm dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-bold">Painel estadual de voluntários</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Os números consideram as fichas aprovadas em Cadastro TDA.</p>
            </div>
            @php($mensagem = rawurlencode('Olá! Faça seu cadastro como voluntário da Terapia do Amor: '.route('captacao.tda.create')))
            <a href="https://wa.me/?text={{ $mensagem }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-5 py-3 font-bold text-white shadow hover:bg-green-700">
                Compartilhar cadastro no WhatsApp
            </a>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl bg-gradient-to-br from-red-700 to-red-900 p-6 text-white shadow-lg sm:col-span-2">
                <p class="text-sm font-semibold uppercase tracking-wider text-red-100">Fichas aprovadas no estado</p>
                <p class="mt-2 text-5xl font-black">{{ number_format($total, 0, ',', '.') }}</p>
                <p class="mt-3 text-sm text-red-100">Total consolidado da tabela de cadastros TDA.</p>
            </article>
            <article class="rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-900">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Aguardando conferência</p>
                <p class="mt-2 text-4xl font-black text-amber-600">{{ number_format($pendentes, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-900">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Com Espírito Santo</p>
                @php($espirito = $destaques->firstWhere('nome', 'Com Espírito Santo')['total'] ?? 0)
                <p class="mt-2 text-4xl font-black text-blue-600">{{ number_format($espirito, 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-gray-500">{{ $total ? number_format(($espirito / $total) * 100, 1, ',', '.') : '0,0' }}% das fichas</p>
            </article>
        </section>

        <section>
            <h3 class="mb-3 text-lg font-bold">Perfil dos voluntários</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($destaques->where('nome', '!=', 'Com Espírito Santo') as $item)
                    <article class="rounded-2xl bg-white p-5 shadow-sm dark:bg-gray-900">
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $item['nome'] }}</p>
                        <div class="mt-2 flex items-end justify-between gap-3">
                            <p class="text-3xl font-black">{{ number_format($item['total'], 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">{{ $total ? number_format(($item['total'] / $total) * 100, 1, ',', '.') : '0,0' }}%</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"><div class="h-full rounded-full bg-red-700" style="width:{{ $total ? min(100, ($item['total'] / $total) * 100) : 0 }}%"></div></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-bold">Dados espirituais — condição atual</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($condicoes as $item)
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <div class="flex justify-between gap-3"><span class="font-semibold">{{ $item['nome'] }}</span><strong>{{ number_format($item['total'], 0, ',', '.') }}</strong></div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"><div class="h-full bg-blue-600" style="width:{{ $total ? min(100, ($item['total'] / $total) * 100) : 0 }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </section>

        @php($tabelas = [
            ['titulo' => 'Quantidade por bloco', 'dados' => $blocos, 'colunas' => ['Bloco' => 'nome']],
            ['titulo' => 'Quantidade por região', 'dados' => $regioes, 'colunas' => ['Região' => 'nome', 'Bloco' => 'bloco']],
            ['titulo' => 'Quantidade por igreja', 'dados' => $igrejas, 'colunas' => ['Igreja' => 'nome', 'Região' => 'regiao', 'Bloco' => 'bloco']],
        ])
        @foreach($tabelas as $tabela)
            <section x-data="{ aberto: {{ $loop->first ? 'true' : 'false' }} }" class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-gray-900">
                <button type="button" @click="aberto = !aberto" class="flex w-full items-center justify-between p-5 text-left">
                    <span class="text-lg font-bold">{{ $tabela['titulo'] }}</span><span class="text-sm text-gray-500" x-text="aberto ? 'Recolher' : 'Exibir'"></span>
                </button>
                <div x-show="aberto" class="overflow-x-auto border-t border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-left uppercase text-gray-600 dark:bg-gray-800 dark:text-gray-300"><tr>@foreach($tabela['colunas'] as $rotulo => $campo)<th class="px-5 py-3">{{ $rotulo }}</th>@endforeach<th class="px-5 py-3 text-right">Quantidade</th></tr></thead>
                        <tbody>@forelse($tabela['dados'] as $linha)<tr class="border-t border-gray-100 dark:border-gray-800">@foreach($tabela['colunas'] as $campo)<td class="px-5 py-3">{{ data_get($linha, $campo, '—') }}</td>@endforeach<td class="px-5 py-3 text-right font-bold">{{ number_format($linha->total, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="{{ count($tabela['colunas']) + 1 }}" class="p-8 text-center text-gray-500">Nenhum cadastro encontrado.</td></tr>@endforelse</tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>
</div>
