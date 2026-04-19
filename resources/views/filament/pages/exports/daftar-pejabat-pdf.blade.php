<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 1.2cm; }
        body { font-size: 9px; font-family: sans-serif; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 4px; }
        th { background: #eee; }

        .center { text-align: center; }
    </style>
</head>
<body>

<h3 style="text-align:center;">DAFTAR PEJABAT STRUKTURAL</h3>
<p>Filter: {{ $filter }}</p>
<p>Tanggal: {{ $date }}</p>

<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>NAMA</th>
            <th>NIP</th>
            <th>JABATAN</th>
            <th>ESELON</th>
            <th>UNIT KERJA</th>
        </tr>
    </thead>

    <tbody>
        @forelse($data as $i => $p)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->nip_baru }}</td>
                <td>{{ $p->jabatan_nama }}</td>
                <td class="center">{{ $p->eselon_display }}</td>
                <td>{{ $p->unor_nama }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center">Tidak ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
