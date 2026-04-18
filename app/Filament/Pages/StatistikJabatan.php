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
            $this->data = $this->getData();
        }

        public function getData()
        {
            $query = DB::table('pegawais as p')
                // Gunakan LEFT JOIN agar jika jabatan tidak ditemukan, pegawai tetap terhitung
                ->leftJoin('jabatans as j', 'p.jabatan_id', '=', 'j.jabatan_id')
                ->selectRaw("
                    CASE
                        WHEN j.kel_jab IS NULL THEN 'Belum Dikategorikan'

                        WHEN LOWER(j.kel_jab) IN ('jf guru', 'jf kesehatan', 'jf lainnya') THEN j.kel_jab
                        WHEN LOWER(j.kel_jab) = 'pelaksana' THEN 'pelaksana'
                        WHEN LOWER(j.kel_jab) = 'struktural' AND j.eselon IS NOT NULL THEN j.eselon
                    END as kelompok,

                    SUM(CASE WHEN p.kedudukan_hukum_id IN (1,2,3,4,13,15) AND LOWER(p.jenis_kelamin) = 'm' THEN 1 ELSE 0 END) as pns_l,
                    SUM(CASE WHEN p.kedudukan_hukum_id IN (1,2,3,4,13,15) AND LOWER(p.jenis_kelamin) = 'f' THEN 1 ELSE 0 END) as pns_p,
                    SUM(CASE WHEN p.kedudukan_hukum_id = 71 AND LOWER(p.jenis_kelamin) = 'm' THEN 1 ELSE 0 END) as pppk_l,
                    SUM(CASE WHEN p.kedudukan_hukum_id = 71 AND LOWER(p.jenis_kelamin) = 'f' THEN 1 ELSE 0 END) as pppk_p,
                    SUM(CASE WHEN p.kedudukan_hukum_id = 101 AND LOWER(p.jenis_kelamin) = 'm' THEN 1 ELSE 0 END) as pppk_pw_l,
                    SUM(CASE WHEN p.kedudukan_hukum_id = 101 AND LOWER(p.jenis_kelamin) = 'f' THEN 1 ELSE 0 END) as pppk_pw_p
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
