<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Ofício de Credencial</title>
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
            height: 25px; /* Altura fixa para o cabeçalho */
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
            padding-top: 10px;
            /* pt-6 */
            flex-grow: 1;
            /* flex-grow */
            display: flex;
            /* flex */
            flex-direction: column;
            /* flex-col */
            overflow: hidden;
            /* Crucial para evitar quebra de página */
        }

        .dados {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 10px;
        }

        .texto-dados {
            text-align: left;
            font-size: 12px;
            line-height: 1.4;
            flex: 1;
        }

        .texto-dados p {
            margin: 5px 0;
            padding-left: 15px;
        }

        .caixa-confirmacao {
            width: 200px;
            border: 1px solid #dc2626;
            padding: 8px;
            font-size: 10px;
            color: #dc2626;
            font-weight: bold;
            flex-shrink: 0;
        }

        .caixa-confirmacao p {
            margin: 8px 0;
            line-height: 1.3;
        }

        @media print {
            .caixa-confirmacao {
                position: absolute;
                right: 0;
                top: 90px;
                /* Ajuste conforme a altura do seu header e título */
                margin-left: 0;
                /* Override ml-8 */
            }
        }

        .corpo {
            margin-top: 24px;
            /* mt-6 */
            font-size: 14px;
            /* text-sm */
            text-align: justify;
            line-height: 1.5;
            /* leading-snug (1.375) - ajustado */
            padding: 0 20px;
            /* px-5 */
            flex-grow: 1;
            overflow: hidden;
        }

        .corpo>p {
            text-indent: 32px;
            /* indent-8 */
            margin-bottom: 16px;
            /* mb-4 */
            /* padding-bottom: 40px; pb-10 */
            /* Ajustar no HTML se necessário */
        }

        .credenciado-info {
            padding-left: 32px;
            /* pl-8 */
            font-family: monospace;
            /* font-serif - mantido monospace para consistência com RG/CPF */
            font-size: 14px;
            /* text-base */
            font-weight: bold;
            /* font-semibold */
            margin: 25px 0;
            /* my-6 */
            line-height: 1.5;
            /* Ajustado */
            /* padding-bottom: 40px; pb-10 */
            /* Ajustar no HTML se necessário */
        }

        .credenciado-info p {
            margin: 0;
            /* Remover margin-top/bottom extras */
            line-height: 1.5;
        }

        .assinatura {
            margin-top: 20px; /* Reduzido */
            padding-bottom: 5px;
            text-align: center;
            flex: none; /* Remove flex-grow */
        }

        .assinatura-img-container {
            display: inline-block;
            margin-top: 5px; /* Reduzido */
        }

        .assinatura img {
            height: 60px; /* Reduzido */
            display: block;
            margin: 0 auto;
        }

        .assinatura .linha {
            border-top: 1px solid #000;
            margin-top: 3px;
            width: 100%;
        }

        .assinatura .assinatura-texto p {
            margin: 2px 0;
            font-size: 11px;
        }

        footer {
            text-align: center;
            opacity: 0.75;
            border-top: 4px solid #172554;
            padding: 5px 0;
            font-size: 10px;
            flex-shrink: 0; /* Impede que o footer cresça */
        }

        .footer-banner {
            border-top: 1px solid #172554;
            border-bottom: 1px solid #172554;
            margin: 5px auto;
            display: inline-block;
            padding: 2px 15px;
            font-size: 14px;
            font-weight: bold;
            color: #172554;
        }

        .footer-banner span {
            color: #dc2626;
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
        <button onclick="window.print()">Imprimir Ofício</button>
    </div>

    <header>
        <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
    </header>

    <p class="title">Coordenadoria de Evangelização Estadual nas Unidades Prisionais</p>

    <main>
        <div class="dados">
            <div class="texto-dados">
                <p><strong>{{ $selectedOficio->numero_oficio }}</strong></p>
                <p style="padding-bottom: 1em;">{{ $selectedOficio->data_formatada }}</p>
                <p><strong>{{ $selectedOficio->destinatario }}</strong></p>
                <p><strong>ASSUNTO: SOLICITAÇÃO DE CREDENCIAL DE ACESSO</strong></p>
                <p><strong>{{ $selectedOficio->diretor_formatado }}</strong></p>
            </div>

            <div class="caixa-confirmacao">
                <p style="text-align: center;">Confirmo recebimento e dou ciência.</p>
                <p>Nome: ____________________________</p>
                <p>Assinatura: _______________________</p>
                <p>Data: _______/ _______/ _________</p>
            </div>
        </div>

        <div class="corpo">
            <p>Com os cumprimentos de estilo, a Universal nos Presídios - UNP, vem, através de seu Coordenador Geral que
                a este subscreve, requerer autorização emissão (renovação) da credencial de acesso para esta unidade, no
                período máximo de 30 dias para que não haja interrupção no trabalho evangelístico.</p>

            <div class="credenciado-info">
                <p>Nome: {{ $selectedOficio->nome }}</p>
                <p>CPF: {{ $selectedOficio->cpf }}</p>
                @if (isset($selectedOficio->rg) && strlen(trim($selectedOficio->rg)) > 0)
    				<p>RG: {{ $selectedOficio->rg }}</p>
				@endif
            </div>

            <p>Cumpre salientar nossa Instituição continua comprometida a corroborar com os cuidados necessários em que
                lhe compete, contribuindo e atendendo as orientações e determinações da Unidade. De plano, registra e
                reitera protestos de elevada estima e consideração. Que o Senhor Deus os abençoe!</p>
        </div>

        <div class="assinatura">
            <p>Atenciosamente,</p>
            <div class="assinatura-img-container">
                <img src="{{ asset('formaturas/assinatura-pastor.png') }}" alt="Assinatura">
                <div class="linha"></div>
                <div class="assinatura-texto">
                    <p class="nome-pastor">Bispo Sérgio Simplício dos Santos</p>
                    <p>Coordenador da UNP no Estado da Bahia</p>
                    <p class="email-contato">Contato e WhatsApp (71)99982-9897 | <span>E-mail:
                            sergsantos@universal.org</span></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p class="footer-banner">UNIVERSAL NOS <span>PRESÍDIOS</span></p>
        <p>Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA 40280-000</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print(); // Aciona a impressão assim que a página é carregada
        });
    </script>
</body>

</html>
