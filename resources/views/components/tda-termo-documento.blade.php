@props(['tipo', 'aceito' => false])

@php($termo = config("tda.termos.{$tipo}"))

<section class="space-y-4">
    <div class="flex flex-col gap-2 rounded-xl border border-gray-200 bg-gray-50 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-800">
        <div>
            <h2 class="font-bold text-gray-900 dark:text-white">{{ $termo['titulo'] }}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">Versão {{ $termo['versao'] }}</p>
        </div>
        @if($aceito)
            <span class="w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-800">Aceito</span>
        @endif
    </div>

    <div class="max-h-[62vh] space-y-3 overflow-y-auto rounded-xl border bg-gray-200 p-2 dark:border-gray-700 dark:bg-gray-900" tabindex="0" aria-label="Documento completo para leitura">
        @foreach($termo['paginas'] as $pagina)
            <img src="{{ asset($pagina) }}" alt="{{ $termo['titulo'] }} — página {{ $loop->iteration }}" class="mx-auto h-auto w-full max-w-3xl bg-white shadow" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
        @endforeach
    </div>

    <p class="text-sm leading-6 text-gray-600 dark:text-gray-300">
        Leia o documento inteiro. O aceite registrará a versão, data e hora, integridade do documento e assinatura final.
    </p>
</section>
