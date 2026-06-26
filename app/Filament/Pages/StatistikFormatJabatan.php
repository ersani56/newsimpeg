<?php

namespace App\Filament\Pages;

use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class StatistikFormatJabatan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Format Jabatan';
    protected static ?string $title = 'Statistik Format Jabatan';
    protected static string|UnitEnum|null $navigationGroup = 'Statistik';
    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.statistik-format-jabatan';

    public function getPnsDataProperty()
    {
        return $this->getDataForJenisPegawai('pns');
    }

    public function getPppkDataProperty()
    {
        return $this->getDataForJenisPegawai('pppk');
    }

    protected function getDataForJenisPegawai(string $jenisPegawai)
    {
        return $this->baseQuery($jenisPegawai)
            ->groupBy('s.gol_akhir_nama')
            ->orderByRaw("
                FIELD(
                    s.gol_akhir_nama,
                    'XI', 'X', 'IX', 'VIII', 'VII', 'VI', 'V',
                    'IV/e', 'IV/d', 'IV/c', 'IV/b', 'IV/a',
                    'III/d', 'III/c', 'III/b', 'III/a',
                    'II/d', 'II/c', 'II/b', 'II/a',
                    'I/d', 'I/c', 'I/b', 'I/a', 'I'
                )
            ")
            ->orderBy('s.gol_akhir_nama')
            ->get();
    }

    protected function baseQuery(string $jenisPegawai)
    {
        $query = DB::table('staging_import as s')
            ->leftJoin('jabatans as j', 's.jabatan_id', '=', 'j.jabatan_id')
            ->selectRaw("
                COALESCE(NULLIF(s.gol_akhir_nama, ''), '-') AS gol,

                SUM(CASE WHEN j.eselon IN ('I/a', 'I/b') THEN 1 ELSE 0 END) AS eselon_i,
                SUM(CASE WHEN j.eselon IN ('II/a', 'II/b') THEN 1 ELSE 0 END) AS eselon_ii,
                SUM(CASE WHEN j.eselon IN ('III/a', 'III/b') THEN 1 ELSE 0 END) AS eselon_iii,
                SUM(CASE WHEN j.eselon IN ('IV/a', 'IV/b') THEN 1 ELSE 0 END) AS eselon_iv,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])utama($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS utama,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])madya($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS madya,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])muda($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS muda,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])pertama($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS pertama,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])penyelia($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS penyelia,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])mahir($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS mahir,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])terampil($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS terampil,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '2'
                        AND LOWER(s.jabatan_nama) REGEXP '(^|[[:space:]/-])pemula($|[[:space:]/-])'
                    THEN 1 ELSE 0
                END) AS pemula,

                SUM(CASE
                    WHEN s.jenis_jabatan_id = '4'
                    THEN 1 ELSE 0
                END) AS pelaksana
            ");

        if ($jenisPegawai === 'pppk') {
            return $query->where('s.kedudukan_hukum_id', '71');
        }

        return $query->whereIn('s.kedudukan_hukum_id', ['01', '02', '03', '04', '13', '15']);
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('filament.pages.exports.statistik-format-jabatan-pdf', [
            'pnsData' => $this->pnsData,
            'pppkData' => $this->pppkData,
            'date' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i'),
        ]);

        $pdf->setPaper([0, 0, 936, 612], 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'statistik-format-jabatan-' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
