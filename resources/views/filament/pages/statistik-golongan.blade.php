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

        /* Enhanced Mobile Styles */
        @media (max-width: 768px) {
            .mobile-view {
                display: block;
            }

            .desktop-view {
                display: none;
            }

            .mobile-stat-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                margin-bottom: 16px;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .mobile-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            }

            .mobile-card-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 12px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .mobile-golongan {
                font-size: 16px;
                font-weight: bold;
            }

            .mobile-total-badge {
                background: rgba(255,255,255,0.2);
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 14px;
                font-weight: bold;
            }

            .mobile-stats-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 16px;
            }

            .mobile-stat-item {
                background: #f8fafc;
                border-radius: 10px;
                padding: 12px;
                text-align: center;
                border-left: 3px solid;
                transition: all 0.2s ease;
            }

            .mobile-stat-item.pns {
                border-left-color: #3b82f6;
            }

            .mobile-stat-item.pppk {
                border-left-color: #10b981;
            }

            .mobile-stat-item.pppk-pw {
                border-left-color: #8b5cf6;
            }

            .mobile-stat-label {
                font-size: 12px;
                font-weight: 600;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }

            .mobile-stat-label i {
                font-size: 14px;
            }

            .mobile-gender-stats {
                display: flex;
                justify-content: space-between;
                gap: 8px;
                margin-top: 8px;
                padding-top: 8px;
                border-top: 1px solid #e2e8f0;
            }

            .mobile-gender-item {
                flex: 1;
                font-size: 11px;
            }

            .mobile-gender-value {
                font-weight: bold;
                font-size: 14px;
                margin-top: 2px;
            }

            .mobile-total-section {
                margin-top: 8px;
                padding-top: 8px;
                text-align: center;
                font-weight: bold;
                font-size: 14px;
                color: #1e293b;
            }

            .mobile-overall-stats {
                background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
                color: white;
                padding: 16px;
                border-radius: 12px;
                margin-top: 16px;
            }

            .mobile-overall-title {
                font-size: 14px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 12px;
                opacity: 0.9;
            }

            .mobile-overall-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .mobile-overall-item {
                background: rgba(255,255,255,0.15);
                padding: 10px;
                border-radius: 8px;
                text-align: center;
            }

            .mobile-overall-label {
                font-size: 11px;
                margin-bottom: 4px;
            }

            .mobile-overall-value {
                font-size: 18px;
                font-weight: bold;
            }

            .mobile-grand-total {
                grid-column: span 2;
                background: rgba(255,255,255,0.25);
                margin-top: 4px;
            }

            .mobile-grand-total .mobile-overall-value {
                font-size: 22px;
            }

            /* Progress bar for mobile */
            .mobile-progress {
                margin-top: 12px;
                padding: 12px;
                background: #f1f5f9;
                border-radius: 8px;
            }

            .mobile-progress-item {
                margin-bottom: 8px;
            }

            .mobile-progress-label {
                font-size: 11px;
                font-weight: 600;
                margin-bottom: 4px;
                display: flex;
                justify-content: space-between;
            }

            .mobile-progress-bar {
                height: 6px;
                background: #e2e8f0;
                border-radius: 3px;
                overflow: hidden;
            }

            .mobile-progress-fill {
                height: 100%;
                background: linear-gradient(90deg, #667eea, #764ba2);
                border-radius: 3px;
                transition: width 0.3s ease;
            }
        }
    </style>
    @endpush

<div class="statistik-body">

    <!-- ================== MOBILE (ENHANCED CARDS) ================== -->
    <div class="mobile-view">
        @foreach ($this->data as $row)
            @php
                $totalRow = $row->pns_l + $row->pns_p + $row->pppk_l + $row->pppk_p + $row->pppk_pw_l + $row->pppk_pw_p;
                $pnsTotal = $row->pns_l + $row->pns_p;
                $pppkTotal = $row->pppk_l + $row->pppk_p;
                $pppkPwTotal = $row->pppk_pw_l + $row->pppk_pw_p;
            @endphp

            <div class="mobile-stat-card">
                <div class="mobile-card-header">
                    <span class="mobile-golongan">{{ $row->golongan ?? '-' }}</span>
                    <span class="mobile-total-badge">
                        Total: {{ number_format($totalRow) }}
                    </span>
                </div>

                <div class="mobile-stats-grid">
                    <!-- PNS Card -->
                    <div class="mobile-stat-item pns">
                        <div class="mobile-stat-label">
                            <span>👔</span> PNS
                        </div>
                        <div class="mobile-gender-stats">
                            <div class="mobile-gender-item">
                                <div>👨 Laki</div>
                                <div class="mobile-gender-value">{{ number_format($row->pns_l) }}</div>
                            </div>
                            <div class="mobile-gender-item">
                                <div>👩 Perempuan</div>
                                <div class="mobile-gender-value">{{ number_format($row->pns_p) }}</div>
                            </div>
                        </div>
                        <div class="mobile-total-section">
                            Total: {{ number_format($pnsTotal) }}
                        </div>
                    </div>

                    <!-- PPPK Card -->
                    <div class="mobile-stat-item pppk">
                        <div class="mobile-stat-label">
                            <span>📋</span> PPPK
                        </div>
                        <div class="mobile-gender-stats">
                            <div class="mobile-gender-item">
                                <div>👨 Laki</div>
                                <div class="mobile-gender-value">{{ number_format($row->pppk_l) }}</div>
                            </div>
                            <div class="mobile-gender-item">
                                <div>👩 Perempuan</div>
                                <div class="mobile-gender-value">{{ number_format($row->pppk_p) }}</div>
                            </div>
                        </div>
                        <div class="mobile-total-section">
                            Total: {{ number_format($pppkTotal) }}
                        </div>
                    </div>

                    <!-- PPPK PW Card (full width) -->
                    <div class="mobile-stat-item pppk-pw" style="grid-column: span 2;">
                        <div class="mobile-stat-label">
                            <span>⏰</span> PPPK Paruh Waktu
                        </div>
                        <div class="mobile-gender-stats">
                            <div class="mobile-gender-item">
                                <div>👨 Laki</div>
                                <div class="mobile-gender-value">{{ number_format($row->pppk_pw_l) }}</div>
                            </div>
                            <div class="mobile-gender-item">
                                <div>👩 Perempuan</div>
                                <div class="mobile-gender-value">{{ number_format($row->pppk_pw_p) }}</div>
                            </div>
                        </div>
                        <div class="mobile-total-section">
                            Total: {{ number_format($pppkPwTotal) }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mobile-progress">
                    <div class="mobile-progress-item">
                        <div class="mobile-progress-label">
                            <span>PNS</span>
                            <span>{{ number_format($pnsTotal) }} ({{ $totalRow > 0 ? round(($pnsTotal/$totalRow)*100) : 0 }}%)</span>
                        </div>
                        <div class="mobile-progress-bar">
                            <div class="mobile-progress-fill" style="width: {{ $totalRow > 0 ? ($pnsTotal/$totalRow)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mobile-progress-item">
                        <div class="mobile-progress-label">
                            <span>PPPK</span>
                            <span>{{ number_format($pppkTotal) }} ({{ $totalRow > 0 ? round(($pppkTotal/$totalRow)*100) : 0 }}%)</span>
                        </div>
                        <div class="mobile-progress-bar">
                            <div class="mobile-progress-fill" style="width: {{ $totalRow > 0 ? ($pppkTotal/$totalRow)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mobile-progress-item">
                        <div class="mobile-progress-label">
                            <span>PPPK PW</span>
                            <span>{{ number_format($pppkPwTotal) }} ({{ $totalRow > 0 ? round(($pppkPwTotal/$totalRow)*100) : 0 }}%)</span>
                        </div>
                        <div class="mobile-progress-bar">
                            <div class="mobile-progress-fill" style="width: {{ $totalRow > 0 ? ($pppkPwTotal/$totalRow)*100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Overall Statistics -->
        @php
            // Gunakan array_sum untuk menghitung total dari array
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
            $totalGolongan = count($this->data);
        @endphp

        <div class="mobile-overall-stats">
            <div class="mobile-overall-title">
                📊 REKAPITULASI KESELURUHAN
            </div>
            <div class="mobile-overall-grid">
                <div class="mobile-overall-item">
                    <div class="mobile-overall-label">👔 PNS</div>
                    <div class="mobile-overall-value">{{ number_format($totalPns) }}</div>
                </div>
                <div class="mobile-overall-item">
                    <div class="mobile-overall-label">📋 PPPK</div>
                    <div class="mobile-overall-value">{{ number_format($totalPppk) }}</div>
                </div>
                <div class="mobile-overall-item">
                    <div class="mobile-overall-label">⏰ PPPK PW</div>
                    <div class="mobile-overall-value">{{ number_format($totalPppkPw) }}</div>
                </div>
                <div class="mobile-overall-item">
                    <div class="mobile-overall-label">🏢 Total Golongan</div>
                    <div class="mobile-overall-value">{{ number_format($totalGolongan) }}</div>
                </div>
                <div class="mobile-overall-item mobile-grand-total">
                    <div class="mobile-overall-label">🎯 GRAND TOTAL</div>
                    <div class="mobile-overall-value">{{ number_format($grandTotal) }}</div>
                </div>
            </div>
        </div>

        <!-- Additional Info Card -->
        <div style="background: #f8fafc; border-radius: 12px; padding: 12px; margin-top: 16px; text-align: center; border: 1px solid #e2e8f0;">
            <div style="font-size: 12px; color: #64748b;">
                <span>📱 </span>Geser untuk melihat detail |
                <span>🔄 </span>{{ now()->format('d/m/Y H:i') }}
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
                @php
                    $totalPnsL = array_sum(array_column($this->data, 'pns_l'));
                    $totalPnsP = array_sum(array_column($this->data, 'pns_p'));
                    $totalPppkL = array_sum(array_column($this->data, 'pppk_l'));
                    $totalPppkP = array_sum(array_column($this->data, 'pppk_p'));
                    $totalPppkPwL = array_sum(array_column($this->data, 'pppk_pw_l'));
                    $totalPppkPwP = array_sum(array_column($this->data, 'pppk_pw_p'));
                @endphp
                <tr>
                    <td class="text-left font-bold">TOTAL</td>

                    <td class="font-bold">{{ number_format($totalPnsL) }}</td>
                    <td class="font-bold">{{ number_format($totalPnsP) }}</td>
                    <td class="font-bold">{{ number_format($totalPnsL + $totalPnsP) }}</td>

                    <td class="font-bold">{{ number_format($totalPppkL) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkP) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkL + $totalPppkP) }}</td>

                    <td class="font-bold">{{ number_format($totalPppkPwL) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkPwP) }}</td>
                    <td class="font-bold">{{ number_format($totalPppkPwL + $totalPppkPwP) }}</td>

                    <td class="font-bold">{{ number_format($this->totalPegawai) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
</x-filament::page>
