<x-filament::page>
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

    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border">
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="jenisPegawai" class="border rounded-lg text-sm">
                    <option value="pns">PNS</option>
                    <option value="pppk">PPPK Penuh Waktu</option>
                </select>

                <x-filament::button wire:click="exportPdf" color="danger">
                    Export ke PDF
                </x-filament::button>
            </div>

            <div class="text-xs">
                {{ $this->judulJenisPegawai }}
            </div>
        </div>

        <div wire:loading>Memuat data...</div>

        <div class="overflow-x-auto bg-white border rounded-xl">
            <table class="w-full border-collapse text-sm min-w-[1200px]">
                <thead>
                    <tr>
                        <th class="border px-2 py-2" rowspan="2">Gol</th>
                        <th class="border px-2 py-2" colspan="4">Struktural</th>
                        <th class="border px-2 py-2" colspan="8">Fungsional</th>
                        <th class="border px-2 py-2" rowspan="2">Pelaksana</th>
                    </tr>
                    <tr>
                        <th class="border px-2 py-2">Eselon I</th>
                        <th class="border px-2 py-2">Eselon II</th>
                        <th class="border px-2 py-2">Eselon III</th>
                        <th class="border px-2 py-2">Eselon IV</th>
                        <th class="border px-2 py-2">Utama</th>
                        <th class="border px-2 py-2">Madya</th>
                        <th class="border px-2 py-2">Muda</th>
                        <th class="border px-2 py-2">Pertama</th>
                        <th class="border px-2 py-2">Penyelia</th>
                        <th class="border px-2 py-2">Mahir</th>
                        <th class="border px-2 py-2">Terampil</th>
                        <th class="border px-2 py-2">Pemula</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->data as $row)
                        <tr>
                            <td class="border px-2 py-1 font-semibold">{{ $row->gol }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->eselon_i) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->eselon_ii) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->eselon_iii) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->eselon_iv) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->utama) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->madya) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->muda) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->pertama) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->penyelia) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->mahir) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->terampil) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->pemula) }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($row->pelaksana) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="border px-2 py-2 text-center" colspan="14">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td class="border px-2 py-2">TOTAL</td>
                        @foreach($columns as $column)
                            <td class="border px-2 py-2 text-center">
                                {{ number_format(collect($this->data)->sum($column)) }}
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament::page>
