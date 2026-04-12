<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use UnitEnum;

class StatistikJabatan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $modelLabel = 'Statistik Jabatan';
    protected string $view = 'filament.pages.statistik-jabatan';
    protected static ?string $navigationLabel = 'Statistik Jabatan';
    protected static ?string $title = 'Statistik Pegawai per Jenis Jabatan';
    protected static ?int $navigationSort = 3;
    protected static string|UnitEnum|null $navigationGroup = 'Statistik';

    public $data = [];

    public function mount()
    {
        $this->data = DB::table('pegawais as p')

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
            ->leftJoin('jenis_jabatans as jj', 'j.jenis_jabatan_id', '=', 'jj.id')

            ->selectRaw("
                CASE
                    -- STRUKTURAL (pakai eselon langsung)
                    WHEN jj.nama = 'struktural' AND j.eselon IS NOT NULL THEN CONCAT('Eselon ', j.eselon)

                    -- FUNGSIONAL
                    WHEN LOWER(j.kel_jab) = 'jf guru' THEN 'Fungsional Guru'
                    WHEN LOWER(j.kel_jab) = 'jf kesehatan' THEN 'Fungsional Kesehatan'
                    WHEN LOWER(j.kel_jab) = 'jf lainnya' THEN 'Fungsional Lainnya'

                    -- PELAKSANA
                    WHEN jj.nama = 'pelaksana' THEN 'Pelaksana'

                    ELSE 'Lainnya'
                END as kelompok_jabatan,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id IN ('01','02','03','13','15','04')
                    AND LOWER(p.jenis_kelamin) = 'l' THEN 1 ELSE 0 END) as pns_l,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id IN ('01','02','03','13','15','04')
                    AND LOWER(p.jenis_kelamin) = 'p' THEN 1 ELSE 0 END) as pns_p,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id = '71'
                    AND LOWER(p.jenis_kelamin) = 'l' THEN 1 ELSE 0 END) as pppk_l,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id = '71'
                    AND LOWER(p.jenis_kelamin) = 'p' THEN 1 ELSE 0 END) as pppk_p,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id = '101'
                    AND LOWER(p.jenis_kelamin) = 'l' THEN 1 ELSE 0 END) as pppk_pw_l,

                SUM(CASE
                    WHEN p.kedudukan_hukum_id = '101'
                    AND LOWER(p.jenis_kelamin) = 'p' THEN 1 ELSE 0 END) as pppk_pw_p
            ")

            ->groupBy('kelompok_jabatan')

            ->orderByRaw("
                CASE
                    WHEN kelompok_jabatan = 'Eselon II/a' THEN 1
                    WHEN kelompok_jabatan = 'Eselon II/b' THEN 2
                    WHEN kelompok_jabatan = 'Eselon III/a' THEN 3
                    WHEN kelompok_jabatan = 'Eselon III/b' THEN 4
                    WHEN kelompok_jabatan = 'Eselon IV/a' THEN 5
                    WHEN kelompok_jabatan = 'Eselon IV/b' THEN 6
                    WHEN kelompok_jabatan = 'Fungsional Guru' THEN 7
                    WHEN kelompok_jabatan = 'Fungsional Kesehatan' THEN 8
                    WHEN kelompok_jabatan = 'Fungsional Lainnya' THEN 9
                    WHEN kelompok_jabatan = 'Pelaksana' THEN 10
                    ELSE 11
                END
            ")

            ->get()
            ->toArray();
    }
        public function exportPdf()
        {
            $payload = [
                'date' => now()->translatedFormat('d F Y H:i'),
                'data' => $this->data,
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.pages.exports.statistik-jabatan-pdf', $payload);

            // Set kertas F4 (8.5 x 13 inci)
            $pdf->setPaper([0, 0, 612, 936], 'portrait');

            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, 'statistik-jabatan-' . date('YmdHis') . '.pdf');
        }
}
