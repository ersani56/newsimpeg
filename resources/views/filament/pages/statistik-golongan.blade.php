<x-filament::page>
    @push('styles')
    <style>
        .custom-statistik-table {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid #000;
        }

        .custom-statistik-table th,
        .custom-statistik-table td {
            border: 2px solid #000 !important;
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
        .mobile-view {
            display: none;
        }

        .desktop-view {
            display: block;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .mobile-view {
                display: block;
            }

            .desktop-view {
                display: none;
            }
        }
    </style>
    @endpush

<div class="statistik-body">

    <!-- ================== MOBILE (CARD) ================== -->
    <div class="mobile-view">
        @foreach ($this->data as $row)
            @php
                $totalRow = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p;
            @endphp

            <div class="bg-white rounded-xl shadow p-4 border">
                <!-- Header -->
                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-lg">
                        {{ $row->golongan ?? '-' }}
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-bold">{{ number_format($totalRow) }}</span>
                    </div>
                </div>

                <!-- PNS -->
                <div class="mb-2">
                    <div class="font-semibold text-blue-600">PNS</div>
                    <div class="flex justify-between text-sm">
                        <span>L: {{ number_format($row->pns_l) }}</span>
                        <span>P: {{ number_format($row->pns_p) }}</span>
                        <span class="font-bold">Total: {{ number_format($row->pns_l + $row->pns_p) }}</span>
                    </div>
                </div>

                <!-- PPPK -->
                <div class="mb-2">
                    <div class="font-semibold text-green-600">PPPK</div>
                    <div class="flex justify-between text-sm">
                        <span>L: {{ number_format($row->pppk_l) }}</span>
                        <span>P: {{ number_format($row->pppk_p) }}</span>
                        <span class="font-bold">Total: {{ number_format($row->pppk_l + $row->pppk_p) }}</span>
                    </div>
                </div>

                <!-- PPPK Paruh Waktu -->
                <div>
                    <div class="font-semibold text-purple-600">PPPK Paruh Waktu</div>
                    <div class="flex justify-between text-sm">
                        <span>L: {{ number_format($row->pppk_pw_l) }}</span>
                        <span>P: {{ number_format($row->pppk_pw_p) }}</span>
                        <span class="font-bold">Total: {{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</span>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- TOTAL CARD -->
        <div class="bg-gray-100 rounded-xl p-4 border">
            <div class="font-bold text-center mb-2">TOTAL KESELURUHAN</div>

            <div class="text-sm space-y-1">
                <div class="flex justify-between">
                    <span>PNS</span>
                    <span class="font-bold">
                        {{ number_format(collect($this->data)->sum('pns_l') + collect($this->data)->sum('pns_p')) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>PPPK</span>
                    <span class="font-bold">
                        {{ number_format(collect($this->data)->sum('pppk_l') + collect($this->data)->sum('pppk_p')) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>PPPK Paruh Waktu</span>
                    <span class="font-bold">
                        {{ number_format(collect($this->data)->sum('pppk_pw_l') + collect($this->data)->sum('pppk_pw_p')) }}
                    </span>
                </div>

                <div class="flex justify-between border-t pt-2 mt-2">
                    <span class="font-bold">Grand Total</span>
                    <span class="font-bold">{{ number_format($this->totalPegawai) }}</span>
                </div>
            </div>
        </div>
    </div>


    <!-- ================== DESKTOP (TABEL) ================== -->
    <div class="desktop-view overflow-x-auto">
        <table class="custom-statistik-table min-w-[900px]">
            <thead>
                <tr>
                    <th rowspan="2" class="text-left">Golongan</th>
                    <th colspan="3">PNS</th>
                    <th colspan="3">PPPK</th>
                    <th colspan="3">PPPK Paruh Waktu</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>L</th><th>P</th><th>Total</th>
                    <th>L</th><th>P</th><th>Total</th>
                    <th>L</th><th>P</th><th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($this->data as $row)
                    @php
                        $totalRow = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p;
                    @endphp
                    <tr>
                        <td class="text-left font-bold">{{ $row->golongan ?? '-' }}</td>

                        <td>{{ number_format($row->pns_l) }}</td>
                        <td>{{ number_format($row->pns_p) }}</td>
                        <td class="font-bold">{{ number_format($row->pns_l + $row->pns_p) }}</td>

                        <td>{{ number_format($row->pppk_l) }}</td>
                        <td>{{ number_format($row->pppk_p) }}</td>
                        <td class="font-bold">{{ number_format($row->pppk_l + $row->pppk_p) }}</td>

                        <td>{{ number_format($row->pppk_pw_l) }}</td>
                        <td>{{ number_format($row->pppk_pw_p) }}</td>
                        <td class="font-bold">{{ number_format($row->pppk_pw_l + $row->pppk_pw_p) }}</td>

                        <td class="font-bold">{{ number_format($totalRow) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="bg-gray-100">
                <tr>
                    <td class="text-left font-bold">TOTAL</td>

                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_l')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_p')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pns_l') + collect($this->data)->sum('pns_p')) }}</td>

                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_l')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_p')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_l') + collect($this->data)->sum('pppk_p')) }}</td>

                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_l')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_p')) }}</td>
                    <td class="font-bold">{{ number_format(collect($this->data)->sum('pppk_pw_l') + collect($this->data)->sum('pppk_pw_p')) }}</td>

                    <td class="font-bold">{{ number_format($this->totalPegawai) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
</x-filament::page>
