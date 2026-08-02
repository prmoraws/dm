<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111
        }

        .page {
            position: relative;
            width: 595.28pt;
            height: 841.89pt;
            overflow: hidden;
            page-break-after: always
        }

        .page:last-child {
            page-break-after: auto
        }

        .base {
            position: absolute;
            inset: 0;
            width: 595.28pt;
            height: 841.89pt
        }

        .v {
            position: absolute;
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 12pt;
            white-space: nowrap;
            overflow: hidden
        }

        .photo {
            position: absolute;
            left: 474pt;
            top: 36pt;
            width: 85pt;
            height: 113pt;
            object-fit: cover
        }

        .mark {
            position: absolute;
            font: bold 9pt DejaVu Sans, sans-serif;
            line-height: 10pt;
            text-align: center;
            width: 12pt;
            height: 12pt
        }

        .p1 .igreja {
            left: 72pt;
            top: 88pt;
            width: 178pt
        }

        .p1 .bloco {
            left: 292pt;
            top: 88pt;
            width: 172pt
        }

        .p1 .regiao {
            left: 78pt;
            top: 117pt;
            width: 172pt
        }

        .p1 .estado {
            left: 297pt;
            top: 117pt;
            width: 167pt
        }

        .p1 .ingresso {
            left: 209pt;
            top: 155pt;
            width: 115pt
        }

        .p1 .funcao {
            left: 132pt;
            top: 184pt;
            width: 427pt
        }

        .p1 .nome {
            left: 74pt;
            top: 269pt;
            width: 483pt
        }

        .p1 .nascimento {
            left: 145pt;
            top: 298pt;
            width: 143pt
        }

        .p1 .civil {
            left: 354pt;
            top: 298pt;
            width: 150pt
        }

        .p1 .rg {
            left: 95pt;
            top: 327pt;
            width: 157pt
        }

        .p1 .cpf {
            left: 303pt;
            top: 327pt;
            width: 166pt
        }

        .p1 .celular {
            left: 136pt;
            top: 356pt;
            width: 151pt
        }

        .p1 .facebook {
            left: 93pt;
            top: 385pt;
            width: 183pt
        }

        .p1 .instagram {
            left: 344pt;
            top: 385pt;
            width: 211pt
        }

        .p1 .endereco {
            left: 92pt;
            top: 414pt;
            width: 402pt
        }

        .p1 .numero {
            left: 523pt;
            top: 414pt;
            width: 34pt
        }

        .p1 .cep {
            left: 63pt;
            top: 443pt;
            width: 82pt
        }

        .p1 .bairro {
            left: 184pt;
            top: 443pt;
            width: 104pt
        }

        .p1 .cidade {
            left: 335pt;
            top: 443pt;
            width: 168pt
        }

        .p1 .uf {
            left: 531pt;
            top: 443pt;
            width: 25pt
        }

        .p1 .email {
            left: 76pt;
            top: 472pt;
            width: 462pt
        }

        .p1 .escolaridade {
            left: 149pt;
            top: 501pt;
            width: 176pt
        }

        .p1 .profissao {
            left: 385pt;
            top: 501pt;
            width: 158pt
        }

        .p1 .qtd {
            left: 297pt;
            top: 531pt;
            width: 48pt
        }

        .p1 .idades {
            left: 442pt;
            top: 531pt;
            width: 113pt
        }

        .p1 .emergencia_nome {
            left: 74pt;
            top: 597pt;
            width: 483pt
        }

        .p1 .emergencia_celular {
            left: 136pt;
            top: 627pt;
            width: 151pt
        }

        .p1 .emergencia_facebook {
            left: 93pt;
            top: 655pt;
            width: 195pt
        }

        .p1 .emergencia_instagram {
            left: 354pt;
            top: 655pt;
            width: 190pt
        }

        .p2 .inicio {
            left: 115pt;
            top: 142pt;
            width: 102pt
        }

        .p2 .data_aguas {
            left: 366pt;
            top: 171pt;
            width: 103pt
        }

        .p2 .data_espirito {
            left: 366pt;
            top: 200pt;
            width: 103pt
        }

        .p2 .grad_colaborador {
            left: 232pt;
            top: 606pt;
            width: 95pt
        }

        .p2 .grad_obreiro {
            left: 232pt;
            top: 635pt;
            width: 95pt
        }

        .p2 .grad_levita {
            left: 232pt;
            top: 664pt;
            width: 95pt
        }

        .p1 .v {
            transform: translate(3pt, -3pt)
        }

        .p1 .emergencia_nome,
        .p1 .emergencia_celular,
        .p1 .emergencia_facebook,
        .p1 .emergencia_instagram {
            transform: translate(3pt, -5pt)
        }

        .mark {
            width: 12pt;
            height: 12pt;
            font-family: Arial, sans-serif !important;
            font-size: 7pt !important;
            font-weight: bold !important;
            line-height: 12pt !important;
            text-align: center !important;
            vertical-align: middle !important;
            overflow: hidden
        }
    </style>
</head>

<body>
    @php
        $f = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '';
        $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $xs = [40, 90, 145, 202, 254, 307, 361];
        $xsReunioes = [39, 90, 146, 201, 253, 305, 360];
    @endphp
    <div class="page p1"><img class="base" src="{{ $pagina1DataUri }}">
        @if ($fotoDataUri)
            <img class="photo" src="{{ $fotoDataUri }}">
        @endif
        <span class="v igreja">{{ $pessoa->igreja->nome ?? '' }}</span><span
            class="v bloco">{{ $pessoa->bloco->nome ?? '' }}</span><span
            class="v regiao">{{ $pessoa->regiao->nome ?? '' }}</span><span
            class="v estado">{{ $pessoa->estado->nome ?? '' }}</span><span
            class="v ingresso">{{ $f($pessoa->data_ingresso_grupo) }}</span><span
            class="v funcao">{{ $pessoa->funcao_grupo }}</span>
        <span class="v nome">{{ $pessoa->nome }}</span><span
            class="v nascimento">{{ $f($pessoa->data_nascimento) }}</span><span
            class="v civil">{{ $pessoa->estado_civil }}</span><span class="v rg">{{ $pessoa->rg }}</span><span
            class="v cpf">{{ $pessoa->cpf }}</span><span class="v celular">{{ $pessoa->celular }}</span><span
            class="v facebook">{{ $pessoa->facebook }}</span><span
            class="v instagram">{{ $pessoa->instagram }}</span><span
            class="v endereco">{{ $pessoa->endereco }}</span><span class="v numero">{{ $pessoa->numero }}</span><span
            class="v cep">{{ $pessoa->cep }}</span><span class="v bairro">{{ $pessoa->bairro }}</span><span
            class="v cidade">{{ $pessoa->cidade->nome ?? '' }}</span><span
            class="v uf">{{ $pessoa->estado->uf ?? '' }}</span><span class="v email">{{ $pessoa->email }}</span><span
            class="v escolaridade">{{ $pessoa->escolaridade }}</span><span
            class="v profissao">{{ $pessoa->profissao }}</span><span class="mark"
            style="left:107pt;top:528pt">{{ $pessoa->tem_filhos ? 'X' : '' }}</span><span class="mark"
            style="left:156pt;top:528pt">{{ !$pessoa->tem_filhos ? 'X' : '' }}</span><span
            class="v qtd">{{ $pessoa->quantidade_filhos }}</span><span
            class="v idades">{{ $pessoa->idade_filhos }}</span><span
            class="v emergencia_nome">{{ $pessoa->emergencia_nome }}</span><span
            class="v emergencia_celular">{{ $pessoa->emergencia_celular }}</span><span
            class="v emergencia_facebook">{{ $pessoa->emergencia_facebook }}</span><span
            class="v emergencia_instagram">{{ $pessoa->emergencia_instagram }}</span>
    </div>
    <div class="page p2"><img class="base" src="{{ $pagina2DataUri }}">
        @foreach (['membro' => 39, 'cpo' => 113, 'colaborador' => 166, 'obreiro' => 259, 'levita' => 329, 'auxiliar' => 388] as $condicao => $x)
            @if ($pessoa->condicao_atual === $condicao)
                <span class="mark" style="left:{{ $x }}pt;top:119pt">X</span>
            @endif
        @endforeach
        <span class="v inicio">{{ $f($pessoa->inicio_iurd) }}</span><span class="mark"
            style="left:197pt;top:178pt">{{ $pessoa->batizado_aguas ? 'X' : '' }}</span><span class="mark"
            style="left:248pt;top:178pt">{{ !$pessoa->batizado_aguas ? 'X' : '' }}</span><span
            class="v data_aguas">{{ $f($pessoa->data_batismo_aguas) }}</span><span class="mark"
            style="left:197pt;top:205pt">{{ $pessoa->batizado_espirito_santo ? 'X' : '' }}</span><span class="mark"
            style="left:248pt;top:205pt">{{ !$pessoa->batizado_espirito_santo ? 'X' : '' }}</span><span
            class="v data_espirito">{{ $f($pessoa->data_batismo_espirito_santo) }}</span><span class="mark"
            style="left:119pt;top:229pt">{{ $pessoa->ja_se_afastou ? 'X' : '' }}</span><span class="mark"
            style="left:171pt;top:229pt">{{ !$pessoa->ja_se_afastou ? 'X' : '' }}</span>
        @foreach ($dias as $i => $dia)
            @if (in_array($dia, $pessoa->dias_reunioes ?? []))
                <span class="mark" style="left:{{ $xsReunioes[$i] }}pt;top:285pt">X</span>
                @endif @if (in_array($dia, $pessoa->dias_evangelizacao ?? []))
                    <span class="mark" style="left:{{ $xs[$i] }}pt;top:344pt">X</span>
                    @endif @if (in_array($dia, $pessoa->dias_trabalho_reuniao ?? []))
                        <span class="mark" style="left:{{ $xs[$i] }}pt;top:717pt">X</span>
                    @endif
                @endforeach
                <span class="mark"
                    style="left:296pt;top:422pt">{{ $pessoa->godllywood_autoajuda ? 'X' : '' }}</span><span class="mark"
                    style="left:346pt;top:421pt">{{ !$pessoa->godllywood_autoajuda ? 'X' : '' }}</span><span class="mark"
                    style="left:261pt;top:444pt">{{ $pessoa->meditacao_univer ? 'X' : '' }}</span><span class="mark"
                    style="left:309pt;top:444pt">{{ !$pessoa->meditacao_univer ? 'X' : '' }}</span><span class="mark"
                    style="left:234pt;top:504pt">{{ $pessoa->intellimen_reunioes ? 'X' : '' }}</span><span class="mark"
                    style="left:283pt;top:504pt">{{ !$pessoa->intellimen_reunioes ? 'X' : '' }}</span><span class="mark"
                    style="left:244pt;top:528pt">{{ $pessoa->intellimen_desafios ? 'X' : '' }}</span><span class="mark"
                    style="left:292pt;top:528pt">{{ !$pessoa->intellimen_desafios ? 'X' : '' }}</span>
                <span class="mark" style="left:39pt;top:613pt">{{ $pessoa->colaborador ? 'X' : '' }}</span><span
                    class="v grad_colaborador">{{ $f($pessoa->data_graduacao_colaborador) }}</span><span class="mark"
                    style="left:39pt;top:641pt">{{ $pessoa->obreiro ? 'X' : '' }}</span><span
                    class="v grad_obreiro">{{ $f($pessoa->data_graduacao_obreiro) }}</span><span class="mark"
                    style="left:39pt;top:671pt">{{ $pessoa->levita ? 'X' : '' }}</span><span
                    class="v grad_levita">{{ $f($pessoa->data_graduacao_levita) }}</span>
    </div>
</body>

</html>
