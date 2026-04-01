<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistik Pegawai per Golongan</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            margin: 15px;
            font-weight: normal;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #000;
            font-size: 16px;
            font-weight: bold;
        }

        .subtitle {
            text-align: center;
            color: #000;
            margin-bottom: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 2px solid #000; /* Garis lebih tebal */
            padding: 10px 8px;
            vertical-align: middle;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 12px;
            color: #000;
        }

        td {
            font-size: 11px;
            font-weight: 500; /* Sedikit lebih tebal */
            color: #000;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #000;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .total-row td {
            font-weight: bold;
            font-size: 12px;
        }

        .header-info {
            margin-bottom: 15px;
            font-size: 10px;
            font-weight: normal;
        }

        /* Untuk header tabel */
        thead th {
            border: 2px solid #000;
        }

        /* Membuat border lebih jelas */
        table {
            border: 2px solid #000;
        }

        /* Styling untuk total */
        tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        /* Garis bawah untuk header */
        .header-border {
            border-bottom: 2px solid #000;
        }
    </style>
</head>
<body>
    <h2>STATISTIK PEGAWAI PER GOLONGAN</h2>
    <div class="subtitle">
        Data Pegawai Berdasarkan Golongan dan Status Kepegawaian
    </div>

    <div class="header-info">
        <div><strong>Tanggal Cetak:</strong> {{ $date }}</div>
        <div><strong>Periode:</strong> Seluruh Data Aktif</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-left" width="15%">GOLONGAN</th>
                <th colspan="2" width="20%">PNS</th>
                <th colspan="2" width="20%">PPPK</th>
                <th colspan="2" width="25%">PPPK PARUH WAKTU</th>
            </tr>
            <tr>
                <th width="10%">LAKI-LAKI</th>
                <th width="10%">PEREMPUAN</th>
                <th width="10%">LAKI-LAKI</th>
                <th width="10%">PEREMPUAN</th>
                <th width="12%">LAKI-LAKI</th>
                <th width="13%">PEREMPUAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td class="text-left"><strong>{{ $row->golongan ?? '-' }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pns_l ?: 0) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pns_p ?: 0) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pppk_l ?: 0) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pppk_p ?: 0) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pppk_pw_l ?: 0) }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($row->pppk_pw_p ?: 0) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center"><strong>TIDAK ADA DATA</strong></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-left"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pns_l']) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pns_p']) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pppk_l']) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pppk_p']) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pppk_pw_l']) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($totals['pppk_pw_p']) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table style="border: none; margin-top: 20px;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; text-align: center;">
                    <div style="border-top: 2px solid #000; margin-top: 30px; padding-top: 5px;">
                        <strong>Mengetahui,</strong><br>
                        <span style="font-size: 10px;">Kepala Badan Kepegawaian</span>
                    </div>
                </td>
                <td style="border: none; text-align: center;">
                    <div style="border-top: 2px solid #000; margin-top: 30px; padding-top: 5px;">
                        <strong>Petugas,</strong><br>
                        <span style="font-size: 10px;">Admin</span>
                    </div>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"></td>
                <td style="border: none; text-align: center; padding-top: 40px;">
                    (____________________)
                </td>
                <td style="border: none; text-align: center; padding-top: 40px;">
                    ({{ auth()->user()->name ?? '____________________' }})
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
