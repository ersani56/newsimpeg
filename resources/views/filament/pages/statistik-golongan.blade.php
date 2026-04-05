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

        .btn-export:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
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
    </style>
    @endpush

<div class="statistik-body">
    <!-- Header with Export Button (if needed) -->
    <div class="statistik-header">
        <div>📊 STATISTIK PEGAWAI PER GOLONGAN</div>
        @if(isset($this->exportButton))
            <button class="btn-export" onclick="window.livewire.emit('exportStatistik')">
                📥 Export ke Excel
            </button>
        @endif
    </div>

    <!-- Mobile Summary Cards (Ringkasan cepat di mobile) -->
    @php
        $totalPnsL = array_sum(array_column($this->data, 'pns_l'));
        $totalPnsP = array_sum(array_column($this->data, 'pns_p'));
        $totalPns = $totalPnsL + $totalPnsP;

        $totalPppkL = array_sum(array_column($this->data, 'pppk_l'));
        $totalPppkP = array_sum(array_column($this->data, 'pppk_p'));
        $totalPppk = $totalPppkL + $totalPppkP;

        $totalPppkPwL = array_sum(array_column($this->data, 'pppk_pw_l'));
        $totalPppkPwP = array_sum(array_column($this->data, 'pppk_pw_p'));
        $totalPppkPw = $totalPppkPwL + $totalPppkPwP;

        $grandTotal = $this->totalPegawai;
    @endphp

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
    <div class="table-wrapper">
        <table class="custom-statistik-table">
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
                    <th>Total</th>
                    <th>L</th>
                    <th>P</th>
                    <th>Total</th>
                    <th>L</th>
                    <th>P</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($this->data as $row)
                    @php
                        $totalRow = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="text-left font-bold">{{ $row->golongan ?? '-' }}</td>

                        <!-- PNS -->
                        <td class="{{ $row->pns_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pns_l) }}</td>
                        <td class="{{ $row->pns_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pns_p) }}</td>
                        <td class="font-bold bg-blue-50">{{ number_format($row->pns_l + $row->pns_p) }}</td>

                        <!-- PPPK -->
                        <td class="{{ $row->pppk_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_l) }}</td>
                        <td class="{{ $row->pppk_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_p) }}</td>
                        <td class="font-bold bg-green-50">{{ number_format($row->pppk_l + $row->pppk_p) }}</td>

                        <!-- PPPK PW -->
                        <td class="{{ $row->pppk_pw_l > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_pw_l) }}</td>
                        <td class="{{ $row->pppk_pw_p > 0 ? 'font-semibold' : '' }}">{{ number_format($row->pppk_pw_p) }}</td>
                        <td class="font-bold bg-purple-50">{{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</td>

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
        <span>📊 {{ count($this->data) }} Golongan</span>
    </div>
</div>
</x-filament::page>
