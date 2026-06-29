<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Lista para Certificados</title>
    <style>
        /* Cores base Tailwind para referência */
        /* blue-950: #172554 */
        /* red-600: #dc2626 */
        /* gray-900: #111827 */
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
            font-size: 10pt;
            /* Tamanho da fonte ajustado para tabelas */
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

        /* Título Principal */
        h1 {
            text-align: center;
            font-size: 14pt;
            /* text-xl */
            font-weight: bold;
            /* font-bold */
            text-transform: uppercase;
            /* uppercase */
            margin-bottom: 15px;
            /* mb-4 */
            padding-top: 10px;
            /* Adicionado para espaço do topo */
        }

        /* Tabelas */
        .header-table,
        .main-table {
            width: 100%;
            border-collapse: collapse;
            /* Garante que as bordas se toquem */
            font-size: 9pt;
            /* text-xs */
            page-break-inside: avoid;
            /* Tenta evitar quebra de página dentro da tabela */
        }

        /* Células e cabeçalhos de tabelas */
        .header-table td,
        .main-table th,
        .main-table td {
            border: 1px solid black;
            /* border border-black */
            padding: 3px 5px;
            /* p-1 */
            vertical-align: middle;
            line-height: 1.2;
            /* Para compactar o texto nas células */
        }

        /* Thead da tabela principal */
        .main-table thead th {
            font-weight: bold;
            /* font-bold */
            text-align: center;
            /* text-center */
            background-color: #f3f4f6;
            /* gray-100 */
            color: #4b5563;
            /* gray-600 */
            text-transform: uppercase;
            /* uppercase */
            padding: 5px 6px;
            /* py-3 px-6 */
            border-bottom: 2px solid black;
            /* border-y-2 border-black */
        }

        /* Linhas da tabela principal */
        .main-table tbody tr {
            border-bottom: 1px solid #d1d5db;
            /* border-b border-gray-400 */
        }

        .main-table tbody tr:last-child {
            border-bottom: none;
            /* Remover borda da última linha se for o caso */
        }

        .main-table tbody td {
            height: 16px;
            /* Altura fixa para cada linha, h-6 */
        }

        /* Estilos de texto */
        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        /* --- Estilos para Botões e Mídia de Impressão --- */
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
        <button onclick="window.print()">Imprimir Lista</button>
    </div>

    <h1>Emissão de Certificados</h1>

    <table class="header-table">
        <tbody>
            <tr>
                <td class="font-bold">UNIDADE:</td>
                <td class="uppercase" colspan="2">{{ $selectedLista->curso->presidio->nome ?? 'N/A' }}</td>
                <td class="font-bold">RESPONSÁVEL UNP:</td>
                <td colspan="2">SÉRGIO SIMPLÍCIO DOS SANTOS</td>
            </tr>
            <tr>
                <td class="font-bold">CURSO:</td>
                <td class="uppercase" colspan="2">{{ $selectedLista->curso->nome ?? 'N/A' }}</td>
                <td class="font-bold">PROFISSIONAL:</td>
                <td class="uppercase" colspan="2">{{ $selectedLista->curso->instrutor->nome ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-bold">DATA INÍCIO:</td>
                <td>{{ $selectedLista->curso->inicio ? \Carbon\Carbon::parse($selectedLista->curso->inicio)->format('d/m/Y') : 'N/A' }}
                </td>
                <td class="font-bold">DATA FINAL:</td>
                <td>{{ $selectedLista->curso->fim ? \Carbon\Carbon::parse($selectedLista->curso->fim)->format('d/m/Y') : 'N/A' }}
                </td>
                <td class="font-bold">CARGA HORÁRIA TOTAL (HRS):</td>
                <td>{{ $selectedLista->curso->carga }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">Nº</th>
                <th style="width: 45%;" class="text-left">NOME DO INTERNO (A)</th>
                <th style="width: 15%;">DOC IDENTIFICAÇÃO (RG OU CPF)</th>
                <th style="width: 15%;">CARGA HORÁRIA TOTAL</th>
                <th style="width: 20%;">ASSINATURA:</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedLista->lista_reeducandos as $index => $reeducando)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $reeducando->nome }}</td>
                    <td class="text-center">{{ $reeducando->documento }}</td>
                    <td class="text-center">{{ $selectedLista->curso->carga }}</td>
                    <td></td>
                </tr>
            @endforeach
            {{-- Preenche com linhas vazias até 30, se necessário --}}
            @for ($i = $selectedLista->lista_reeducandos->count(); $i < 30; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print(); // Aciona a impressão assim que a página é carregada
        });
    </script>
</body>

</html>
