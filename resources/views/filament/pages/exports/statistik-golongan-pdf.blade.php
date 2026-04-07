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
    <h2>STATISTIK PEGAWAI BERDASARKAN GOLONGAN</h2>
    <div class="subtitle">
        Pemerintah Kabupaten Tulang Bawang Barat
    </div>

    <div class="header-info">
        <div><strong>Tanggal Cetak:</strong> {{ $date }}</div>
    </div>

    @php
        // Fungsi untuk mengurutkan data
        function sortDataForPdf($data) {
            $pnsData = [];
            $pppkData = [];
            $pppkPwData = [];

            // Urutan golongan PNS yang benar
            $pnsOrder = [
                'I/a', 'I/b', 'I/c', 'I/d',
                'II/a', 'II/b', 'II/c', 'II/d',
                'III/a', 'III/b', 'III/c', 'III/d',
                'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'
            ];

            // Urutan golongan PPPK
            $pppkOrder = [
                'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII',
                'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII'
            ];

            foreach ($data as $item) {
                $golongan = trim($item->golongan ?? '');

                // Cek apakah ini PPPK Paruh Waktu (tanpa golongan/kosong)
                if (empty($golongan) || $golongan === '' || $golongan === null) {
                    $pppkPwData[] = $item;
                }
                // Cek apakah ini PPPK (format "I", "II", "III", dst tanpa garis miring)
                elseif (in_array(strtoupper($golongan), $pppkOrder) || preg_match('/^[I|V|X]+$/i', $golongan)) {
                    $pppkData[] = $item;
                }
                // Sisanya adalah PNS (format "I/a", "I/b", "II/a", dll)
                else {
                    $pnsData[] = $item;
                }
            }

            // Urutkan PNS berdasarkan urutan yang sudah ditentukan
            usort($pnsData, function($a, $b) use ($pnsOrder) {
                $golA = trim($a->golongan ?? '');
                $golB = trim($b->golongan ?? '');
                $posA = array_search($golA, $pnsOrder);
                $posB = array_search($golB, $pnsOrder);
                return ($posA !== false ? $posA : 999) <=> ($posB !== false ? $posB : 999);
            });

            // Urutkan PPPK berdasarkan urutan yang sudah ditentukan
            usort($pppkData, function($a, $b) use ($pppkOrder) {
                $golA = strtoupper(trim($a->golongan ?? ''));
                $golB = strtoupper(trim($b->golongan ?? ''));
                $posA = array_search($golA, $pppkOrder);
                $posB = array_search($golB, $pppkOrder);
                return ($posA !== false ? $posA : 999) <=> ($posB !== false ? $posB : 999);
            });

            // Gabungkan semua data dengan urutan: PNS, PPPK, PPPK Paruh Waktu
            return array_merge($pnsData, $pppkData, $pppkPwData);
        }

        // Urutkan data
        $sortedData = sortDataForPdf($data);

        // Hitung total keseluruhan
        $grandTotalPnsL = 0;
        $grandTotalPnsP = 0;
        $grandTotalPns = 0;
        $grandTotalPppkL = 0;
        $grandTotalPppkP = 0;
        $grandTotalPppk = 0;
        $grandTotalPppkPwL = 0;
        $grandTotalPppkPwP = 0;
        $grandTotalPppkPw = 0;
        $grandTotal = 0;

        // Hitung jumlah PNS dan PPPK untuk separator
        $pnsCount = 0;
        $pppkCount = 0;
        foreach ($sortedData as $row) {
            $golongan = trim($row->golongan ?? '');
            if (empty($golongan) || $golongan === '' || $golongan === null) {
                break;
            } elseif (in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII'])) {
                $pppkCount++;
            } else {
                $pnsCount++;
            }
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="text-left" width="12%">GOLONGAN</th>
                <th colspan="3" width="25%">PNS</th>
                <th colspan="3" width="25%">PPPK</th>
                <th colspan="3" width="28%">PPPK PW</th>
                <th rowspan="2" width="10%">TOTAL</th>
            </tr>
            <tr>
                <th width="7%">L</th>
                <th width="7%">P</th>
                <th width="8%">TOTAL</th>
                <th width="7%">L</th>
                <th width="7%">P</th>
                <th width="8%">TOTAL</th>
                <th width="8%">L</th>
                <th width="8%">P</th>
                <th width="9%">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sortedData as $index => $row)
                @php
                    $golongan = trim($row->golongan ?? '');
                    $isPppkPw = empty($golongan) || $golongan === '' || $golongan === null;
                    $isPppk = !$isPppkPw && in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII']);
                    $isPns = !$isPppkPw && !$isPppk;

                    $pnsTotal = ($row->pns_l ?? 0) + ($row->pns_p ?? 0);
                    $pppkTotal = ($row->pppk_l ?? 0) + ($row->pppk_p ?? 0);
                    $pppkPwTotal = ($row->pppk_pw_l ?? 0) + ($row->pppk_pw_p ?? 0);
                    $rowTotal = $pnsTotal + $pppkTotal + $pppkPwTotal;

                    // Tentukan label golongan yang ditampilkan
                    $displayGolongan = $row->golongan ?? '-';
                    if ($isPppkPw) {
                        $displayGolongan = 'PPPK PW';
                    } elseif ($isPppk) {
                        $displayGolongan = $displayGolongan;
                    }

                    // Cek apakah perlu separator
                    $showSeparator = false;
                    if ($index == $pnsCount && $pnsCount > 0 && $pppkCount > 0) {
                        $showSeparator = true;
                    } elseif ($index == ($pnsCount + $pppkCount) && $pppkCount > 0 && count($sortedData) > ($pnsCount + $pppkCount)) {
                        $showSeparator = true;
                    }

                    // Akumulasi total
                    $grandTotalPnsL += $row->pns_l ?? 0;
                    $grandTotalPnsP += $row->pns_p ?? 0;
                    $grandTotalPns += $pnsTotal;
                    $grandTotalPppkL += $row->pppk_l ?? 0;
                    $grandTotalPppkP += $row->pppk_p ?? 0;
                    $grandTotalPppk += $pppkTotal;
                    $grandTotalPppkPwL += $row->pppk_pw_l ?? 0;
                    $grandTotalPppkPwP += $row->pppk_pw_p ?? 0;
                    $grandTotalPppkPw += $pppkPwTotal;
                    $grandTotal += $rowTotal;
                @endphp

                @if($showSeparator)
                    <tr class="separator-row">
                        <td colspan="11">&nbsp;</td>
                    </tr>
                @endif

                <tr>
                    <td class="text-left font-bold">
                        @if($isPppkPw)
                            <span>{{ $displayGolongan }}</span>
                        @elseif($isPppk)
                            <span>{{ $displayGolongan }}</span>
                        @else
                            <span>{{ $displayGolongan }}</span>
                        @endif
                    </td>

                    <!-- PNS -->
                    <td>{{ number_format($row->pns_l ?? 0) }}</td>
                    <td>{{ number_format($row->pns_p ?? 0) }}</td>
                    <td class="font-bold bg-blue">{{ number_format($pnsTotal) }}</td>

                    <!-- PPPK -->
                    <td>{{ number_format($row->pppk_l ?? 0) }}</td>
                    <td>{{ number_format($row->pppk_p ?? 0) }}</td>
                    <td class="font-bold bg-green">{{ number_format($pppkTotal) }}</td>

                    <!-- PPPK Paruh Waktu -->
                    <td>{{ number_format($row->pppk_pw_l ?? 0) }}</td>
                    <td>{{ number_format($row->pppk_pw_p ?? 0) }}</td>
                    <td class="font-bold bg-purple">{{ number_format($pppkPwTotal) }}</td>

                    <!-- Total Row -->
                    <td class="font-bold bg-gray">{{ number_format($rowTotal) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center font-bold">TIDAK ADA DATA</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-left font-bold">TOTAL</td>

                <!-- PNS Total -->
                <td class="font-bold">{{ number_format($grandTotalPnsL) }}</td>
                <td class="font-bold">{{ number_format($grandTotalPnsP) }}</td>
                <td class="font-bold bg-blue">{{ number_format($grandTotalPns) }}</td>

                <!-- PPPK Total -->
                <td class="font-bold">{{ number_format($grandTotalPppkL) }}</td>
                <td class="font-bold">{{ number_format($grandTotalPppkP) }}</td>
                <td class="font-bold bg-green">{{ number_format($grandTotalPppk) }}</td>

                <!-- PPPK Paruh Waktu Total -->
                <td class="font-bold">{{ number_format($grandTotalPppkPwL) }}</td>
                <td class="font-bold">{{ number_format($grandTotalPppkPwP) }}</td>
                <td class="font-bold bg-purple">{{ number_format($grandTotalPppkPw) }}</td>

                <!-- Grand Total -->
                <td class="font-bold bg-gray">{{ number_format($grandTotal) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
