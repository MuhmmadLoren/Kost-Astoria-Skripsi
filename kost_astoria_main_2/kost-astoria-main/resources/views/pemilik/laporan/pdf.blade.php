<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .info {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
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

    <h2>Laporan Pembayaran</h2>
    <div class="info">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Pemilik</th>
                <th>Jumlah Bayar</th>
                <th>Tanggal Transfer</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payment as $pay)
                <tr>
                    <td>{{ $pay->nama_pemilik }}</td>
                    <td>{{ rupiah($pay->jumlah_bayar) }}</td>
                    <td>{{ \Carbon\Carbon::parse($pay->tgl_transfer)->translatedFormat('d F Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data pembayaran</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} Kost Astoria. Dokumen ini dicetak secara otomatis oleh sistem.
    </div>

</body>
</html>

