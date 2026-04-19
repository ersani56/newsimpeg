<x-filament::page>
    @push('styles')
    <style>
        .custom-statistik-table { border-collapse: collapse; width: 100%; border: 2px solid #000; }
        .custom-statistik-table th, .custom-statistik-table td { border: 2px solid #000 !important; padding: 10px; text-align: center; }
        .custom-statistik-table th { background-color: #f3f4f6; font-weight: bold; }
        .text-left { text-align: left !important; }
        .statistik-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white; padding: 1rem; border-radius: 0.5rem 0.5rem 0 0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .btn-export { padding: 0.5rem 1rem; border-radius: 0.5rem; color: white; cursor: pointer; border: none; font-weight: bold; }
        .btn-pdf { background-color: #dc2626; }
        .btn-excel { background-color: #10b981; }
        .table-wrapper { background: white; overflow-x: auto; }
    </style>
    @endpush

    @php
        $tPnsL = collect($this->data)->sum('pns_l');
        $tPnsP = collect($this->data)->sum('pns_p');
        $tPppkL = collect($this->data)->sum('pppk_l');
        $tPppkP = collect($this->data)->sum('pppk_p');
        $tPppkPwL = collect($this->data)->sum('pppk_pw_l');
        $tPppkPwP = collect($this->data)->sum('pppk_pw_p');
        $grandTotal = $tPnsL + $tPnsP + $tPppkL + $tPppkP + $tPppkPwL + $tPppkPwP;
    @endphp

    <div class="statistik-card">
        <div class="statistik-header">
            <div>💼 STATISTIK PEGAWAI PER JENIS JABATAN</div>
            <div style="display: flex; gap: 8px;">
                <button class="btn-export btn-pdf" wire:click="exportPdf">📄 Export ke PDF</button>
                <button class="btn-export btn-excel" onclick="exportToExcel()">📊 Export ke Excel</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="custom-statistik-table" id="jabatan-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left">Jenis Jabatan</th>
                        <th colspan="3">PNS</th>
                        <th colspan="3">PPPK</th>
                        <th colspan="3">PPPK Paruh Waktu</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>L</th> <th>P</th> <th>Total</th>
                        <th>L</th> <th>P</th> <th>Total</th>
                        <th>L</th> <th>P</th> <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->data as $row)
                        @php
                            $sumPns = $row->pns_l + $row->pns_p;
                            $sumPppk = $row->pppk_l + $row->pppk_p;
                            $sumPppkPw = $row->pppk_pw_l + $row->pppk_pw_p;
                            $totalRow = $sumPns + $sumPppk + $sumPppkPw;
                        @endphp
                        <tr class="hover:bg-orange-50">
                            <td class="text-left font-bold">{{ $row->kelompok_jabatan }}</td>
                            <td>{{ number_format($row->pns_l) }}</td>
                            <td>{{ number_format($row->pns_p) }}</td>
                            <td class="bg-blue-50 font-bold">{{ number_format($sumPns) }}</td>
                            <td>{{ number_format($row->pppk_l) }}</td>
                            <td>{{ number_format($row->pppk_p) }}</td>
                            <td class="bg-green-50 font-bold">{{ number_format($sumPppk) }}</td>
                            <td>{{ number_format($row->pppk_pw_l) }}</td>
                            <td>{{ number_format($row->pppk_pw_p) }}</td>
                            <td class="bg-purple-50 font-bold">{{ number_format($sumPppkPw) }}</td>
                            <td class="bg-gray-100 font-bold">{{ number_format($totalRow) }}</td>
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
            const table = document.getElementById('jabatan-table');
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, 'Statistik Jabatan');
            XLSX.writeFile(wb, 'statistik_jabatan_' + new Date().getTime() + '.xlsx');
        }
    </script>
</x-filament::page>
