<x-filament::page>
    <div class="space-y-4">

        <div class="flex justify-between bg-white p-4 rounded-xl border">
            <div class="flex gap-4">

                <select wire:model.live.debounce.500ms="filterKategori" class="border rounded-lg text-sm">
                    <option value="Pilih Kategori">Pilih Kategori</option>
                    <option value="Fungsional Guru">Fungsional Guru</option>
                    <option value="Fungsional Kesehatan">Fungsional Kesehatan</option>
                    <option value="Fungsional Lainnya">Fungsional Lainnya</option>
                    <option value="Pelaksana">Pelaksana</option>
                </select>

                <x-filament::button wire:click="exportPdf" color="danger">
                    Export PDF
                </x-filament::button>

            </div>

            <div class="text-xs">
                {{ $this->pegawai->count() }} Data
            </div>
        </div>

        <div wire:loading>Memuat data...</div>

        <table class="w-full border text-sm">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA / NIP</th>
                    <th>JABATAN</th>
                    <th>UNIT KERJA</th>
                </tr>
            </thead>

            <tbody>
                @forelse($this->pegawai as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $p->nama }}<br>
                            {{ $p->nip_baru ?? '-' }}
                        </td>
                        <td>{{ $p->jabatan_nama }}</td>
                        <td>{{ $p->unor_nama }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</x-filament::page>
