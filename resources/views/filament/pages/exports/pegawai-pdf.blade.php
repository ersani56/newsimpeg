<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 1cm; }
        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #111;
        }
        h3 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 14px;
        }
        .meta {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #222;
            padding: 4px;
            vertical-align: top;
        }
        th {
            background: #eee;
            text-align: center;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h3>DAFTAR PEGAWAI</h3>

    <div class="meta">
        <div>Tanggal Export: {{ $date }}</div>
        <div>Pencarian: {{ $search }}</div>
        <div>Total Data: {{ $total }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 110px;">NIP</th>
                <th style="width: 150px;">NAMA</th>
                <th style="width: 115px;">KEDUDUKAN HUKUM</th>
                <th style="width: 105px;">NIK</th>
                <th style="width: 75px;">AGAMA</th>
                <th style="width: 95px;">PENDIDIKAN</th>
                <th>JABATAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $pegawai)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $pegawai->nip_baru }}</td>
                    <td>{{ $pegawai->nama_lengkap }}</td>
                    <td>{{ $pegawai->kedudukan_hukum }}</td>
                    <td>{{ $pegawai->nik }}</td>
                    <td>{{ $pegawai->agama }}</td>
                    <td>{{ $pegawai->pendidikan }}</td>
                    <td>{{ $pegawai->jabatan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
