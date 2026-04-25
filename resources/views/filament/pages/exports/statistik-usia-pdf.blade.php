<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistik Pegawai Berdasarkan Usia</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            margin: 15px;
            font-weight: normal;
            background: #fff !important;
            background-image: none !important;
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
            border-spacing: 0; /* Tambahkan ini */
            margin-top: 10px;
            border: 1px solid #000;
        }

        th, td {
            border: 1px solid #000 !important; /* Gunakan !important agar tidak tertutup bg-color */
            padding: 8px 6px;
            vertical-align: middle;
            box-sizing: border-box;
        }

        th {
            background-color: #e0e0e0 !important;
            font-weight: bold;
            font-size: 11px;
            color: #000;
            text-align: center;
        }

        td {
            font-size: 10px;
            font-weight: 500;
            color: #000;
            text-align: center;
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
            font-size: 11px;
        }

        .header-info {
            margin-bottom: 15px;
            font-size: 10px;
            font-weight: normal;
        }

        thead th {
            border: 1px solid #000;
        }

        table {
            border: 1px solid #000;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .bg-blue {
            background-color: #dbeafe;
        }

        .bg-green {
            background-color: #d1fae5;
        }

        .bg-purple {
            background-color: #f3e8ff;
        }

        .bg-gray {
            background-color: #f3f4f6;
        }

        .font-bold {
            font-weight: bold;
        }

        .separator-row td {
                background-color: #e5e7eb !important;
                height: 10px;
                padding: 0;
                border: 1px solid #000 !important;
        }
    </style>
</head>
<body>
    <h2>STATISTIK PEGAWAI BERDASARKAN USIA</h2>
    <div class="subtitle">
        Pemerintah Kabupaten Tulang Bawang Barat
    </div>

    <div class="header-info">
        <div><strong>Tanggal Cetak:</strong> {{ $date }}</div>
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
                <th width="6%">Jml</th>
                <th width="6%">L</th>
                <th width="6%">P</th>
                <th width="6%">Jml</th>
                <th width="6%">L</th>
                <th width="6%">P</th>
                <th width="6%">Jml</th>
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
</body>
</html>
