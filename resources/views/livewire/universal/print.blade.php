<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Ficha Cadastral - {{ $pastor->nome }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        @media print {
            .print-hidden {
                display: none;
            }

            @page {
                margin: 1cm;
            }
        }

        .container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
        }

        .card {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        footer {
            text-align: center;
            opacity: 0.75;
            border-top: 4px solid #172554;
            padding: 5px 0;
            font-size: 10px;
            flex-shrink: 0;
            /* Impede que o footer cresça */
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

        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 8px 12px;
            font-size: 14px;
        }

        .info-label {
            font-weight: bold;
            color: #1f2937;
        }

        .info-value {
            word-break: break-word;
            color: #4b5563;
        }

        .photo {
            width: 206px;
            height: 282px;
            object-fit: cover;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 2px;
            transition: transform 0.3s ease;
        }

        .vehicle-photos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .vehicle-photo {
            text-align: center;
        }

        .vehicle-photo img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border: 2px solid #ccc;
            border-radius: 4px;
        }

        .vehicle-photo p {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #172554;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body class="bg-gray-100 print:bg-white">
    <div class="container">
        <div class="print-hidden my-4 text-center">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Imprimir Ficha</button>
        </div>

        <!-- Página 1: Dados do Pastor -->
        <div class="card">
            <header>
                <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
            </header>

            <p class="title">Cadastro Pastoral UNP</p>

            <main>
                <div class="flex flex-row gap-6">
                    <!-- Fotos à esquerda, uma acima da outra -->
                    <div class="flex flex-col gap-4">
                        <div>
                            <img src="{{ $pastor->foto ? asset($pastor->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($pastor->nome) . '&color=7F9CF5&background=EBF4FF' }}"
                                alt="Foto de {{ $pastor->nome }}" class="photo hover:scale-105">
                        </div>
                        @if ($pastor->nome_esposa && $pastor->foto_esposa)
                            <div>
                                <img src="{{ asset($pastor->foto_esposa) }}" alt="Foto de {{ $pastor->nome_esposa }}"
                                    class="photo hover:scale-105">
                            </div>
                        @endif
                    </div>

                    <!-- Dados Pessoais e Ministeriais à direita com design tecnológico -->
                    <div
                        class="flex-1 flex flex-col gap-6 bg-gradient-to-br from-gray-50 to-blue-50 p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                        <div>
                            <div class="section-title">Dados Pessoais</div>
                            <div class="info-grid">
                                <div class="info-label">Nome:</div>
                                <div class="info-value">{{ $pastor->nome }}</div>
                                <div class="info-label">Nascimento:</div>
                                <div class="info-value">
                                    {{ $pastor->nascimento ? $pastor->nascimento->format('d/m/Y') : 'N/A' }}</div>
                                <div class="info-label">Email pessoal:</div>
                                <div class="info-value">{{ $pastor->email ?? 'N/A' }}</div>
                                <div class="info-label">Whatsapp:</div>
                                <div class="info-value">{{ $pastor->whatsapp }}</div>
                                <div class="info-label">Telefone:</div>
                                <div class="info-value">{{ $pastor->telefone ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div>
                            <div class="section-title">Dados Ministeriais</div>
                            <div class="info-grid">
                                <div class="info-label">Responsabilidade no Grupo:</div>
                                <div class="info-value capitalize">{{ $pastor->cargo }}</div>
                                <div class="info-label">Bloco:</div>
                                <div class="info-value">{{ $pastor->bloco->nome ?? 'N/A' }}</div>
                                <div class="info-label">Região:</div>
                                <div class="info-value">{{ $pastor->regiao->nome ?? 'N/A' }}</div>
                                <div class="info-label">Entrada no Grupo:</div>
                                <div class="info-value">
                                    {{ $pastor->entrada ? $pastor->entrada->format('d/m/Y') : 'N/A' }}</div>
                                <div class="info-label">Tempo no Estado/Bloco:</div>
                                <div class="info-value">{{ $pastor->chegada ?? 'N/A' }}</div>
                                <div class="info-label">Dias que faz trabalho:</div>
                                <div class="info-value">
                                    {{ is_array($pastor->trabalho) && !empty($pastor->trabalho) ? implode(', ', $pastor->trabalho) : 'Não informado' }}
                                </div>
                                <div class="info-label">Já foi Preso:</div>
                                <div class="info-value">{{ $pastor->preso ? 'Sim' : 'Não' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dados da Esposa abaixo com design elegante -->
                @if ($pastor->nome_esposa)
                    <div class="mt-6 bg-gray-50 p-6 rounded-xl shadow-sm border-t-2 border-blue-200">
                        <div class="section-title">Dados da Esposa</div>
                        <div class="info-grid">
                            <div class="info-label">Nome:</div>
                            <div class="info-value">{{ $pastor->nome_esposa }}</div>
                            <div class="info-label">Tempo de Obra:</div>
                            <div class="info-value">{{ $pastor->obra ?? 'N/A' }}</div>
                            <div class="info-label">Tempo de Casada:</div>
                            <div class="info-value">{{ $pastor->casado ?? 'N/A' }}</div>
                            <div class="info-label">É Missionária:</div>
                            <div class="info-value">{{ $pastor->consagrada_esposa ? 'Sim' : 'Não' }}</div>
                            <div class="info-label">Já foi Presa:</div>
                            <div class="info-value">{{ $pastor->preso_esposa ? 'Sim' : 'Não' }}</div>
                            <div class="info-label">Dias que faz trabalho:</div>
                            <div class="info-value">
                                {{ is_array($pastor->trabalho_esposa) && !empty($pastor->trabalho_esposa) ? implode(', ', $pastor->trabalho_esposa) : 'Não informado' }}
                            </div>
                        </div>
                    </div>
                @endif
            </main>
            <footer>
                <p class="footer-banner">UNIVERSAL NOS <span>PRESÍDIOS</span></p>
                <p>Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA 40280-000</p>
            </footer>
        </div>

        @if ($pastor->carroUnp)
            <div class="page-break"></div>
            <!-- Página 2: Dados do Veículo -->
            <div class="card">
                <header>
                    <img src="{{ asset('formaturas/logo-unp.png') }}" alt="Logo UNP">
                </header>

                <p class="title">Carro da Frota UNP-BA</p>

                <main>
                    <div class="section-title">Dados do Veículo</div>
                    <div class="info-grid">
                        <div class="info-label">Modelo:</div>
                        <div class="info-value">{{ $pastor->carroUnp->modelo }}</div>
                        <div class="info-label">Ano:</div>
                        <div class="info-value">{{ $pastor->carroUnp->ano }}</div>
                        <div class="info-label">Placa:</div>
                        <div class="info-value">{{ $pastor->carroUnp->placa }}</div>
                        <div class="info-label">KM:</div>
                        <div class="info-value">{{ number_format($pastor->carroUnp->km, 0, ',', '.') }}</div>
                        <div class="info-label">Pastor Responsável:</div>
                        <div class="info-value">{{ $pastor->nome }}</div>
                        <div class="info-label">Bloco:</div>
                        <div class="info-value">{{ $pastor->bloco->nome ?? 'N/A' }}</div>
                    </div>

                    @if ($pastor->carroUnp->demanda)
                        <div class="section-title">Demanda</div>
                        <p class="whitespace-pre-wrap text-sm">{{ $pastor->carroUnp->demanda }}</p>
                    @endif

                    <div class="section-title">Fotos do Veículo</div>
                    <div class="vehicle-photos">
                        @if ($pastor->carroUnp->foto_frente)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_frente) }}"
                                    alt="Frente">
                                <p>Frente</p>
                            </div>
                        @endif
                        @if ($pastor->carroUnp->foto_tras)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_tras) }}"
                                    alt="Traseira">
                                <p>Traseira</p>
                            </div>
                        @endif
                        @if ($pastor->carroUnp->foto_direita)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_direita) }}"
                                    alt="Direita">
                                <p>Direita</p>
                            </div>
                        @endif
                        @if ($pastor->carroUnp->foto_esquerda)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_esquerda) }}"
                                    alt="Esquerda">
                                <p>Esquerda</p>
                            </div>
                        @endif
                        @if ($pastor->carroUnp->foto_dentro)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_dentro) }}"
                                    alt="Interior">
                                <p>Interior</p>
                            </div>
                        @endif
                        @if ($pastor->carroUnp->foto_cambio)
                            <div class="vehicle-photo"><img src="{{ asset($pastor->carroUnp->foto_cambio) }}"
                                    alt="Câmbio">
                                <p>Câmbio</p>
                            </div>
                        @endif
                    </div>
                </main>
            </div>
        @endif
    </div>
    <footer>
        <p class="footer-banner">UNIVERSAL NOS <span>PRESÍDIOS</span></p>
        <p>Avenida Antônio Carlos Magalhães, 4197 Pituba, Salvador - BA 40280-000</p>
    </footer>
</body>

</html>
