<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class StatistikAgama extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sun';
    protected static ?string $modelLabel = 'Statistik Agama';
    protected string $view = 'filament.pages.statistik-agama';
    protected static ?string $navigationLabel = 'Statistik Agama';
    protected static ?string $title = 'Statistik Pegawai per Agama';
    protected static ?int $navigationSort = 4;
    protected static string|UnitEnum|null $navigationGroup = 'Statistik';

    public $data = [];

    public function mount()
    {
        // Pastikan nama kolom 'agama_nama' sesuai dengan di tabel staging_import Anda
        $this->data = DB::table('staging_import')
            ->selectRaw("
                COALESCE(agama_nama, 'Tidak Terisi') as agama,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
            ")
            ->groupBy('agama_nama')
            ->orderBy('agama_nama', 'asc')
            ->get()
            ->toArray();
    }

    public function exportPdf()
    {
        // Menggunakan timezone Jakarta agar jam tidak selisih 7 jam
        $this->date = now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i');

        $payload = [
            'date' => $this->date,
            'data' => $this->data,
        ];

        // Ganti nama view sesuai filenya (statistik-agama-pdf atau statistik-gender-pdf)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.exports.statistik-agama-pdf', $payload);

        // Set ukuran F4 Portrait
        $pdf->setPaper([0, 0, 612, 936], 'portrait');

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-laporan-' . date('YmdHis') . '.pdf');
    }
}
