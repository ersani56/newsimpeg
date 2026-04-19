<x-filament::page>
    <div class="space-y-4">

        <div class="flex justify-between bg-white p-4 rounded-xl border">
            <div class="flex gap-4">

                <select wire:model.live.debounce.500ms="filterEselon" class="border rounded-lg text-sm">
                    <option value="semua">Semua Eselon</option>
                    <option value="eselon_2">Eselon II</option>
                    <option value="eselon_3">Eselon III</option>
                    <option value="eselon_4">Eselon IV</option>
                </select>

                <x-filament::button wire:click="exportPdf" color="danger">
                    Export ke PDF
                </x-filament::button>

            </div>

            <div class="text-xs">
                {{ $this->pejabat->count() }} Data
            </div>
        </div>

        <div wire:loading>Memuat data...</div>

        <table class="w-full border text-sm">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>NIP</th>
                    <th>JABATAN</th>
                    <th>ESELON</th>
                    <th>UNIT KERJA</th>
                </tr>
            </thead>

            <tbody>
                @forelse($this->pejabat as $i => $p)
                    <tr>
                        <td>{{ $i + 1}}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->nip_baru }}</td>
                        <td>{{ $p->jabatan_nama }}</td>
                        <td>{{ $p->eselon_display }}</td>
                        <td>{{ $p->unor_nama }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</x-filament::page>
