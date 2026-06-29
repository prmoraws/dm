<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Voluntário(a) - {{ $voluntario->nome }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS customizado para controle de impressão A4 e margens */
        @page {
            size: A4;
            margin: 1cm;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            /* ALTERAÇÃO 3: Oculta o botão de impressão no PDF final */
            .no-print {
                display: none !important;
            }
        }
        /* Garante que o container principal ocupe o espaço correto dentro das margens */
        body {
            width: 21cm;
            height: 29.7cm;
            position: relative; /* Necessário para posicionar o botão de impressão */
        }
        .page-container {
            width: 19cm; /* 21cm - 2cm (margens) */
            min-height: 27.7cm; /* 29.7cm - 2cm (margens) */
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-white text-black font-sans">
    @php
        // Função para sanitizar dados que podem ser strings JSON mal formatadas
        function sanitize_json_attribute($attribute) {
            if (is_array($attribute)) return $attribute;
            if (!is_string($attribute) || empty($attribute)) return [];
            $decoded = json_decode($attribute, true);
            while (is_string($decoded)) { $decoded = json_decode($decoded, true); }
            return is_array($decoded) ? $decoded : [];
        }

        $batismoArray = sanitize_json_attribute($voluntario->batismo);
        $trabalhoArray = sanitize_json_attribute($voluntario->trabalho);
        $presoArray = sanitize_json_attribute($voluntario->preso);
    @endphp

    {{-- ALTERAÇÃO 3: Botão de impressão adicionado --}}
    <div class="no-print fixed top-4 right-4">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center gap-2 transition-transform transform hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm7-8a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Imprimir
        </button>
    </div>


    <div class="page-container flex flex-col text-xs">

        <div class="w-full flex-grow flex flex-col border border-gray-400">

            <header class="w-full p-1">
                {{-- REFINAMENTO: Alinhamento vertical centralizado --}}
                <div class="flex justify-between items-center">
                    <div class="w-1/4 flex justify-center">
                        <div class="w-[3cm] h-[4cm] border-2 border-black flex items-center justify-center bg-gray-100">
                            @if($voluntario->foto)
                                <img src="{{ asset($voluntario->foto) }}" alt="Foto" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs text-gray-500">FOTO</span>
                            @endif
                        </div>
                    </div>

                    <div class="w-1/2 flex flex-col items-center space-y-2">
                        <div class="flex items-center gap-2 w-full max-w-[8cm]">
                            <span class="font-bold w-[60px] text-right">BLOCO:</span>
                            <div class="border border-gray-400 flex-grow h-[24px] px-2 flex items-center">{{ mb_strtoupper($voluntario->bloco->nome ?? '') }}</div>
                        </div>
                        <div class="flex items-center gap-2 w-full max-w-[8cm]">
                            <span class="font-bold w-[60px] text-right">REGIÃO:</span>
                            <div class="border border-gray-400 flex-grow h-[24px] px-2 flex items-center">{{ mb_strtoupper($voluntario->regiao->nome ?? '') }}</div>
                        </div>
                        <div class="flex items-center gap-2 w-full max-w-[8cm]">
                            <span class="font-bold w-[60px] text-right">IGREJA:</span>
                            <div class="border border-gray-400 flex-grow h-[24px] px-2 flex items-center">{{ mb_strtoupper($voluntario->igreja->nome ?? '') }}</div>
                        </div>
                        <div class="pt-2 flex flex-col items-start space-y-1">
                            @php $cargos = ['Líder', 'Auxiliar', 'Secretária', 'Obreiro(a)', 'Evangelista', 'Levita']; @endphp
                            @foreach($cargos as $cargo)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-4 border border-gray-400 flex items-center justify-center font-bold">
                                        {{-- ALTERAÇÃO 1: Lógica do cargo corrigida para ser sensível a acentos --}}
                                        @php
                                            $baseCargo = mb_strtolower(trim(explode('(', $cargo)[0]));
                                            $voluntarioCargoNome = isset($voluntario->cargo) ? mb_strtolower(trim($voluntario->cargo->nome)) : '';
                                        @endphp
                                        @if(!empty($voluntarioCargoNome) && str_starts_with($voluntarioCargoNome, $baseCargo))
                                            X
                                        @endif
                                    </div>
                                    <span>{{ $cargo }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-1/4 flex justify-center">
                        <img src="{{ asset('assets/images/logo-unp.png') }}" alt="Logo UNP" class="w-[5cm]">
                    </div>
                </div>
            </header>

            <main class="flex-grow">
                <h1 class="text-center text-base font-bold uppercase bg-red-600 text-white py-1 border-y border-gray-400 border-t-[5px] border-indigo-950">Ficha de Voluntário(a)</h1>

                <section class="p-1 space-y-1">
                    <div class="flex gap-1">
                        <div class="border border-gray-400 p-1 flex-grow-[12] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">NOME COMPLETO:</span><span class="truncate">{{ mb_strtoupper($voluntario->nome ?? '') }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow-[5] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">CELULAR:</span><span class="truncate">{{ $voluntario->celular ?? '' }}</span></div>
                    </div>
                    <div class="flex gap-1">
                        {{-- ALTERAÇÃO 2: Formatação do telefone fixo --}}
                        @php
                            $telefoneFixo = $voluntario->telefone ?? '';
                            $telefoneFixoFormatado = $telefoneFixo;
                            if (!empty($telefoneFixo)) {
                                $digitos = preg_replace('/\D/', '', $telefoneFixo);
                                if (strlen($digitos) == 10) {
                                    $telefoneFixoFormatado = '(' . substr($digitos, 0, 2) . ') ' . substr($digitos, 2, 4) . '-' . substr($digitos, 6);
                                }
                            }
                        @endphp
                        <div class="border border-gray-400 p-1 flex-grow-[5] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">TEL. FIXO:</span><span class="truncate">{{ $telefoneFixoFormatado }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow-[12] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">E-MAIL:</span><span class="truncate">{{ $voluntario->email ?? '' }}</span></div>
                    </div>
                    <div class="flex gap-1">
                        <div class="border border-gray-400 p-1 flex-grow-[10] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">ENDEREÇO:</span><span class="truncate">{{ mb_strtoupper($voluntario->endereco ?? '') }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow-[7] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">BAIRRO:</span><span class="truncate">{{ mb_strtoupper($voluntario->bairro ?? '') }}</span></div>
                    </div>
                    <div class="flex gap-1">
                        <div class="border border-gray-400 p-1 flex-grow-[4] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">CEP:</span><span class="truncate">{{ $voluntario->cep ?? '' }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow-[10] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">CIDADE:</span><span class="truncate">{{ mb_strtoupper(optional($voluntario->cidade)->nome ?? '') }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow-[3] flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">ESTADO:</span><span class="truncate">{{ mb_strtoupper(optional(optional($voluntario->cidade)->estado)->uf ?? '') }}</span></div>
                    </div>
                    <div class="flex gap-1">
                        <div class="border border-gray-400 p-1 flex-grow flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">PROFISSÃO:</span><span class="truncate">{{ mb_strtoupper($voluntario->profissao ?? '') }}</span></div>
                        <div class="border border-gray-400 p-1 flex-grow flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">APTIDÕES:</span><span class="truncate">{{ mb_strtoupper($voluntario->aptidoes ?? '') }}</span></div>
                    </div>
                </section>

                <section>
                    <h2 class="text-center text-base font-bold uppercase bg-red-600 text-white py-1 border-y border-gray-400 border-t-[5px] border-indigo-950">Dados Espirituais</h2>
                    <div class="p-1 space-y-1">
                        <div class="flex gap-1">
                            <div class="border border-gray-400 p-1 flex-grow flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">MÊS E ANO DE CONVERSÃO:</span><span>{{ $voluntario->conversao ? $voluntario->conversao->format('m / Y') : '' }}</span></div>
                            <div class="border border-gray-400 p-1 flex-grow flex items-center text-[10px] h-[24px]"><span class="font-bold pr-2 flex-shrink-0">TEMPO DE OBREIRO(A) OU EVANGELISTA:</span><span>{{ $voluntario->obra ? $voluntario->obra->format('m / Y') : '' }}</span></div>
                        </div>
                        <div class="flex gap-1">
                            <div class="border border-gray-400 p-1 w-1/2 flex items-center justify-center gap-4 text-[10px] h-[24px]"><span class="font-bold">BATIZADO NAS ÁGUAS:</span><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('aguas', $batismoArray) ? 'X' : '' }}</div><span>SIM</span></div><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ !in_array('aguas', $batismoArray) ? 'X' : '' }}</div><span>NÃO</span></div></div>
                            <div class="border border-gray-400 p-1 w-1/2 flex items-center justify-center gap-4 text-[10px] h-[24px]"><span class="font-bold">NO ESPÍRITO SANTO:</span><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('espirito', $batismoArray) ? 'X' : '' }}</div><span>SIM</span></div><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ !in_array('espirito', $batismoArray) ? 'X' : '' }}</div><span>NÃO</span></div></div>
                        </div>
                        <div class="flex gap-1">
                            <div class="border border-gray-400 p-1 w-full flex items-center justify-center gap-4 text-[10px] h-[24px]"><span class="font-bold">FAZ O TRABALHO:</span><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('externo', $trabalhoArray) ? 'X' : '' }}</div><span>EXTERNO</span></div><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('interno', $trabalhoArray) ? 'X' : '' }}</div><span>INTERNO</span></div></div>
                        </div>
                        <div class="flex gap-1">
                            <div class="border border-gray-400 p-1 w-1/2 flex items-center justify-center gap-4 text-[10px] h-[24px]"><span class="font-bold">JÁ FOI PRESO?</span><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('preso', $presoArray) ? 'X' : '' }}</div><span>SIM</span></div><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ !in_array('preso', $presoArray) ? 'X' : '' }}</div><span>NÃO</span></div></div>
                            <div class="border border-gray-400 p-1 w-1/2 flex items-center justify-center gap-4 text-[10px] h-[24px]"><span class="font-bold">POSSUI ALGUM FAMILIAR PRESO?</span><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ in_array('familiar', $presoArray) ? 'X' : '' }}</div><span>SIM</span></div><div class="flex items-center gap-1"><div class="w-3 h-3 border border-gray-400 flex items-center justify-center">{{ !in_array('familiar', $presoArray) ? 'X' : '' }}</div><span>NÃO</span></div></div>
                        </div>
                        <div class="border border-gray-400 p-1">
                            <span class="font-bold">DESCREVA SEU TESTEMUNHO:</span>
                            <div class="w-full h-[3.5cm] mt-1 p-1 text-[10px] leading-tight overflow-hidden">{{ $voluntario->testemunho ?? '' }}</div>
                            <img src="{{ asset('assets/images/qr.png') }}" alt="QR Code" class="w-full h-auto pt-1">
                        </div>
                    </div>
                </section>
            </main>

            <footer class="w-full mt-1 p-1">
                <div class="border border-gray-400 p-1">
                    <div class="flex justify-between items-end">
                        <div class="flex items-center gap-2">
                            <span class="font-bold">Digitada em:</span>
                            <div class="border-b border-gray-400 w-[80px] h-[24px] text-center">{{ now()->format('d / m / Y') }}</div>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-[8cm] h-[48px] flex items-center justify-center">
                                @if($voluntario->assinatura)
                                    <img src="{{ asset($voluntario->assinatura) }}" alt="Assinatura" class="h-full w-auto">
                                @else
                                    <div class="border-b-2 border-black w-full h-full"></div>
                                @endif
                            </div>
                            <span class="font-bold text-xs">ASSINATURA DO VOLUNTÁRIO</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>