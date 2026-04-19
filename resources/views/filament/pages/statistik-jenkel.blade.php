<x-filament::page>
    @push('styles')
    <style>
        .custom-statistik-table { border-collapse: collapse; width: 100%; border: 2px solid #000; }
        .custom-statistik-table th, .custom-statistik-table td { border: 2px solid #000 !important; padding: 15px; text-align: center; font-size: 1.1rem; }
        .custom-statistik-table th { background-color: #f3f4f6; font-weight: bold; }
        .text-left { text-align: left !important; }
        .statistik-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white; padding: 1rem; border-radius: 0.5rem 0.5rem 0 0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .btn-export { padding: 0.5rem 1rem; border-radius: 0.5rem; color: white; cursor: pointer; border: none; font-weight: bold; }
        .btn-pdf { background-color: #dc2626; }
        .btn-excel { background-color: #0f172a; }
        .gender-icon { font-size: 1.5rem; margin-right: 10px; }
    </style>
    @endpush

    <div class="statistik-card">
        <div class="statistik-header">
            <div>🚻 STATISTIK PEGAWAI PER JENIS KELAMIN</div>
            <div style="display: flex; gap: 8px;">
                <button class="btn-export btn-pdf" wire:click="exportPdf">📄 Export ke PDF</button>
                <button class="btn-export btn-excel" onclick="exportToExcel()">📊 Export ke Excel</button>
            </div>
        </div>

        <div class="table-wrapper" style="background: white;">
            <table class="custom-statistik-table" id="gender-table">
                <thead>
                    <tr>
                        <th class="text-left">Jenis Kelamin</th>
                        <th>PNS</th>
                        <th>PPPK</th>
                        <th>PPPK Paruh Waktu</th>
                        <th class="bg-gray-100">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->data as $row)
                        @php $totalRow = $row->pns + $row->pppk + $row->pppk_pw; @endphp
                        <tr class="hover:bg-blue-50">
                            <td class="text-left font-bold">
                                @if($row->gender == 'Laki-laki')
                                    <span class="gender-icon">👨</span>
                                @else
                                    <span class="gender-icon">👩</span>
                                @endif
                                {{ $row->gender }}
                            </td>
                            <td>{{ number_format($row->pns) }}</td>
                            <td>{{ number_format($row->pppk) }}</td>
                            <td>{{ number_format($row->pppk_pw) }}</td>
                            <td class="bg-gray-50 font-bold text-blue-700">{{ number_format($totalRow) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="text-left">TOTAL KESELURUHAN</td>
                        <td>{{ number_format(collect($this->data)->sum('pns')) }}</td>
                        <td>{{ number_format(collect($this->data)->sum('pppk')) }}</td>
                        <td>{{ number_format(collect($this->data)->sum('pppk_pw')) }}</td>
                        <td class="bg-blue-600 text-white">{{ number_format(collect($this->data)->sum('pns') + collect($this->data)->sum('pppk') + collect($this->data)->sum('pppk_pw')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportToExcel() {
            const table = document.getElementById('gender-table');
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, 'Statistik Gender');
            XLSX.writeFile(wb, 'statistik_gender_' + new Date().getTime() + '.xlsx');
        }
    </script>
</x-filament::page>
