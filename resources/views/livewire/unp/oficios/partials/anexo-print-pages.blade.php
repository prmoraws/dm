@php($paginasAnexo = \App\Support\Unp\PaginadorAnexo::paginas($selectedAnexo->listas_formatadas ?? []))
@foreach($paginasAnexo as $paginaAnexo)
    <section class="anexo-print-page" data-anexo-page="{{ $paginaAnexo['numero'] }}" data-anexo-names="{{ $paginaAnexo['quantidade'] }}">
        <header class="anexo-print-header">
            <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
        </header>
        <p class="anexo-print-coordenadoria">Coordenadoria de Evangelização Estadual nas Unidades Prisionais</p>
        <main class="anexo-print-main">
            <h2 class="anexo-print-title">{{ mb_strtoupper($selectedAnexo->titulo, 'UTF-8') }}</h2>
            @foreach($paginaAnexo['secoes'] as $secaoAnexo)
                <div class="anexo-print-section">
                    <h3 class="anexo-print-category">{{ $secaoAnexo['titulo'] }}{{ $secaoAnexo['continuacao'] ? ' (continuação)' : '' }}</h3>
                    @foreach($secaoAnexo['convidados'] as $convidado)
                        <p class="anexo-print-name">{{ mb_strtoupper($convidado->nome, 'UTF-8') }} - CPF: {{ $convidado->cpf }}</p>
                    @endforeach
                </div>
            @endforeach
            @if(!$paginaAnexo['quantidade'])<p>Nenhum convidado nesta lista.</p>@endif
        </main>
        <footer class="anexo-print-footer">
            <p class="anexo-print-banner">UNIVERSAL NOS <span>PRESÍDIOS</span></p>
            <p>Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA 40280-000</p>
            <p class="anexo-print-number">Página {{ $paginaAnexo['numero'] }} de {{ count($paginasAnexo) }}</p>
        </footer>
    </section>
@endforeach
