<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use UnitEnum;

class StatistikPend extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected string $view = 'filament.pages.statistik-pend';
    protected static ?string $navigationLabel = 'Statistik Pendidikan';
    protected static ?string $title = 'Statistik Pegawai Berdasarkan Pendidikan';
    protected static ?int $navigationSort = 2;
    protected static UnitEnum|string|null $navigationGroup = 'Statistik';

    public $data = [];

    public function mount()
    {
        // Ganti 'pendidikan_nama' dengan nama kolom tingkat pendidikan di tabel Anda (misal: S1, D3, SMA)
        $this->data = DB::table('staging_import')
            ->selectRaw("
                tingkat_pendidikan_nama as pendidikan,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
            ")
            ->groupBy('tingkat_pendidikan_nama')
            ->get()
            ->toArray();
    }

    public function exportPdf()
    {
        $totals = [
            'pns_l' => collect($this->data)->sum('pns_l'),
            'pns_p' => collect($this->data)->sum('pns_p'),
            'pppk_l' => collect($this->data)->sum('pppk_l'),
            'pppk_p' => collect($this->data)->sum('pppk_p'),
            'pppk_pw_l' => collect($this->data)->sum('pppk_pw_l'),
            'pppk_pw_p' => collect($this->data)->sum('pppk_pw_p'),
        ];

        $pdf = Pdf::loadView('filament.pages.exports.statistik-umum-pdf', [
            'title' => 'STATISTIK PEGAWAI PER TINGKAT PENDIDIKAN',
            'label' => 'Tingkat Pendidikan',
            'data' => $this->data,
            'totals' => $totals,
            'date' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('folio', 'landscape');

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-pendidikan-' . now()->format('Y-m-d') . '.pdf');
    }
}
