<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dados para Certificados</title>
    <style>
        /* Cores base Tailwind para referência */
        /* blue-950: #172554 */
        /* red-600: #dc2626 */
        /* gray-800: #1f2937 */
        /* gray-900: #111827 */
        /* gray-100: #f3f4f6 */
        /* blue-700: #1d4ed8 */

        @page {
            margin: 5mm 10mm;
            /* Top/Bottom 5mm, Left/Right 10mm */
            size: A4 portrait;
        }

        body {
            background-color: #fff;
            color: #000;
            /* text-black */
            font-family: sans-serif;
            /* font-sans */
            font-size: 14px;
            /* text-sm */
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: calc(297mm - 10mm);
            /* A4 height - (top_margin + bottom_margin) */
            overflow: hidden;
            /* Crucial para evitar quebra de página */
        }

        header {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            border-bottom: 3px solid #172554;
            padding-bottom: 1mm;
            margin-bottom: 1mm;
            opacity: 0.75;
            height: 25px;
            /* Altura fixa para o cabeçalho */
        }

        header img {
            width: 150px;
            height: auto;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin: 0px 0;
            color: #616161;
            padding-left: 10px;
        }

        main {
            padding: 0 15px;
            /* px-10 (40px) - ajustado para 15px se for muito largo */
            flex-grow: 1;
            /* flex-grow */
            display: flex;
            /* flex */
            flex-direction: column;
            /* flex-col */
            overflow: hidden;
            /* Crucial para evitar quebra de página */
        }

        .section-title {
            font-weight: bold;
            /* font-bold */
            text-decoration: underline;
            font-size: 14px;
            /* text-base */
            margin-top: 20px;
            margin-bottom: 10px;
            padding-left: 0;
            /* Removido pl-10 (40px) */
        }

        .data-list {
            line-height: 1.4;
            /* leading-snug (1.375) ou leading-none (1) - ajustado para compactar */
            font-size: 14px;
            /* text-base */
            text-align: left;
            /* text-left */
            padding-top: 16px;
            /* pt-4 */
            margin-top: 0;
            /* space-y-0 */
            flex-shrink: 0;
            /* Não permitir encolher */
        }

        .data-list p {
            margin-top: 0;
            /* space-y-0 */
            margin-bottom: 5px;
            /* Pequeno espaçamento entre os parágrafos */
        }

        .data-list strong {
            font-weight: bold;
        }

        .data-list p:first-child {
            margin-top: 0;
            /* Garante que o primeiro p não tenha margin-top extra */
        }

        .data-list p.uppercase {
            text-transform: uppercase;
            /* uppercase */
        }

        .info-curso {
            margin-top: 16px;
            /* pt-4 */
            padding-left: 40px;
            /* pl-10 */
            white-space: pre-line;
            font-size: 14px;
            /* text-xl - ajustado para text-base */
            line-height: 1.2;
            /* leading-none (1) - ajustado para melhor legibilidade */
            flex-grow: 1;
            /* Permite que ocupe o espaço restante */
            overflow: hidden;
            /* Crucial para evitar quebra de página */
        }

        /* Controles (botões) que não devem aparecer na impressão */
        .controls-container {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f0f0f0;
            border-bottom: 1px solid #ccc;
        }

        .controls-container button {
            background-color: #2563eb;
            /* blue-600 */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .controls-container button:hover {
            background-color: #1d4ed8;
            /* blue-700 */
        }

        /* Esconder os controles na impressão */
        @media print {
            .controls-container {
                display: none;
            }

            @page {
                margin: 0 !important;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    {{-- Controles de Impressão (Visíveis apenas na tela, escondidos na impressão) --}}
    <div class="controls-container">
        <button onclick="window.print()">Imprimir Ofício</button>
    </div>

    <header>
        <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
    </header>

    <p class="title">Coordenadoria de Evangelização Estadual nas Unidades Prisionais</p>

    <main>
        <h3 class="section-title">DADOS DO CURSO</h3>
        <div class="data-list">
            <p><strong>NOME DO PROFESSOR:</strong>
                {{ $selectedDados->curso->instrutor->nome ?? 'N/A' }}</p>
            <p class="uppercase"><strong>NOME DO DIRETOR DA UNIDADE:</strong>
                {{ strtoupper($selectedDados->presidio->diretor ?? 'N/A') }}</p>
            <p class="uppercase"><strong>NOME DA UNIDADE:</strong>
                {{ strtoupper($selectedDados->presidio->nome ?? 'N/A') }}</p>
            <p><strong>NOME DO BISPO RESPONSAVEL DO GRUPO:</strong>
                SÉRGIO SIMPLÍCIO DOS SANTOS</p>
            <p><strong>CURSO:</strong>
                {{ $selectedDados->curso->nome ?? 'N/A' }}</p>
            <p><strong>DATA DE INICIO:</strong>
                {{ \Carbon\Carbon::parse($selectedDados->inicio)->format('d/m/Y') }}</p>
            <p><strong>DATA DE TERMINO:</strong>
                {{ \Carbon\Carbon::parse($selectedDados->fim)->format('d/m/Y') }}</p>
            <p><strong>CARGA HORARIA:</strong>
                {{ $selectedDados->carga }}
            </p>
        </div>

        @if ($selectedDados->informacaoCurso?->informacao)
            <div class="pt-4"> {{-- Este div pode ser uma seção --}}
                <h3 class="section-title">ETAPAS DO CURSO:</h3>
                <div class="info-curso">
                    {{ $selectedDados->informacaoCurso->informacao }}
                </div>
            </div>
        @endif
    </main>

    {{-- Não há footer nesta página específica de "dados-curso-pdf.blade.php" --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aciona a janela de impressão assim que a página é carregada
            window.print();
        });
    </script>
</body>

</html>
