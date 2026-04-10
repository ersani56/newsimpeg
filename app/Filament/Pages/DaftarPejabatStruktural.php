<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;

class DaftarPejabatStruktural extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Daftar Pejabat';
    protected string $view = 'filament.pages.daftar-pejabat-struktural';
    protected static ?string $title = 'Daftar Pejabat Struktural';
    protected static string|UnitEnum|null $navigationGroup = 'Daftar Pejabat'; // Atau grup lain sesuai keinginan Anda
    protected static ?int $navigationSort = 1;

   // Property untuk filter
public $filterEselon = 'semua';

    public function getPejabatProperty()
    {
        $query = DB::table('staging_import')
            ->select([
                'nama', 'nip_baru', 'jabatan_nama', 'unor_nama', 'gol_akhir_nama',
                DB::raw("
                    CASE
                        WHEN LOWER(jabatan_nama) = 'sekretaris daerah' THEN 'II/a'
                        WHEN LOWER(jabatan_nama) LIKE 'kepala dinas%' OR LOWER(jabatan_nama) LIKE 'kepala badan%' OR
                            LOWER(jabatan_nama) LIKE 'asisten bidang%' OR LOWER(jabatan_nama) LIKE 'asisten perekonomian%' OR
                            LOWER(jabatan_nama) LIKE 'asisten pemerintahan%' OR LOWER(jabatan_nama) LIKE 'inspektur%' OR
                            LOWER(jabatan_nama) = 'sekretaris dprd' OR LOWER(jabatan_nama) LIKE 'kepala satuan%' OR
                            LOWER(jabatan_nama) LIKE 'staf ahli%' THEN 'II/b'
                        WHEN (
                            LOWER(jabatan_nama) LIKE 'sekretaris dinas%' OR
                            LOWER(jabatan_nama) LIKE 'sekretaris badan%' OR
                            LOWER(jabatan_nama) LIKE 'sekretaris inspektorat%' OR
                            LOWER(jabatan_nama) LIKE 'camat%' OR
                            LOWER(jabatan_nama) LIKE 'kepala bagian%' OR
                            (
                                LOWER(jabatan_nama) = 'sekretaris' AND
                                LOWER(unor_nama) NOT LIKE '%kecamatan%' AND
                                LOWER(unor_nama) NOT LIKE '%kelurahan%'
                            )
                        ) THEN 'III/a'
                        WHEN LOWER(jabatan_nama) LIKE 'kepala bidang%' OR
                        LOWER(jabatan_nama) LIKE 'direktur rumah sakit%' OR LOWER(jabatan_nama) LIKE 'sekretaris kecamatan%' THEN 'III/b'
                        WHEN (LOWER(jabatan_nama) REGEXP 'lurah|kasi|kasubid|kasubbag|kepala seksi|kepala sub') THEN
                            CASE WHEN LOWER(unor_nama) LIKE '%kecamatan%' OR LOWER(unor_nama) LIKE '%kelurahan%' THEN 'IV/b' ELSE 'IV/a' END
                        ELSE 'Non-Eselon'
                    END as eselon_display
                ")
            ]);

        if ($this->filterEselon === 'eselon_2') {
            $query->havingRaw("eselon_display IN ('II/a', 'II/b')");
        } elseif ($this->filterEselon === 'eselon_3') {
            $query->havingRaw("eselon_display IN ('III/a', 'III/b')");
        } elseif ($this->filterEselon === 'eselon_4') {
            $query->havingRaw("eselon_display IN ('IV/a', 'IV/b')");
        } else {
            $query->having('eselon_display', '!=', 'Non-Eselon');
        }

        return $query->orderByRaw("FIELD(eselon_display, 'II/a', 'II/b', 'III/a', 'III/b', 'IV/a', 'IV/b')")
                     ->orderBy('nama', 'asc')
                     ->get();
    }

    public function exportPdf()
    {
        $data = [
            'pejabat' => $this->pejabat,
            'filter' => $this->filterEselon,
            'date' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.exports.daftar-pejabat-pdf', $data);
        $pdf->setPaper([0, 0, 612, 936], 'portrait'); // F4 Portrait

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Daftar_Pejabat_' . $this->filterEselon . '_' . date('YmdHis') . '.pdf');
    }
}
