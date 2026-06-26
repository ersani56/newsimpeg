<x-filament::page>
    @push('styles')
        <style>
            .format-page-title {
                margin: 0 0 24px;
                font-size: 40px;
                line-height: 1.1;
                font-weight: 800;
                color: #050505;
            }

            .format-card {
                background: #fff;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
            }

            .format-header {
                min-height: 86px;
                padding: 20px 28px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
            }

            .format-header-title {
                font-size: 22px;
                font-weight: 800;
                letter-spacing: 0;
            }

            .format-actions {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .format-select {
                height: 44px;
                min-width: 180px;
                border: 0;
                border-radius: 8px;
                padding: 0 12px;
                color: #111827;
                font-weight: 700;
                background: #fff;
            }

            .format-button {
                min-height: 46px;
                border: 0;
                border-radius: 10px;
                padding: 0 18px;
                color: #fff;
                font-size: 16px;
                font-weight: 800;
                cursor: pointer;
            }

            .format-button-pdf {
                background: #e92727;
            }

            .format-button-excel {
                background: #12b981;
            }

            .format-table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                background: #fff;
            }

            .format-table {
                width: 100%;
                min-width: 1240px;
                border-collapse: collapse;
                border-spacing: 0;
                color: #050505;
            }

            .format-table th,
            .format-table td {
                border: 2px solid #111;
                padding: 14px 12px;
                text-align: center;
                vertical-align: middle;
                font-size: 18px;
            }

            .format-table th {
                background: #e5e7eb;
                font-weight: 800;
            }

            .format-table td {
                font-weight: 600;
                background: #fff;
            }

            .format-table .gol-column {
                min-width: 130px;
                text-align: left;
                font-weight: 800;
            }

            .format-table .gol-value {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .format-table tfoot td {
                background: #f3f4f6;
                font-weight: 900;
            }

            @media (max-width: 768px) {
                .format-page-title {
                    font-size: 30px;
                }

                .format-header {
                    align-items: stretch;
                }

                .format-actions,
                .format-select,
                .format-button {
                    width: 100%;
                }

                .format-table th,
                .format-table td {
                    font-size: 14px;
                    padding: 10px 8px;
                }
            }
        </style>
    @endpush

    @php
        $columns = [
            'eselon_i',
            'eselon_ii',
            'eselon_iii',
            'eselon_iv',
            'utama',
            'madya',
            'muda',
            'pertama',
            'penyelia',
            'mahir',
            'terampil',
            'pemula',
            'pelaksana',
        ];
    @endphp

    <h1 class="format-page-title">Statistik Format Jabatan</h1>

    <div class="format-card">
        <div class="format-header">
            <div class="format-header-title">
                STATISTIK FORMAT JABATAN - {{ strtoupper($this->judulJenisPegawai) }}
            </div>

            <div class="format-actions">
                <select wire:model.live="jenisPegawai" class="format-select">
                    <option value="pns">PNS</option>
                    <option value="pppk">PPPK Penuh Waktu</option>
                </select>

                <button type="button" class="format-button format-button-pdf" wire:click="exportPdf">
                    Export ke PDF
                </button>

                <button type="button" class="format-button format-button-excel" onclick="exportFormatJabatanToExcel()">
                    Export ke Excel
                </button>
            </div>
        </div>

        <div wire:loading class="p-4 text-sm">Memuat data...</div>

        <div class="format-table-wrapper">
            <table class="format-table" id="format-jabatan-table">
                <thead>
                    <tr>
                        <th class="gol-column" rowspan="2">Golongan</th>
                        <th colspan="4">Struktural</th>
                        <th colspan="8">Fungsional</th>
                        <th rowspan="2">Pelaksana</th>
                    </tr>
                    <tr>
                        <th>Eselon I</th>
                        <th>Eselon II</th>
                        <th>Eselon III</th>
                        <th>Eselon IV</th>
                        <th>Utama</th>
                        <th>Madya</th>
                        <th>Muda</th>
                        <th>Pertama</th>
                        <th>Penyelia</th>
                        <th>Mahir</th>
                        <th>Terampil</th>
                        <th>Pemula</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->data as $row)
                        <tr>
                            <td class="gol-column">
                                <span class="gol-value">{{ $row->gol }}</span>
                            </td>
                            <td>{{ number_format($row->eselon_i) }}</td>
                            <td>{{ number_format($row->eselon_ii) }}</td>
                            <td>{{ number_format($row->eselon_iii) }}</td>
                            <td>{{ number_format($row->eselon_iv) }}</td>
                            <td>{{ number_format($row->utama) }}</td>
                            <td>{{ number_format($row->madya) }}</td>
                            <td>{{ number_format($row->muda) }}</td>
                            <td>{{ number_format($row->pertama) }}</td>
                            <td>{{ number_format($row->penyelia) }}</td>
                            <td>{{ number_format($row->mahir) }}</td>
                            <td>{{ number_format($row->terampil) }}</td>
                            <td>{{ number_format($row->pemula) }}</td>
                            <td>{{ number_format($row->pelaksana) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="gol-column">TOTAL</td>
                        @foreach($columns as $column)
                            <td>{{ number_format(collect($this->data)->sum($column)) }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function exportFormatJabatanToExcel() {
            const table = document.getElementById('format-jabatan-table');
            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.table_to_sheet(table);

            XLSX.utils.book_append_sheet(workbook, worksheet, 'Format Jabatan');
            XLSX.writeFile(workbook, 'statistik_format_jabatan_' + new Date().getTime() + '.xlsx');
        }
    </script>
</x-filament::page>
