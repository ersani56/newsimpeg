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
            margin-bottom: 1rem;
        }

        .btn-export:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        .total-info {
            background: #f3f4f6;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            display: inline-block;
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

        .text-bold {
            font-weight: bold;
        }

        .bg-total {
            background-color: #f0f0f0;
        }
    </style>
    @endpush

    <div>
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Statistik Pegawai Berdasarkan Usia
                </h2>
                <div class="total-info mt-2">
                    📊 Total Seluruh Pegawai: <strong>{{ number_format($this->totalPegawai) }}</strong> Orang
                </div>
            </div>

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

        <div class="statistik-card">
            <div class="statistik-header">
                📈 Distribusi Pegawai Berdasarkan Kelompok Usia
            </div>
            <div class="statistik-body">
                <div class="overflow-x-auto">
                    <table class="custom-statistik-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-left" width="12%">Kelompok Usia</th>
                                <th colspan="3">PNS</th>
                                <th colspan="3">PPPK</th>
                                <th colspan="3">PPPK Paruh Waktu</th>
                                <th rowspan="2" width="10%">Total</th>
                            </tr>
                            <tr>
                                <!-- PNS -->
                                <th width="8%">L</th>
                                <th width="8%">P</th>
                                <th width="8%">Total</th>
                                <!-- PPPK -->
                                <th width="8%">L</th>
                                <th width="8%">P</th>
                                <th width="8%">Total</th>
                                <!-- PPPK Paruh Waktu -->
                                <th width="8%">L</th>
                                <th width="8%">P</th>
                                <th width="8%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->data as $row)
                                <tr>
                                    <td class="text-left font-bold">{{ $row->range }} tahun</td>

                                    <!-- PNS -->
                                    <td>{{ number_format($row->pns_l) }}</td>
                                    <td>{{ number_format($row->pns_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pns_total) }}</td>

                                    <!-- PPPK -->
                                    <td>{{ number_format($row->pppk_l) }}</td>
                                    <td>{{ number_format($row->pppk_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pppk_total) }}</td>

                                    <!-- PPPK Paruh Waktu -->
                                    <td>{{ number_format($row->pppk_pw_l) }}</td>
                                    <td>{{ number_format($row->pppk_pw_p) }}</td>
                                    <td class="font-bold">{{ number_format($row->pppk_pw_total) }}</td>

                                    <!-- Total -->
                                    <td class="font-bold bg-total">{{ number_format($row->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-total">
                            <tr class="font-bold">
                                <td class="text-left">TOTAL</td>

                                <!-- PNS Total -->
                                <td>{{ number_format(collect($this->data)->sum('pns_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pns_p')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pns_total')) }}</td>

                                <!-- PPPK Total -->
                                <td>{{ number_format(collect($this->data)->sum('pppk_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_p')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_total')) }}</td>

                                <!-- PPPK Paruh Waktu Total -->
                                <td>{{ number_format(collect($this->data)->sum('pppk_pw_l')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_pw_p')) }}</td>
                                <td>{{ number_format(collect($this->data)->sum('pppk_pw_total')) }}</td>

                                <!-- Grand Total -->
                                <td class="font-bold">{{ number_format(collect($this->data)->sum('total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart sederhana menggunakan CSS (opsional) -->
        <div class="statistik-card mt-4">
            <div class="statistik-header">
                📊 Visualisasi Distribusi Usia
            </div>
            <div class="statistik-body">
                <div class="space-y-2">
                    @foreach($this->data as $row)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold">{{ $row->range }} tahun</span>
                                <span>{{ number_format($row->total) }} orang ({{ round(($row->total / $this->totalPegawai) * 100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-6 rounded-full flex items-center justify-end pr-2 text-white text-xs font-bold"
                                     style="width: {{ ($row->total / $this->totalPegawai) * 100 }}%">
                                    {{ round(($row->total / $this->totalPegawai) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
