<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan - {{ $periode }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
            color: #2c3e50;
        }

        .header p {
            margin: 4px 0;
            font-size: 13px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Pendapatan Kost Astoria</h1>
        <p><strong>Periode:</strong> {{ $label }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @if ($periode === 'Bulanan' && is_array($dataHarian))
        <table>
            <thead>
                <tr>
                    <th style="width: 70%;">Tanggal</th>
                    <th style="width: 30%;">Jumlah Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($dataHarian as $data)
                    <tr>
                        <td>{{ $data['tanggal'] }}</td>
                        <td>{{ rupiah($data['jumlah']) }}</td>
                        @php $total += $data['jumlah']; @endphp
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="total">Total Pendapatan Bulan Ini: {{ rupiah($total) }}</p>

    @elseif ($periode === 'Tahunan' && is_array($dataBulanan))
        <table>
            <thead>
                <tr>
                    <th style="width: 70%;">Bulan</th>
                    <th style="width: 30%;">Jumlah Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($dataBulanan as $data)
                    <tr>
                        <td>{{ $data['bulan'] }}</td>
                        <td>{{ rupiah($data['jumlah']) }}</td>
                        @php $total += $data['jumlah']; @endphp
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="total">Total Pendapatan Tahun Ini: {{ rupiah($total) }}</p>

    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 70%;">Keterangan</th>
                    <th style="width: 30%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Pendapatan Hari Ini</td>
                    <td>{{ rupiah($jumlah ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        &copy; {{ date('Y') }} Kost Astoria. Dokumen ini dicetak secara otomatis oleh sistem.
    </div>

</body>
</html>
