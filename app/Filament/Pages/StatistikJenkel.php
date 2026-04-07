<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use BackedEnum;
use UnitEnum;

class StatistikJenkel extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected string $view = 'filament.pages.statistik-jenkel';
    protected static ?string $modelLabel = 'statistik jenis kelamin';
    protected static ?string $navigationLabel = 'Statistik Jenis Kelamin';
    protected static ?string $title = 'Statistik Pegawai per Jenis Kelamin';
    protected static ?int $navigationSort = 5;
    protected static string|UnitEnum|null $navigationGroup = 'Statistik';
    public $data = [];

    public function mount()
    {
        // Mengelompokkan langsung berdasarkan Jenis Kelamin
        $this->data = DB::table('staging_import')
            ->selectRaw("
                CASE
                    WHEN LOWER(jenis_kelamin) LIKE '%m%' THEN 'Laki-laki'
                    WHEN LOWER(jenis_kelamin) LIKE '%f%' THEN 'Perempuan'
                    ELSE 'Tidak Diketahui'
                END as gender,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') THEN 1 ELSE 0 END) as pns,
                SUM(CASE WHEN kedudukan_hukum_id = '71' THEN 1 ELSE 0 END) as pppk,
                SUM(CASE WHEN kedudukan_hukum_id = '101' THEN 1 ELSE 0 END) as pppk_pw
            ")
            ->groupBy('gender')
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
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.exports.statistik-gender-pdf', $payload);

        // Set ukuran F4 Portrait
        $pdf->setPaper([0, 0, 612, 936], 'portrait');

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-laporan-' . date('YmdHis') . '.pdf');
    }

}
