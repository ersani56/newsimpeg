<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use BackedEnum;
use UnitEnum;

class StatistikJenisKelamin extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected string $view = 'filament.pages.statistik-jenis-kelamin';
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
        // Format data agar sesuai dengan template umum yang sudah kita buat sebelumnya
        // Kita manipulasi sedikit agar pns_l diisi total pns, dst agar template PDF bisa membaca
        $formattedData = collect($this->data)->map(function($item) {
            return (object)[
                'label_custom' => $item->gender,
                'pns_l' => $item->pns, 'pns_p' => 0, // Kita pakai kolom L saja untuk total per baris gender
                'pppk_l' => $item->pppk, 'pppk_p' => 0,
                'pppk_pw_l' => $item->pppk_pw, 'pppk_pw_p' => 0,
            ];
        });

        $totals = [
            'pns_l' => $formattedData->sum('pns_l'), 'pns_p' => 0,
            'pppk_l' => $formattedData->sum('pppk_l'), 'pppk_p' => 0,
            'pppk_pw_l' => $formattedData->sum('pppk_pw_l'), 'pppk_pw_p' => 0,
        ];

        $pdf = Pdf::loadView('filament.pages.exports.statistik-umum-pdf', [
            'title' => 'STATISTIK PEGAWAI PER JENIS KELAMIN',
            'label' => 'Jenis Kelamin',
            'data' => $formattedData,
            'totals' => $totals,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-gender-' . now()->format('Y-m-d') . '.pdf');
    }
}
