<?php

namespace App\Filament\Pages;

use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
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
            $this->data = $this->getData();
        }

        public function getData()
        {
            $query = DB::table('staging_import as s')
                // Statistik jabatan harus mengikuti file import terbaru.
                // Jika referensi jabatan belum ada/kosong, pegawai tetap dihitung.
                ->leftJoin('jabatans as j', 's.jabatan_id', '=', 'j.jabatan_id')
                ->selectRaw("
                    CASE
                        WHEN LOWER(j.kel_jab) = 'struktural' AND j.eselon IN ('II/a', 'II/b', 'III/a', 'III/b', 'IV/a', 'IV/b') THEN j.eselon
                        WHEN LOWER(j.jabatan_nama) LIKE '%kepala sekolah%' THEN 'jf guru'
                        WHEN LOWER(j.kel_jab) IN ('jf guru', 'jf kesehatan', 'jf lainnya') THEN j.kel_jab
                        WHEN LOWER(j.kel_jab) = 'fungsional' AND LOWER(j.jabatan_nama) LIKE '%guru%' THEN 'jf guru'
                        WHEN LOWER(j.kel_jab) = 'fungsional'
                            AND (
                                LOWER(j.jabatan_nama) LIKE '%dokter%'
                                OR LOWER(j.jabatan_nama) LIKE '%perawat%'
                                OR LOWER(j.jabatan_nama) LIKE '%bidan%'
                                OR LOWER(j.jabatan_nama) LIKE '%apoteker%'
                                OR LOWER(j.jabatan_nama) LIKE '%farmasi%'
                                OR LOWER(j.jabatan_nama) LIKE '%sanitarian%'
                                OR LOWER(j.jabatan_nama) LIKE '%nutrisionis%'
                                OR LOWER(j.jabatan_nama) LIKE '%epidemiolog%'
                                OR LOWER(j.jabatan_nama) LIKE '%radiografer%'
                                OR LOWER(j.jabatan_nama) LIKE '%terapis gigi%'
                                OR LOWER(j.jabatan_nama) LIKE '%rekam medis%'
                            )
                        THEN 'jf kesehatan'
                        WHEN LOWER(j.kel_jab) = 'fungsional' THEN 'jf lainnya'
                        WHEN LOWER(j.kel_jab) = 'pelaksana' THEN 'pelaksana'
                        WHEN s.jenis_jabatan_id = '4'
                        THEN 'pelaksana'
                        ELSE 'Belum Dikategorikan'
                    END as kelompok,

                    SUM(CASE WHEN s.kedudukan_hukum_id IN ('01','02','03','04','13','15') AND LOWER(s.jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                    SUM(CASE WHEN s.kedudukan_hukum_id IN ('01','02','03','04','13','15') AND LOWER(s.jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                    SUM(CASE WHEN s.kedudukan_hukum_id = '71' AND LOWER(s.jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                    SUM(CASE WHEN s.kedudukan_hukum_id = '71' AND LOWER(s.jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                    SUM(CASE WHEN s.kedudukan_hukum_id = '101' AND LOWER(s.jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                    SUM(CASE WHEN s.kedudukan_hukum_id = '101' AND LOWER(s.jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
                ")
                ->groupBy('kelompok')
                ->get();

            // Urutan yang sesuai dengan hasil pengelompokan di atas
            // Mapping antara nilai di database dengan label yang ingin ditampilkan
            $kategori = [
                'II/a' => 'Eselon II/a',
                'II/b' => 'Eselon II/b',
                'III/a' => 'Eselon III/a',
                'III/b' => 'Eselon III/b',
                'IV/a' => 'Eselon IV/a',
                'IV/b' => 'Eselon IV/b',
                'pelaksana' => 'Pelaksana',
                'jf guru' => 'JF Guru',
                'jf kesehatan' => 'JF Kesehatan',
                'jf lainnya' => 'JF Lainnya',
                'Belum Dikategorikan' => 'Belum Dikategorikan',
            ];

            return collect($kategori)->map(function ($label, $db_key) use ($query) {
                $row = $query->firstWhere('kelompok', $db_key);

                return (object)[
                    'kelompok_jabatan' => $label, // Ini yang akan muncul di PDF/Tampilan
                    'pns_l' => $row->pns_l ?? 0,
                    'pns_p' => $row->pns_p ?? 0,
                    'pppk_l' => $row->pppk_l ?? 0,
                    'pppk_p' => $row->pppk_p ?? 0,
                    'pppk_pw_l' => $row->pppk_pw_l ?? 0,
                    'pppk_pw_p' => $row->pppk_pw_p ?? 0,
                ];
            })->toArray();
        }
        public function exportPdf()
        {
            $data = $this->data; // pastikan ini ada

            $payload = [
                'date' => now()->translatedFormat('d F Y H:i'),
                'data' => $data,
            ];

            $pdf = Pdf::loadView('filament.pages.exports.statistik-jabatan-pdf', $payload);

            $pdf->setPaper([0, 0, 612, 936], 'portrait');

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'statistik-jabatan-' . now()->format('YmdHis') . '.pdf');
        }
}
