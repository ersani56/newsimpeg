<x-filament::page>
    @push('styles')
    <style>
        .custom-statistik-table {
            border-collapse: collapse;
            width: 100%;
        }

        .custom-statistik-table th,
        .custom-statistik-table td {
            border: 2px solid #000;
            padding: 0.75rem 1rem;
            vertical-align: middle;
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

        .total-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .total-info-item {
            text-align: center;
        }

        .total-info-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .total-info-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .statistik-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .statistik-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .statistik-body {
            padding: 1rem;
        }
    </style>
    @endpush

    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Statistik Pegawai per Golongan
            </h2>

            <button
                wire:click="exportPdf"
                class="btn-export"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </button>
        </div>

        <!-- Total Info Cards -->
        <div class="total-info">
            <div class="total-info-item">
                <div class="total-info-label">Total Seluruh Pegawai</div>
                <div class="total-info-value">{{ number_format($this->totalPegawai) }}</div>
            </div>
            <div class="total-info-item">
                <div class="total-info-label">PNS</div>
                <div class="total-info-value">{{ number_format($this->totalPns) }}</div>
            </div>
            <div class="total-info-item">
                <div class="total-info-label">PPPK</div>
                <div class="total-info-value">{{ number_format($this->totalPppk) }}</div>
            </div>
            <div class="total-info-item">
                <div class="total-info-label">PPPK Paruh Waktu</div>
                <div class="total-info-value">{{ number_format($this->totalPppkPw) }}</div>
            </div>
        </div>

        <!-- Tabel Statistik -->
        <div class="statistik-card">
            <div class="statistik-header">
                📊 Distribusi Pegawai Berdasarkan Golongan
            </div>
            <div class="statistik-body">
                <div class="overflow-x-auto">
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
                                <tr>
                                    <td class="text-left font-bold">{{ $row->golongan ?? '-' }}</td>

                                    <!-- PNS -->
                                    <td>{{ number_format($row->pns_l) }}</td>
                                    <td>{{ number_format($row->pns_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pns_l + $row->pns_p) }}</td>

                                    <!-- PPPK -->
                                    <td>{{ number_format($row->pppk_l) }}</td>
                                    <td>{{ number_format($row->pppk_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pppk_l + $row->pppk_p) }}</td>

                                    <!-- PPPK Paruh Waktu -->
                                    <td>{{ number_format($row->pppk_pw_l) }}</td>
                                    <td>{{ number_format($row->pppk_pw_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</td>

                                    <!-- Total per Golongan -->
                                    <td class="font-bold">{{ number_format($totalRow) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td class="text-left font-bold">TOTAL</td>

                                <!-- Total PNS -->
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_l')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_p')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_l') + collect($this->data)->sum('pns_p')) }}</td>

                                <!-- Total PPPK -->
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_l')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_p')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_l') + collect($this->data)->sum('pppk_p')) }}</td>

                                <!-- Total PPPK Paruh Waktu -->
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_l')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_p')) }}</td>
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_l') + collect($this->data)->sum('pppk_pw_p')) }}</td>

                                <!-- Grand Total -->
                                <td class="font-bold">{{ number_format($this->totalPegawai) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
