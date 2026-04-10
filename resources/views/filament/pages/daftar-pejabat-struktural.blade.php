<x-filament::page>
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-300 shadow-sm">
            <div class="flex items-center gap-4">
                <select wire:model.live="filterEselon" class="border-gray-300 rounded-lg text-sm">
                    <option value="semua">Semua Eselon</option>
                    <option value="eselon_2">Eselon II</option>
                    <option value="eselon_3">Eselon III</option>
                    <option value="eselon_4">Eselon IV</option>
                </select>

                <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
                    Export PDF (F4)
                </x-filament::button>
            </div>
        </div>

        <div class="bg-white border border-black overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-black">
                        <th class="border-r border-black px-2 py-3 text-center text-xs font-bold uppercase" width="40">NO</th>
                        <th class="border-r border-black px-4 py-3 text-xs font-bold uppercase">NAMA / NIP / GOL</th>
                        <th class="border-r border-black px-4 py-3 text-xs font-bold uppercase">JABATAN</th>
                        <th class="border-r border-black px-4 py-3 text-xs font-bold uppercase text-center" width="80">ESELON</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase">UNIT KERJA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->pejabat as $index => $p)
                        <tr class="border-b border-black">
                            <td class="border-r border-black px-2 py-3 text-center text-sm">{{ $index + 1 }}</td>
                            <td class="border-r border-black px-4 py-3">
                                <div class="font-bold text-sm">{{ $p->nama }}</div>
                                <div class="text-xs font-mono">NIP. {{ $p->nip_baru }}</div>
                                <div class="text-[10px] text-gray-600">Pangkat/Gol: {{ $p->gol_akhir_nama }}</div>
                            </td>
                            <td class="border-r border-black px-4 py-3 text-sm font-medium">{{ $p->jabatan_nama }}</td>
                            <td class="border-r border-black px-4 py-3 text-center font-bold text-xs">{{ $p->eselon_display }}</td>
                            <td class="px-4 py-3 text-[11px] uppercase">{{ $p->unor_nama }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
