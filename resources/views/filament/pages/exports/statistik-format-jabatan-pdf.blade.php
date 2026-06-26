<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 1cm; }
        body {
            font-family: sans-serif;
            font-size: 8px;
        }
        h3 {
            text-align: center;
            margin: 0 0 4px;
            font-size: 14px;
        }
        h4 {
            margin: 10px 0 4px;
            font-size: 11px;
        }
        .meta {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background: #eee;
            font-weight: bold;
        }
        td:first-child {
            text-align: left;
            font-weight: bold;
        }
        tfoot td {
            background: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $columns = [
            'eselon_i',
            'eselon_ii',
            'eselon_iii',
            'eselon_iv',
            'utama',
            'madya',
            'muda',
            'pertama',
            'penyelia',
            'mahir',
            'terampil',
            'pemula',
            'pelaksana',
        ];

        $sections = [
            [
                'title' => 'PNS',
                'data' => $pnsData,
            ],
            [
                'title' => 'PPPK Penuh Waktu',
                'data' => $pppkData,
            ],
        ];
    @endphp

    <h3>STATISTIK FORMAT JABATAN</h3>
    <div class="meta">
        <div>Tanggal Cetak: {{ $date }}</div>
    </div>

    @foreach($sections as $section)
        <h4>{{ $section['title'] }}</h4>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">Gol</th>
                    <th colspan="4">Struktural</th>
                    <th colspan="8">Fungsional</th>
                    <th rowspan="2">Pelaksana</th>
                </tr>
                <tr>
                    <th>Eselon I</th>
                    <th>Eselon II</th>
                    <th>Eselon III</th>
                    <th>Eselon IV</th>
                    <th>Utama</th>
                    <th>Madya</th>
                    <th>Muda</th>
                    <th>Pertama</th>
                    <th>Penyelia</th>
                    <th>Mahir</th>
                    <th>Terampil</th>
                    <th>Pemula</th>
                </tr>
            </thead>
            <tbody>
                @forelse($section['data'] as $row)
                    <tr>
                        <td>{{ $row->gol }}</td>
                        <td>{{ number_format($row->eselon_i) }}</td>
                        <td>{{ number_format($row->eselon_ii) }}</td>
                        <td>{{ number_format($row->eselon_iii) }}</td>
                        <td>{{ number_format($row->eselon_iv) }}</td>
                        <td>{{ number_format($row->utama) }}</td>
                        <td>{{ number_format($row->madya) }}</td>
                        <td>{{ number_format($row->muda) }}</td>
                        <td>{{ number_format($row->pertama) }}</td>
                        <td>{{ number_format($row->penyelia) }}</td>
                        <td>{{ number_format($row->mahir) }}</td>
                        <td>{{ number_format($row->terampil) }}</td>
                        <td>{{ number_format($row->pemula) }}</td>
                        <td>{{ number_format($row->pelaksana) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    @foreach($columns as $column)
                        <td>{{ number_format(collect($section['data'])->sum($column)) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    @endforeach
</body>
</html>
