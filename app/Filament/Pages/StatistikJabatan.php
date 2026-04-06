<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
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

    /* public function mount()
    {
        // Sesuaikan 'jenis_jabatan_nama' dengan kolom di database Anda
        $this->data = DB::table('staging_import')
            ->selectRaw("
                COALESCE(jenis_jabatan_nama, 'Tanpa Jabatan') as jabatan,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
            ")
            ->groupBy('jenis_jabatan_nama')
            ->orderBy('jenis_jabatan_nama', 'asc')
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
            'title' => 'STATISTIK PEGAWAI PER JENIS JABATAN',
            'label' => 'Jenis Jabatan',
            'data' => $this->data,
            'totals' => $totals,
            'date' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('folio', 'landscape');

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-jabatan-' . now()->format('Y-m-d') . '.pdf');
    } */

        public function mount()
        {
            $this->data = DB::table('staging_import')
                ->selectRaw("
                    CASE
                        -- 1. Eselon II/a --
                        WHEN LOWER(jabatan_nama) = 'sekretaris daerah' THEN 'Eselon II/a'

                        -- 2. Eselon II/b --
                        WHEN LOWER(jabatan_nama) LIKE 'kepala dinas%' OR
                            LOWER(jabatan_nama) LIKE 'kepala badan%' OR
                            LOWER(jabatan_nama) LIKE 'inspektur%' OR
                            LOWER(jabatan_nama) LIKE 'asisten pemerintah%' OR
                            LOWER(jabatan_nama) LIKE 'asisten bidang%' OR
                            LOWER(jabatan_nama) LIKE 'asisten perekonomian%' OR
                            LOWER(jabatan_nama) LIKE 'staf ahli%' OR
                            LOWER(jabatan_nama) = 'sekretaris dprd' THEN 'Eselon II/b'

                        -- 3. Eselon III/a --
                        WHEN LOWER(jabatan_nama) LIKE 'sekretaris dinas%' OR
                            LOWER(jabatan_nama) LIKE 'sekretaris badan%' OR
                            LOWER(jabatan_nama) LIKE 'sekretaris inspektorat%' OR
                            LOWER(jabatan_nama) LIKE 'camat%' OR
                            LOWER(jabatan_nama) LIKE 'kepala bagian%' OR
                            LOWER(jabatan_nama) LIKE 'inspektur pembantu%' THEN 'Eselon III/a'

                        -- 4. Eselon III/b --
                        WHEN LOWER(jabatan_nama) LIKE 'kepala bidang%' OR
                            LOWER(jabatan_nama) LIKE 'sekretaris camat%' THEN 'Eselon III/b'

                        -- 5. Eselon IV/a & IV/b --
                        WHEN (LOWER(jabatan_nama) REGEXP 'lurah|kepala seksi|kepala sub bidang|kasi|kasubid|kasubbid|kasubbag|kepala sub bagian|kasubag') THEN
                            CASE
                                WHEN LOWER(SUBSTRING_INDEX(unor_nama, '-', -1)) LIKE '%kecamatan%' OR
                                    LOWER(SUBSTRING_INDEX(unor_nama, '-', -1)) LIKE '%kelurahan%'
                                THEN 'Eselon IV/b'
                                ELSE 'Eselon IV/a'
                            END

                        -- 6. Tenaga Guru (Fungsional) --
                        WHEN LOWER(jabatan_nama) LIKE '%guru%' THEN 'Fungsional Guru'

                        -- 7. Tenaga Kesehatan (Fungsional) --
                        WHEN LOWER(jabatan_nama) REGEXP 'dokter|perawat|bidan|apoteker|nutrisionis|sanitarian|asisten apoteker' THEN 'Fungsional Kesehatan'

                        -- 8. Fungsional Lainnya (Fungsional diluar Guru & Nakes) --
                        -- Mengasumsikan ada kolom jenis_jabatan_nama untuk cek tipe 'Fungsional' --
                        WHEN LOWER(jenis_jabatan_nama) LIKE '%fungsional%' THEN 'Fungsional Lainnya'

                        -- 9. Pelaksana (Bukan Fungsional) --
                        ELSE 'Pelaksana'
                    END as kelompok_jabatan,
                    SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pns_l,
                    SUM(CASE WHEN kedudukan_hukum_id IN ('01', '02', '03', '13', '15', '04') AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pns_p,
                    SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_l,
                    SUM(CASE WHEN kedudukan_hukum_id = '71' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_p,
                    SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%m%' THEN 1 ELSE 0 END) as pppk_pw_l,
                    SUM(CASE WHEN kedudukan_hukum_id = '101' AND LOWER(jenis_kelamin) LIKE '%f%' THEN 1 ELSE 0 END) as pppk_pw_p
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
                        WHEN kelompok_jabatan = 'Tenaga Guru' THEN 7
                        WHEN kelompok_jabatan = 'Tenaga Kesehatan' THEN 8
                        WHEN kelompok_jabatan = 'Fungsional Lainnya' THEN 9
                        ELSE 10
                    END ASC
                ")
                ->get()
                ->toArray();
        }
}
