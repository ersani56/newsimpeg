<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use BackedEnum;
use UnitEnum;

class DaftarPegawaiFungsional extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Fungsional & Pelaksana';
    protected static ?string $title = 'Daftar Pegawai Fungsional & Pelaksana';
    protected static string|UnitEnum|null $navigationGroup = 'Daftar Pegawai';

    protected string $view = 'filament.pages.daftar-pegawai-fungsional';

    public $filterKategori = 'guru'; // Default awal

        public function getPegawaiProperty()
        {
            return DB::table('pegawais as p')

                ->leftJoin('jabatans as j', 'p.jabatan_id', '=', 'j.jabatan_id')
                ->leftJoin('golongans as g', 'p.golongan_id', '=', 'g.golongan_id')
                ->leftJoin('kedudukan_hukums as k', 'p.kedudukan_hukum_id', '=', 'k.kedudukan_hukum_id')
                ->leftJoin('unors as u', 'p.unor_id', '=', 'u.unor_id')
                ->select([
                    'p.nama',
                    'p.nip_baru',
                    'k.nama as kh_nama',
                    'g.golru as golru_display',
                    'j.jabatan_nama',
                    'u.nama as unor_nama',

                    DB::raw("
                    CASE
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf guru' THEN 'guru'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf kesehatan' THEN 'kesehatan'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf lainnya' THEN 'lainnya'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'pelaksana' THEN 'pelaksana'
                        ELSE 'lainnya'
                    END as kelompok_jabatan
                    ")
                ])

                // hilangkan struktural
                ->where(function ($q) {
                    $q->where('j.kel_jab', '!=', 'struktural')
                    ->orWhereNull('j.kel_jab');
                })

                ->whereRaw("
                    CASE
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf guru' THEN 'Fungsional Guru'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf kesehatan' THEN 'Fungsional Kesehatan'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'jf lainnya' THEN 'Fungsional Lainnya'
                        WHEN LOWER(TRIM(j.kel_jab)) = 'pelaksana' THEN 'Pelaksana'
                        ELSE 'Fungsional Lainnya'
                    END = ?
                ", [$this->filterKategori])

                ->orderBy('p.nama', 'asc')
                ->get();
        }
        public function exportPdf()
        {
            // Tambahkan ini untuk mencegah timeout dan memori penuh
            ini_set('memory_limit', '1024M');
            set_time_limit(300);

            $data = [
                'pegawai' => $this->pegawai,
                'filter' => $this->filterKategori,
                'date' => now()->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i')
            ];

            $pdf = Pdf::loadView(
                'filament.pages.exports.daftar-fungsional-pdf',
                $data
            );

            // Kertas F4 (8.5 x 13 inch) sudah benar
            $pdf->setPaper([0, 0, 612, 936], 'portrait');

            // Tambahkan opsi untuk DomPDF agar lebih ringan
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Jika ada logo lewat URL
                'logOutputFile' => storage_path('logs/dompdf.html.log'),
                'tempDir' => storage_path('temp/'),
            ]);

            return response()->streamDownload(function () use ($pdf) {
                // Gunakan stream() atau langsung output
                echo $pdf->stream()->getContent();
            }, 'Daftar_Fungsional_' . ($this->filterKategori ?? 'Semua') . '_' . date('YmdHis') . '.pdf');
        }

/*         public function exportExcel()
        {
            $fileName = 'Daftar_Fungsional_' . ($this->filterKategori ?? 'Semua') . '_' . date('YmdHis') . '.xlsx';

            return Excel::download(
                new PegawaiFungsionalExport($this->filterKategori),
                $fileName
            );
        } */
}
