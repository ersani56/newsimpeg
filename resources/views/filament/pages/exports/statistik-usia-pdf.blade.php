<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistik Pegawai Berdasarkan Usia</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        th, td {
            border: 2px solid #000;
            padding: 8px 6px;
            vertical-align: middle;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }

        td {
            font-size: 9px;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .header-info {
            margin-bottom: 15px;
            font-size: 9px;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
        }

        .bg-total {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h2>STATISTIK PEGAWAI BERDASARKAN USIA</h2>
    <div class="subtitle">
        Data Distribusi Pegawai per Kelompok Usia
    </div>

    <div class="header-info">
        <div><strong>Tanggal Cetak:</strong> {{ $date }}</div>
        <div><strong>Total Pegawai:</strong> {{ number_format($totalPegawai) }} Orang</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-left" width="12%">Kelompok Usia</th>
                <th colspan="3">PNS</th>
                <th colspan="3">PPPK</th>
                <th colspan="3">PPPK Paruh Waktu</th>
                <th rowspan="2" width="10%">Total</th>
            </tr>
            <tr>
                <th width="6%">L</th>
                <th width="6%">P</th>
                <th width="6%">Total</th>
                <th width="6%">L</th>
                <th width="6%">P</th>
                <th width="6%">Total</th>
                <th width="6%">L</th>
                <th width="6%">P</th>
                <th width="6%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td class="text-left font-bold">{{ $row->range }} tahun</td>

                    <td>{{ number_format($row->pns_l) }}</td>
                    <td>{{ number_format($row->pns_p) }}</td>
                    <td class="font-bold">{{ number_format($row->pns_total) }}</td>

                    <td>{{ number_format($row->pppk_l) }}</td>
                    <td>{{ number_format($row->pppk_p) }}</td>
                    <td class="font-bold">{{ number_format($row->pppk_total) }}</td>

                    <td>{{ number_format($row->pppk_pw_l) }}</td>
                    <td>{{ number_format($row->pppk_pw_p) }}</td>
                    <td class="font-bold">{{ number_format($row->pppk_pw_total) }}</td>

                    <td class="font-bold bg-total">{{ number_format($row->total) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-total">
            <tr class="font-bold">
                <td class="text-left">TOTAL</td>

                <td>{{ number_format($totals['pns_l']) }}</td>
                <td>{{ number_format($totals['pns_p']) }}</td>
                <td>{{ number_format($totals['pns_total']) }}</td>

                <td>{{ number_format($totals['pppk_l']) }}</td>
                <td>{{ number_format($totals['pppk_p']) }}</td>
                <td>{{ number_format($totals['pppk_total']) }}</td>

                <td>{{ number_format($totals['pppk_pw_l']) }}</td>
                <td>{{ number_format($totals['pppk_pw_p']) }}</td>
                <td>{{ number_format($totals['pppk_pw_total']) }}</td>

                <td>{{ number_format($totals['total']) }}</td>
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
                        Kepala Badan Kepegawaian
                    </div>
                </td>
                <td style="border: none; text-align: center;">
                    <div style="border-top: 2px solid #000; margin-top: 30px; padding-top: 5px;">
                        <strong>Petugas,</strong><br>
                        Admin
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
