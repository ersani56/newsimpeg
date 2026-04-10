<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pejabat Struktural</title>
    <style>
        @page { size: 21.5cm 33cm; margin: 1.2cm; }
        body { font-family: sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; vertical-align: top; }
        th { background: #eee; text-transform: uppercase; font-size: 8px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid black; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0">DAFTAR PEJABAT STRUKTURAL</h2>
        <div style="font-size: 11px">KATEGORI: {{ strtoupper(str_replace('_', ' ', $filter)) }}</div>
    </div>
    <div style="font-size: 8px; margin-bottom: 5px;">Dicetak: {{ $date }}</div>
    <table>
        <thead>
            <tr>
                <th width="20">NO</th>
                <th width="150">NAMA / NIP / GOL</th>
                <th>JABATAN</th>
                <th width="40">ESELON</th>
                <th width="180">UNIT KERJA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pejabat as $index => $p)
                <tr>
                    <td style="text-align:center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $p->nama }}</strong><br>
                        NIP. {{ $p->nip_baru }}<br>
                        Gol: {{ $p->gol_akhir_nama }}
                    </td>
                    <td>{{ $p->jabatan_nama }}</td>
                    <td style="text-align:center">{{ $p->eselon_display }}</td>
                    <td style="text-transform: uppercase; font-size: 8px;">{{ $p->unor_nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
