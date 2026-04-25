<x-filament::page>
    @push('styles')
    <style>
        .custom-statistik-table {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #000;
            min-width: 800px;
        }

        .custom-statistik-table th,
        .custom-statistik-table td {
            border: 2px solid #000 !important;
            padding: 8px 12px;
        }

        .custom-statistik-table th {
            background-color: #e5e7eb;
            font-weight: 700;
            font-size: 0.875rem;
            text-align: center;
        }

        .custom-statistik-table td {
            font-weight: 500;
            text-align: center;
        }

        .custom-statistik-table .text-left {
            text-align: left;
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-export-pdf {
            background-color: #dc2626;
        }

        .btn-export-excel {
            background-color: #10b981;
        }

        .btn-export:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        .statistik-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: bold;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .statistik-body {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Mobile Styles - Table with Horizontal Scroll */
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Custom scrollbar for better UX */
        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Separator between groups */
        .group-separator {
            border-top: 3px solid #000 !important;
        }

        /* Mobile specific table styling */
        @media (max-width: 768px) {
            .custom-statistik-table {
                font-size: 12px;
                min-width: 700px;
            }

            .custom-statistik-table th,
            .custom-statistik-table td {
                padding: 6px 8px;
            }

            .statistik-header {
                flex-direction: column;
                text-align: center;
            }

            /* Sticky first column for better readability */
            .custom-statistik-table th:first-child,
            .custom-statistik-table td:first-child {
                position: sticky;
                left: 0;
                background-color: white;
                z-index: 1;
            }

            .custom-statistik-table th:first-child {
                background-color: #e5e7eb;
                z-index: 2;
            }

            .custom-statistik-table tfoot td:first-child {
                background-color: #f3f4f6;
            }
        }

        /* Info cards for mobile summary */
        .mobile-info-cards {
            display: none;
            margin-bottom: 16px;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .mobile-info-cards {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                margin-bottom: 16px;
            }

            .info-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 12px;
                border-radius: 8px;
                text-align: center;
            }

            .info-card-label {
                font-size: 11px;
                opacity: 0.9;
                margin-bottom: 4px;
            }

            .info-card-value {
                font-size: 20px;
                font-weight: bold;
            }
        }

        /* Group row styling */
        .group-header-row {
            background-color: #f3f4f6;
        }

        .group-header-row td {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: left;
            padding: 8px 12px;
        }

        /* Print styles for PDF */
        @media print {
            .btn-export, .btn-export-pdf, .btn-export-excel, .statistik-header button {
                display: none !important;
            }

            .statistik-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .custom-statistik-table {
                border-collapse: collapse;
                width: 100%;
            }

            .custom-statistik-table th {
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                padding: 20px;
                font-size: 12px;
            }
        }
    </style>
    @endpush

@php
    // Fungsi untuk mengurutkan data sesuai permintaan
    function sortStatistikData($data) {
        $pnsData = [];
        $pppkData = [];
        $ppkpPwData = [];

        foreach ($data as $item) {
            $golongan = trim($item->golongan ?? '');

            // Cek apakah ini PPPK Paruh Waktu (tanpa golongan/kosong)
            if (empty($golongan) || $golongan === '' || $golongan === null) {
                $ppkpPwData[] = $item;
            }
            // Cek apakah ini PPPK (format "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X" tanpa garis miring)
            elseif (preg_match('/^[I|V|X]+$/i', $golongan) || in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'])) {
                $pppkData[] = $item;
            }
            // Sisanya adalah PNS (format "I/a", "I/b", "II/a", dll)
            else {
                $pnsData[] = $item;
            }
        }

        // Urutkan PNS berdasarkan golongan (I/a, I/b, II/a, dst)
        usort($pnsData, function($a, $b) {
            return strcmp($a->golongan ?? '', $b->golongan ?? '');
        });

        // Urutkan PPPK berdasarkan golongan (I, II, III, IV, V, VI, VII, VIII, IX, X)
        usort($pppkData, function($a, $b) {
            $order = [
                'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4,
                'V' => 5, 'VI' => 6, 'VII' => 7, 'VIII' => 8,
                'IX' => 9, 'X' => 10
            ];
            $golA = $order[strtoupper(trim($a->golongan ?? ''))] ?? 999;
            $golB = $order[strtoupper(trim($b->golongan ?? ''))] ?? 999;
            return $golA <=> $golB;
        });

        // Gabungkan semua data dengan urutan: PNS, PPPK, PPPK Paruh Waktu
        return array_merge($pnsData, $pppkData, $ppkpPwData);
    }

    // Urutkan data
    $sortedData = sortStatistikData($this->data);

    // Hitung total untuk setiap kategori
    $totalPnsL = 0;
    $totalPnsP = 0;
    $totalPppkL = 0;
    $totalPppkP = 0;
    $totalPppkPwL = 0;
    $totalPppkPwP = 0;

    foreach ($sortedData as $row) {
        $golongan = trim($row->golongan ?? '');

        if (empty($golongan) || $golongan === '' || $golongan === null) {
            // PPPK Paruh Waktu
            $totalPppkPwL += $row->pppk_pw_l;
            $totalPppkPwP += $row->pppk_pw_p;
        } elseif (preg_match('/^[I|V|X]+$/i', $golongan) || in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'])) {
            // PPPK
            $totalPppkL += $row->pppk_l;
            $totalPppkP += $row->pppk_p;
        } else {
            // PNS
            $totalPnsL += $row->pns_l;
            $totalPnsP += $row->pns_p;
        }
    }

    $totalPns = $totalPnsL + $totalPnsP;
    $totalPppk = $totalPppkL + $totalPppkP;
    $totalPppkPw = $totalPppkPwL + $totalPppkPwP;
    $grandTotal = $totalPns+$totalPppk+$totalPppkPw;

    // Hitung jumlah baris per grup untuk separator
    $pnsCount = 0;
    $pppkCount = 0;
    foreach ($sortedData as $row) {
        $golongan = trim($row->golongan ?? '');
        if (empty($golongan) || $golongan === '' || $golongan === null) {
            break;
        } elseif (preg_match('/^[I|V|X]+$/i', $golongan) || in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'])) {
            $pppkCount++;
        } else {
            $pnsCount++;
        }
    }
@endphp

<div class="statistik-body">
    <!-- Header with Export Buttons -->
    <div class="statistik-header">
        <div>📊 STATISTIK PEGAWAI PER GOLONGAN</div>
        <div style="display: flex; gap: 10px;">
            <button class="btn-export btn-export-pdf" wire:click="exportPdf">
                📄 Export ke PDF
            </button>
            <button class="btn-export btn-export-excel" onclick="exportToExcel()">
                📊 Export ke Excel
            </button>
        </div>
    </div>

    <!-- Mobile Summary Cards (Ringkasan cepat di mobile) -->
    <div class="mobile-info-cards">
        <div class="info-card">
            <div class="info-card-label">👔 PNS</div>
            <div class="info-card-value">{{ number_format($totalPns) }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label">📋 PPPK</div>
            <div class="info-card-value">{{ number_format($totalPppk) }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label">⏰ PPPK PW</div>
            <div class="info-card-value">{{ number_format($totalPppkPw) }}</div>
        </div>
        <div class="info-card">
            <div class="info-card-label">🎯 TOTAL</div>
            <div class="info-card-value">{{ number_format($grandTotal) }}</div>
        </div>
    </div>

    <!-- Table with Horizontal Scroll -->
    <div class="table-wrapper" id="statistik-table">
        <table class="custom-statistik-table" id="data-table">
            <thead>
                <tr>
                    <th rowspan="2" class="text-left">Golongan</th>
                    <th colspan="3">PNS</th>
                    <th colspan="3">PPPK</th>
                    <th colspan="3">PPPK Paruh Waktu</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>L</th>
                    <th>P</th>
                    <th>Jml</th>
                    <th>L</th>
                    <th>P</th>
                    <th>Jml</th>
                    <th>L</th>
                    <th>P</th>
                    <th>Jml</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($sortedData as $index => $row)
                    @php
                        $golongan = trim($row->golongan ?? '');
                        $isPppkPw = empty($golongan) || $golongan === '' || $golongan === null;
                        $isPppk = !$isPppkPw && (preg_match('/^[I|V|X]+$/i', $golongan) || in_array(strtoupper($golongan), ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X']));
                        $isPns = !$isPppkPw && !$isPppk;

                        $totalRow = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p;

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
                    @endphp

                    @if($showSeparator)
                        <tr class="group-separator">
                            <td colspan="12" style="background-color: #f3f4f6; padding: 4px;"></td>
                        </tr>
                    @endif

                    <tr class="{{ $isPppkPw ? 'bg-purple-50' : ($isPppk ? 'bg-green-50' : '') }} hover:bg-gray-100">
                        <td class="text-left font-bold">
                            @if($isPppkPw)
                                <span class="text-purple-700">⏰ {{ $displayGolongan }}</span>
                            @elseif($isPppk)
                                <span class="text-green-700">📋 {{ $displayGolongan }}</span>
                            @else
                                <span class="text-blue-700">👔 {{ $displayGolongan }}</span>
                            @endif
                        </td>

                        <!-- PNS -->
                        <td class="{{ $row->pns_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pns_l) }}</td>
                        <td class="{{ $row->pns_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pns_p) }}</td>
                        <td class="font-bold {{ $isPns ? 'bg-blue-100' : '' }}">{{ number_format($row->pns_l + $row->pns_p) }}</td>

                        <!-- PPPK -->
                        <td class="{{ $row->pppk_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_l) }}</td>
                        <td class="{{ $row->pppk_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_p) }}</td>
                        <td class="font-bold {{ $isPppk ? 'bg-green-100' : '' }}">{{ number_format($row->pppk_l + $row->pppk_p) }}</td>

                        <!-- PPPK PW -->
                        <td class="{{ $row->pppk_pw_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_pw_l) }}</td>
                        <td class="{{ $row->pppk_pw_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_pw_p) }}</td>
                        <td class="font-bold {{ $isPppkPw ? 'bg-purple-100' : '' }}">{{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</td>

                        <!-- Total Row -->
                        <td class="font-bold bg-gray-100">{{ number_format($totalRow) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-gray-100 font-bold">
                <tr class="border-t-2 border-black">
                    <td class="text-left font-bold text-base">TOTAL</td>

                    <!-- PNS Totals -->
                    <td class="font-bold">{{ number_format($totalPnsL) }}</td>
                    <td class="font-bold">{{ number_format($totalPnsP) }}</td>
                    <td class="font-bold bg-blue-100">{{ number_format($totalPns) }}</td>

                    <!-- PPPK Totals -->
                    <td class="font-bold">{{ number_format($totalPppkL) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkP) }}</td>
                    <td class="font-bold bg-green-100">{{ number_format($totalPppk) }}</td>

                    <!-- PPPK PW Totals -->
                    <td class="font-bold">{{ number_format($totalPppkPwL) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkPwP) }}</td>
                    <td class="font-bold bg-purple-100">{{ number_format($totalPppkPw) }}</td>

                    <!-- Grand Total -->
                    <td class="font-bold bg-gray-200 text-base">{{ number_format($grandTotal) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Info tambahan untuk mobile -->
    <div style="padding: 12px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; background: #f9fafb;">
        <span>📱 Geser tabel ke kanan untuk melihat lengkap</span>
        <span style="margin: 0 8px">•</span>
        <span>🔄 Update: {{ now()->format('d/m/Y H:i') }}</span>
        <span style="margin: 0 8px">•</span>
        <span>📊 PNS: {{ $pnsCount }} Golongan | PPPK: {{ $pppkCount }} Golongan</span>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    function exportToPDF() {
        // Get the table element
        const element = document.getElementById('statistik-table');

        // Get the header element
        const header = document.querySelector('.statistik-header');

        // Clone the table for PDF
        const cloneElement = element.cloneNode(true);

        // Create a wrapper div for PDF content
        const pdfContent = document.createElement('div');
        pdfContent.style.padding = '20px';
        pdfContent.style.fontFamily = 'Arial, sans-serif';

        // Add title
        const title = document.createElement('h2');
        title.textContent = 'STATISTIK PEGAWAI PER GOLONGAN';
        title.style.textAlign = 'center';
        title.style.marginBottom = '20px';
        title.style.color = '#333';
        pdfContent.appendChild(title);

        // Add date
        const date = document.createElement('p');
        date.textContent = 'Tanggal Export: ' + new Date().toLocaleString('id-ID');
        date.style.textAlign = 'center';
        date.style.marginBottom = '20px';
        date.style.color = '#666';
        pdfContent.appendChild(date);

        // Add the table
        pdfContent.appendChild(cloneElement);

        // Add footer
        const footer = document.createElement('p');
        footer.textContent = 'Dicetak pada: ' + new Date().toLocaleString('id-ID');
        footer.style.textAlign = 'center';
        footer.style.marginTop = '20px';
        footer.style.fontSize = '10px';
        footer.style.color = '#999';
        pdfContent.appendChild(footer);

        // PDF options
        const opt = {
            margin: [0.5, 0.5, 0.5, 0.5],
            filename: 'statistik_pegawai_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, logging: false, useCORS: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        // Export to PDF
        html2pdf().set(opt).from(pdfContent).save();
    }

    function exportToExcel() {
        // Get the table
        const table = document.getElementById('data-table');

        // Create a new workbook
        const wb = XLSX.utils.book_new();

        // Convert table to worksheet
        const ws = XLSX.utils.table_to_sheet(table, { raw: true });

        // Adjust column widths
        ws['!cols'] = [
            {wch: 20}, // Golongan
            {wch: 10}, {wch: 10}, {wch: 10}, // PNS L, P, Total
            {wch: 10}, {wch: 10}, {wch: 10}, // PPPK L, P, Total
            {wch: 10}, {wch: 10}, {wch: 10}, // PPPK PW L, P, Total
            {wch: 12}  // Total
        ];

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Statistik Pegawai');

        // Export to Excel
        XLSX.writeFile(wb, 'statistik_pegawai_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xlsx');
    }
</script>
</x-filament::page>
