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
        $totals = [
            'pns_l' => collect($this->data)->sum('pns_l'),
            'pns_p' => collect($this->data)->sum('pns_p'),
            'pppk_l' => collect($this->data)->sum('pppk_l'),
            'pppk_p' => collect($this->data)->sum('pppk_p'),
            'pppk_pw_l' => collect($this->data)->sum('pppk_pw_l'),
            'pppk_pw_p' => collect($this->data)->sum('pppk_pw_p'),
        ];

        $pdf = Pdf::loadView('filament.pages.exports.statistik-umum-pdf', [
            'title' => 'STATISTIK PEGAWAI PER AGAMA',
            'label' => 'Agama',
            'data' => $this->data,
            'totals' => $totals,
            'date' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('folio', 'portrait'); // Agama biasanya tidak banyak baris, portrait cukup

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-agama-' . now()->format('Y-m-d') . '.pdf');
    }
}
