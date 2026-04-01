<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class StatistikGolongan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected string $view = 'filament.pages.statistik-golongan';
    protected static ?string $navigationLabel = 'Statistik Golongan';
    protected static ?string $title = 'Statistik Pegawai per Golongan';
    protected static ?int $navigationSort = 1;

    public $data = [];
    public $totalPegawai = 0; // <-- Tambahkan properti ini
    public $totalPns = 0;     // <-- Opsional: total PNS
    public $totalPppk = 0;    // <-- Opsional: total PPPK
    public $totalPppkPw = 0;  // <-- Opsional: total PPPK Paruh Waktu

    public function mount()
    {
        $this->data = DB::table('staging_import')
            ->selectRaw("
                gol_akhir_nama as golongan,
                SUM(CASE WHEN kedudukan_hukum_id = '01' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                SUM(CASE WHEN kedudukan_hukum_id = '01' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
            ")
            ->groupBy('gol_akhir_nama')
            ->orderBy('gol_akhir_nama')
            ->get()
            ->toArray();

        // Hitung total seluruh pegawai
        $this->totalPegawai = DB::table('staging_import')->count();

        // Hitung total PNS, PPPK, PPPK Paruh Waktu (opsional)
        $this->totalPns = DB::table('staging_import')->where('kedudukan_hukum_id', '01')->count();
        $this->totalPppk = DB::table('staging_import')->where('kedudukan_hukum_id', '71')->count();
        $this->totalPppkPw = DB::table('staging_import')->where('kedudukan_hukum_id', '101')->count();
    }

    public function exportPdf()
    {
        // Hitung totals dari data yang sudah ada
        $totals = [
            'pns_l' => collect($this->data)->sum('pns_l'),
            'pns_p' => collect($this->data)->sum('pns_p'),
            'pns_total' => collect($this->data)->sum('pns_l') + collect($this->data)->sum('pns_p'),
            'pppk_l' => collect($this->data)->sum('pppk_l'),
            'pppk_p' => collect($this->data)->sum('pppk_p'),
            'pppk_total' => collect($this->data)->sum('pppk_l') + collect($this->data)->sum('pppk_p'),
            'pppk_pw_l' => collect($this->data)->sum('pppk_pw_l'),
            'pppk_pw_p' => collect($this->data)->sum('pppk_pw_p'),
            'pppk_pw_total' => collect($this->data)->sum('pppk_pw_l') + collect($this->data)->sum('pppk_pw_p'),
            'total' => $this->totalPegawai
        ];

        $pdf = Pdf::loadView('filament.pages.exports.statistik-golongan-pdf', [
            'data' => $this->data,
            'totals' => $totals,
            'totalPegawai' => $this->totalPegawai,
            'totalPns' => $this->totalPns,
            'totalPppk' => $this->totalPppk,
            'totalPppkPw' => $this->totalPppkPw,
            'date' => now()->format('d F Y H:i:s')
        ]);

        $pdf->setPaper('folio', 'portrait');

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-golongan-' . now()->format('Y-m-d') . '.pdf');
    }
}
