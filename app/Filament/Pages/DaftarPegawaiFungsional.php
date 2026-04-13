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
    protected static ?string $navigationLabel = 'Daftar Fungsional';
    protected static ?string $title = 'Daftar Pejabat Fungsional & Pelaksana';
    protected static string|UnitEnum|null $navigationGroup = 'Daftar Pegawai';

    protected string $view = 'filament.pages.daftar-pegawai-fungsional';

    public $filterKategori = 'guru'; // Default awal

        public function getPegawaiProperty()
        {
            return DB::table('pegawais as p')

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
                ->leftJoin('unors as u', 'j.unor_induk_id', '=', 'u.id')

                ->select([
                    'p.nama',
                    'p.nip_baru',
                    'j.jabatan_nama',
                    'u.nama as unor_nama',

                    DB::raw("
                        CASE
                            WHEN j.kel_jab = 'jf guru' THEN 'Fungsional Guru'
                            WHEN j.kel_jab = 'jf kesehatan' THEN 'Fungsional Kesehatan'
                            WHEN j.kel_jab = 'jf lainnya' THEN 'Fungsional Lainnya'
                            WHEN jj.nama = 'pelaksana' THEN 'Pelaksana'
                            ELSE 'Lainnya'
                        END as kelompok_jabatan
                    ")
                ])

                ->where(function ($q) {
                    $q->where('jj.nama', '!=', 'struktural')
                    ->orWhereNull('jj.nama');
                })

                ->having('kelompok_jabatan', $this->filterKategori)

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
