<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Cestas Básicas</title>
    <style>
        @page {
            margin: 1cm;

            @bottom-center {
                content: "Página " counter(page) " de " counter(pages);
                font-family: Arial, sans-serif;
                font-size: 10px;
                color: #000;
                text-align: center;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .no-photo {
            color: #999;
        }

        .last-page {
            page-break-before: always;
            text-align: center;
            margin-top: 10px;
        }

        img {
            object-fit: cover;
            width: 3cm;
            height: 2cm;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Relatório de Cestas Básicas</h2>
        <p>Data de Impressão: {{ $printDate }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>NOME</th>
                <th>INSTITUIÇÃO</th>
                <th>CESTAS</th>
                <th>FOTO</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cestas as $cesta)
                <tr>
                    <td>{{ mb_convert_encoding($cesta->nome, 'UTF-8', 'UTF-8') }}</td>
                    <td>{{ mb_convert_encoding($cesta->terreiro ?? '', 'UTF-8', 'UTF-8') }}</td>
                    <td>{{ $cesta->cestas }}</td>
                    <td>
                        @if ($cesta->foto)
                            @php
                                // Usa o caminho relativo fornecido em $cesta->foto
                                $relativePath = $cesta->foto;
                                \Log::channel('dashboard')->info('Exibindo imagem no PDF', [
                                    'path' => public_path($relativePath),
                                    'exists' => file_exists(public_path($relativePath)),
                                    'readable' => is_readable(public_path($relativePath)),
                                ]);
                            @endphp
                            @if (file_exists(public_path($relativePath)) && is_readable(public_path($relativePath)))
                                <img src="{{ $relativePath }}" alt="Foto da cesta">
                            @else
                                <span class="no-photo">Sem foto (arquivo inacessível)</span>
                            @endif
                        @else
                            <span class="no-photo">Sem foto cadastrada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhuma cesta encontrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="last-page">
        <p><strong>Relatório de Cestas Básicas</strong></p>
        <p>{{ $printDate }}</p>
        <p>Vídeos e fotos disponíveis no link: <a
                href="https://igrejauniversaldorei-my.sharepoint.com/:f:/g/personal/jmmneto_universal_org/EmvDLp6E-blGgDkGaw8kZfcBLB17W2sUuToJ12_GLOFIIg?e=NTTE9A">https://igrejauniversaldorei-my.sharepoint.com</a>
        </p>
    </div>
</body>

</html>
