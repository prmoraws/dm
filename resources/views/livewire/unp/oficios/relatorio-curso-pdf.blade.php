<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ofício de Curso</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2cm 3cm;
            /* Margens: topo, direita, baixo, esquerda */
            font-family: 'Times New Roman', Times, serif;
        }

        body {
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
        }

        header {
            width: 100%;
            text-align: right;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        header img {
            width: 150px;
        }

        .coordenadoria {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .dados-curso {
            margin-bottom: 20px;
        }

        .dados-curso p {
            margin: 0;
            padding: 2px 0;
        }

        .dados-curso .underline {
            text-decoration: underline;
            font-weight: bold;
        }

        .paragrafo {
            text-indent: 4em;
            margin-bottom: 20px;
        }

        .lista {
            margin-left: 6em;
            font-weight: bold;
        }

        .assinatura {
            text-align: center;
            margin-top: 80px;
        }

        .assinatura img {
            height: 60px;
        }

        .assinatura-linha {
            border-top: 1px solid black;
            display: inline-block;
            width: 300px;
            margin-top: 5px;
        }

        .assinatura p {
            margin: 2px 0;
        }

        footer {
            position: fixed;
            bottom: -50px;
            /* Ajuste para descer o rodapé */
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10pt;
        }

        .footer-line {
            border-top: 2px solid #000;
            margin: 0 auto 5px;
            width: 80%;
        }
    </style>
</head>

<body>
    <header>
        <img src="{{ public_path('formaturas/logo-unp.png') }}" alt="Logo UNP">
    </header>

    <main>
        <p class="coordenadoria">Coordenadoria de Evangelização Estadual nas Unidades Prisionais</p>

        <div class="dados-curso">
            <p>{{ $dados->numero_oficio }}</p>
            <p>{{ $dados->data_formatada }}</p>
            <br>
            <p>{{ $dados->destinatario }}</p>
            <p>{{ $dados->diretor_formatado }}</p>
        </div>

        <p class="underline">ASSUNTO: SOLICITAÇÃO DE INÍCIO DE CURSO</p>

        <p class="paragrafo">{{ $dados->paragrafo_principal }}</p>

        <div class="lista">
            <p class="underline">Instrutor será:</p>
            <ul>
                <li>{{ $dados->curso->instrutor->nome ?? 'N/A' }}</li>
                <li>RG: {{ $dados->curso->instrutor->rg ?? 'N/A' }}</li>
                <li>CPF: {{ $dados->curso->instrutor->cpf ?? 'N/A' }}</li>
            </ul>
        </div>

        @if ($dados->material)
            <div class="lista" style="margin-top: 20px;">
                <p class="underline">Material a ser usado:</p>
                <p style="font-style: italic;">{{ $dados->material }}</p>
            </div>
        @endif

        <p class="paragrafo" style="margin-top: 20px;">
            Cumpre salientar nossa Instituição continua comprometida a corroborar com os cuidados necessários em que lhe
            compete, contribuindo e atendendo as orientações e determinações da Unidade. De plano, registra e reitera
            protestos de elevada estima e consideração. Que o Senhor Deus os abençoe!
        </p>

        <div class="assinatura">
            <p>Atenciosamente,</p>
            <img src="{{ public_path('formaturas/assinatura-pastor.png') }}" alt="Assinatura">
            <div class="assinatura-linha"></div>
            <p style="font-weight: bold;">Pastor Pedro Paulo Dos Santos</p>
            <p style="font-size: 10pt;">Coordenador da UNP no Estado da Bahia</p>
            <p style="font-size: 10pt;">Contato e WhatsApp (11)95857-9899 | <span
                    style="color: blue; text-decoration: underline;">E-mail: pedropasantos@universal.org</span></p>
        </div>
    </main>

    <footer>
        <div class="footer-line"></div>
        <p style="font-size: 14pt; font-weight: bold; color: #002B7F;">UNIVERSAL NOS <span
                style="color: #CC0000;">PRESÍDIOS</span></p>
        <p>Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA 40280-000</p>
    </footer>

</body>

</html>
