<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use BackedEnum;
use UnitEnum;

class StatistikPend extends Page
{
    protected static string|BackedEnum|null $navigationIcon= 'heroicon-o-academic-cap';
    protected string $view = 'filament.pages.statistik-pend';
    protected static ?string $navigationLabel = 'Statistik Pendidikan';
    protected static ?string $title = 'Statistik Pegawai per Tingkat Pendidikan';
    protected static ?int $navigationSort = 2;
    protected static string|UnitEnum|null $navigationGroup = 'Statistik';

    public $data = [];

    public function mount()
    {
        $this->data = DB::table('staging_import')
            ->selectRaw("
                tingkat_pendidikan_id,
                tingkat_pendidikan_nama as pendidikan,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
            ")
            ->groupBy('tingkat_pendidikan_id', 'tingkat_pendidikan_nama')
            ->orderBy('tingkat_pendidikan_id')
            ->get()
            ->toArray();
    }


    public function exportPdf()
    {
        $pdf = Pdf::loadView('filament.pages.exports.statistik-pendidikan-pdf', [
            'date' => now()->translatedFormat('d F Y H:i'),
            'data' => $this->data,
        ]);

        // Custom Paper Size untuk F4 dalam satuan Point (1 inci = 72 pt)
        // F4 = 8.5 inci x 13 inci => 612pt x 936pt
        $pdf->setPaper([0, 0, 612, 936], 'portrait');

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-pendidikan-' . date('Ymd') . '.pdf');
    }
}
