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
        <div class="statistik-card">
            <div class="statistik-header">
                <div>📈 Distribusi Pegawai Berdasarkan Kelompok Usia</div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-export btn-export-pdf" wire:click="exportPdf">
                        📄 Export ke PDF
                    </button>
                    <button class="btn-export btn-export-excel" onclick="exportToExcel()">
                        📊 Export ke Excel
                    </button>
                </div>
            </div>
            <div class="statistik-body">
                <div class="table-wrapper">
                    <table class="custom-statistik-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-left">Kelompok Usia</th>
                                <th colspan="3">PNS</th>
                                <th colspan="3">PPPK</th>
                                <th colspan="3">PPPK Paruh Waktu</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <!-- PNS -->
                                <th>L</th>
                                <th>P</th>
                                <th>Total</th>
                                <!-- PPPK -->
                                <th>L</th>
                                <th>P</th>
                                <th>Total</th>
                                <!-- PPPK Paruh Waktu -->
                                <th>L</th>
                                <th>P</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->data as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="text-left font-bold">{{ $row->range }} tahun</td>

                                    <!-- PNS -->
                                    <td>{{ number_format($row->pns_l) }}</td>
                                    <td>{{ number_format($row->pns_p) }}</td>
                                    <td class="font-bold bg-blue-50">{{ number_format($row->pns_total) }}</td>

                                    <!-- PPPK -->
                                    <td>{{ number_format($row->pppk_l) }}</td>
                                    <td>{{ number_format($row->pppk_p) }}</td>
                                    <td class="font-bold bg-green-50">{{ number_format($row->pppk_total) }}</td>

                                    <!-- PPPK Paruh Waktu -->
                                    <td>{{ number_format($row->pppk_pw_l) }}</td>
                                    <td>{{ number_format($row->pppk_pw_p) }}</td>
                                    <td class="font-bold bg-purple-50">{{ number_format($row->pppk_pw_total) }}</td>

                                    <!-- Total -->
                                    <td class="font-bold bg-gray-100">{{ number_format($row->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold">
                            <tr class="border-t-2 border-black">
                                <td class="text-left">TOTAL</td>

                                <!-- PNS Total -->
                                <td>{{ number_format(collect($this->data)->sum('pns_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pns_p')) }}</td>
                                <td class="bg-blue-100">{{ number_format(collect($this->data)->sum('pns_total')) }}</td>

                                <!-- PPPK Total -->
                                <td>{{ number_format(collect($this->data)->sum('pppk_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_p')) }}</td>
                                <td class="bg-green-100">{{ number_format(collect($this->data)->sum('pppk_total')) }}</td>

                                <!-- PPPK Paruh Waktu Total -->
                                <td>{{ number_format(collect($this->data)->sum('pppk_pw_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_pw_p')) }}</td>
                                <td class="bg-purple-100">{{ number_format(collect($this->data)->sum('pppk_pw_total')) }}</td>

                                <!-- Grand Total -->
                                <td class="bg-gray-200">{{ number_format(collect($this->data)->sum('total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        <!-- Info untuk mobile -->
        <div class="mt-4 text-center text-sm text-gray-500" style="padding: 8px; background: #f9fafb; border-radius: 8px;">
            <span>📱 Geser tabel ke kanan untuk melihat data lengkap</span>
            <span style="margin: 0 8px">•</span>
            <span>🔄 Update: {{ now()->format('d/m/Y H:i') }}</span>
            <span style="margin: 0 8px">•</span>
            <span>📊 {{ count($this->data) }} Kelompok Usia</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportToExcel() {
            // Get the table
            const table = document.querySelector('.custom-statistik-table');

            // Clone the table to avoid modifying the original
            const cloneTable = table.cloneNode(true);

            // Create a new workbook
            const wb = XLSX.utils.book_new();

            // Convert table to worksheet
            const ws = XLSX.utils.table_to_sheet(cloneTable, { raw: true });

            // Adjust column widths
            ws['!cols'] = [
                {wch: 20}, // Kelompok Usia
                {wch: 10}, {wch: 10}, {wch: 10}, // PNS L, P, Total
                {wch: 10}, {wch: 10}, {wch: 10}, // PPPK L, P, Total
                {wch: 12}, {wch: 12}, {wch: 12}, // PPPK PW L, P, Total
                {wch: 12}  // Total
            ];

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Statistik Usia');

            // Export to Excel
            XLSX.writeFile(wb, 'statistik_usia_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xlsx');
        }
    </script>
</x-filament::page>
