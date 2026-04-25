<x-filament::page>
    @push('styles')
    <style>
        /* Menggunakan style yang sama dengan Statistik Golongan Anda agar konsisten */
        .custom-statistik-table { border-collapse: collapse; width: 100%; border: 2px solid #000; min-width: 800px; }
        .custom-statistik-table th, .custom-statistik-table td { border: 2px solid #000 !important; padding: 8px 12px; text-align: center; }
        .custom-statistik-table th { background-color: #e5e7eb; font-weight: 700; }
        .custom-statistik-table .text-left { text-align: left; }
        .statistik-header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 1rem 1.5rem; border-radius: 0.5rem 0.5rem 0 0; display: flex; justify-content: space-between; align-items: center; }
        .btn-export { padding: 0.5rem 1rem; border-radius: 0.5rem; color: white; font-weight: 500; cursor: pointer; border: none; }
        .btn-pdf { background-color: #dc2626; }
        .btn-excel { background-color: #10b981; }
        .table-wrapper { overflow-x: auto; background: white; border-radius: 0 0 0.5rem 0.5rem; }
    </style>
    @endpush

    @php
        // Logika Sorting Pendidikan (Custom order)
        $orderPendidikan = [
            'S3' => 1, 'S2' => 2, 'S1' => 3, 'D4' => 4, 'D3' => 5, 'D2' => 6, 'D1' => 7,
            'SMA' => 8, 'SMK' => 9, 'SMP' => 10, 'SD' => 11
        ];

        $sortedData = collect($this->data)->sortBy(function($item) use ($orderPendidikan) {
            return $orderPendidikan[strtoupper($item->pendidikan)] ?? 99;
        });

        $tPnsL = $sortedData->sum('pns_l'); $tPnsP = $sortedData->sum('pns_p');
        $tPppkL = $sortedData->sum('pppk_l'); $tPppkP = $sortedData->sum('pppk_p');
        $tPppkPwL = $sortedData->sum('pppk_pw_l'); $tPppkPwP = $sortedData->sum('pppk_pw_p');
        $grandTotal = $tPnsL + $tPnsP + $tPppkL + $tPppkP + $tPppkPwL + $tPppkPwP;
    @endphp

    <div class="statistik-card">
        <div class="statistik-header">
            <div>🎓 STATISTIK PEGAWAI PER PENDIDIKAN</div>
            <div style="display: flex; gap: 10px;">
                <button class="btn-export btn-pdf" wire:click="exportPdf">📄 Export ke PDF</button>
                <button class="btn-export btn-excel" onclick="exportToExcel()">📊 Export ke Excel</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="custom-statistik-table" id="data-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left">Tingkat Pendidikan</th>
                        <th colspan="3">PNS</th>
                        <th colspan="3">PPPK</th>
                        <th colspan="3">PPPK Paruh Waktu</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>L</th> <th>P</th> <th>Jml</th>
                        <th>L</th> <th>P</th> <th>Jml</th>
                        <th>L</th> <th>P</th> <th>Jml</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedData as $row)
                        @php $rowTotal = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="text-left font-bold">{{ $row->pendidikan ?: 'Tidak Terisi' }}</td>
                            <td>{{ number_format($row->pns_l) }}</td>
                            <td>{{ number_format($row->pns_p) }}</td>
                            <td class="bg-blue-50 font-bold">{{ number_format($row->pns_l + $row->pns_p) }}</td>
                            <td>{{ number_format($row->pppk_l) }}</td>
                            <td>{{ number_format($row->pppk_p) }}</td>
                            <td class="bg-green-50 font-bold">{{ number_format($row->pppk_l + $row->pppk_p) }}</td>
                            <td>{{ number_format($row->pppk_pw_l) }}</td>
                            <td>{{ number_format($row->pppk_pw_p) }}</td>
                            <td class="bg-purple-50 font-bold">{{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</td>
                            <td class="bg-gray-100 font-bold">{{ number_format($rowTotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="text-left">TOTAL</td>
                        <td>{{ number_format($tPnsL) }}</td> <td>{{ number_format($tPnsP) }}</td>
                        <td class="bg-blue-100">{{ number_format($tPnsL + $tPnsP) }}</td>
                        <td>{{ number_format($tPppkL) }}</td> <td>{{ number_format($tPppkP) }}</td>
                        <td class="bg-green-100">{{ number_format($tPppkL + $tPppkP) }}</td>
                        <td>{{ number_format($tPppkPwL) }}</td> <td>{{ number_format($tPppkPwP) }}</td>
                        <td class="bg-purple-100">{{ number_format($tPppkPwL + $tPppkPwP) }}</td>
                        <td class="bg-gray-200">{{ number_format($grandTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportToExcel() {
            const table = document.getElementById('data-table');
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, 'Statistik Pendidikan');
            XLSX.writeFile(wb, 'statistik_pendidikan_' + new Date().getTime() + '.xlsx');
        }
    </script>
</x-filament::page>
