<?php

namespace App\Filament\Pages;

use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class DaftarPejabatStruktural extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Daftar Pejabat';
    protected string $view = 'filament.pages.daftar-pejabat-struktural';
    protected static ?string $title = 'Daftar Pejabat Struktural';
    protected static string|UnitEnum|null $navigationGroup = 'Daftar Pegawai'; // Atau grup lain sesuai keinginan Anda
    protected static ?int $navigationSort = 1;
public $filterEselon = 'semua';

    public function getPejabatProperty()
    {
        $query = DB::table('pegawais as p')

            // ambil jabatan dari snapshot
            ->leftJoin('jabatans as j', 'p.jabatan_id', '=', 'j.jabatan_id')
            ->select([
                'p.nama',
                'p.nip_baru',
                'j.jabatan_nama',
                'j.eselon as eselon_display',
                'j.unor_nama',
            ])

            ->where('j.kel_jab', 'struktural');

        // FILTER
        if ($this->filterEselon === 'eselon_2') {
            $query->whereIn('j.eselon', ['II/a', 'II/b']);
        } elseif ($this->filterEselon === 'eselon_3') {
            $query->whereIn('j.eselon', ['III/a', 'III/b']);
        } elseif ($this->filterEselon === 'eselon_4') {
            $query->whereIn('j.eselon', ['IV/a', 'IV/b']);
        } else {
            $query->whereNotNull('j.eselon');
        }

        return $query
            ->orderByRaw("FIELD(j.eselon, 'II/a', 'II/b', 'III/a', 'III/b', 'IV/a', 'IV/b')")
            ->orderBy('p.nama', 'asc')
            ->get();
    }

    public function exportPdf()
    {
        $data = collect($this->pejabat); // ambil dari accessor

        // amankan null
        $data = $data->map(function ($row) {
            $row->nama = $row->nama ?? '-';
            $row->nip_baru = $row->nip_baru ?? '-';
            $row->jabatan_nama = $row->jabatan_nama ?? '-';
            $row->eselon_display = $row->eselon_display ?? '-';
            $row->unor_nama = $row->unor_nama ?? '-';
            return $row;
        });

        $payload = [
            'date' => now()->translatedFormat('d F Y H:i'),
            'data' => $data,
            'filter' => $this->filterEselon,
        ];

        $pdf = Pdf::loadView('filament.pages.exports.daftar-pejabat-pdf', $payload);

        $pdf->setPaper([0, 0, 612, 936], 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'daftar-pejabat-struktural-' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

}
