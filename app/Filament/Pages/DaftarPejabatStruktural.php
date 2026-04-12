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

            ->join(DB::raw("
                (
                    SELECT pegawai_id, MAX(tmt_jabatan) as max_tmt
                    FROM r_jabatans
                    GROUP BY pegawai_id
                ) as latest
            "), 'latest.pegawai_id', '=', 'p.id')

            ->join('r_jabatans as r', function ($join) {
                $join->on('r.pegawai_id', '=', 'latest.pegawai_id')
                     ->on('r.tmt_jabatan', '=', 'latest.max_tmt');
            })

            ->join('jabatans as j', 'r.jabatan_id', '=', 'j.id')
            ->leftJoin('unors as u', 'j.unor_induk_id', '=', 'u.id')

            ->select([
                'p.nama',
                'p.nip_baru',
                'j.jabatan_nama',
                'j.eselon as eselon_display',
                'u.nama as unor_nama',
            ])

            ->where('j.kel_jab', 'Struktural');

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
        $data = [
            'pejabat' => $this->pejabat,
            'filter' => $this->filterEselon,
            'date' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i')
        ];

        $pdf = Pdf::loadView(
            'filament.pages.exports.daftar-pejabat-pdf',
            $data
        );

        $pdf->setPaper([0, 0, 612, 936], 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Daftar_Pejabat_' . $this->filterEselon . '_' . date('YmdHis') . '.pdf');
    }
}
