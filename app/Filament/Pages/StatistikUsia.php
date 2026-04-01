<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class StatistikUsia extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected string $view = 'filament.pages.statistik-usia';
    protected static ?string $navigationLabel = 'Statistik Usia';
    protected static ?string $title = 'Statistik Pegawai Berdasarkan Usia';
    protected static ?int $navigationSort = 3;


    public $data = [];
    public $totalPegawai = 0;
    public $pegawaiUsiaDiatas60 = [];
    public $totalUsiaDiatas60 = 0;

    // Filter untuk tabel pegawai
    public $search = '';
    public $statusFilter = '';

    public function mount()
    {
        $this->loadData();
        $this->totalPegawai = DB::table('staging_import')->count();
        $this->loadPegawaiUsiaDiatas60();
    }

    public function loadPegawaiUsiaDiatas60()
    {
        // Ambil semua data pegawai
        $pegawai = DB::table('staging_import')
            ->select('nama', 'nip_baru', 'nip_lama', 'tanggal_lahir', 'kedudukan_hukum_id', 'kedudukan_hukum_nama', 'jenis_kelamin', 'gol_akhir_nama', 'jabatan_nama', 'unor_nama')
            ->get();

        // Filter pegawai dengan usia > 60 tahun (sudah lewat ulang tahun)
        $filtered = $pegawai->filter(function($item) {
            $usia = $this->calculateAgeDetail($item->tanggal_lahir);
            return $usia['sudah_lewat_60'] === true;
        })->values();

        $this->totalUsiaDiatas60 = $filtered->count();

        // Mapping data
        $this->pegawaiUsiaDiatas60 = $filtered->map(function($item) {
            $usia = $this->calculateAgeDetail($item->tanggal_lahir);
            return (object)[
                'nama' => $item->nama,
                'nip_baru' => $item->nip_baru,
                'nip_lama' => $item->nip_lama,
                'tanggal_lahir' => $item->tanggal_lahir,
                'tanggal_lahir_formatted' => $usia['tanggal_formatted'],
                'usia_tahun' => $usia['tahun'],
                'usia_bulan' => $usia['bulan'],
                'usia_hari' => $usia['hari'],
                'tgl_genap_60' => $usia['tgl_genap_60'],
                'hari_setelah_60' => $usia['hari_setelah_60'],
                'jenis_kelamin' => $this->formatGender($item->jenis_kelamin),
                'status_kepegawaian' => $item->kedudukan_hukum_nama,
                'status_id' => $item->kedudukan_hukum_id,
                'golongan' => $item->gol_akhir_nama,
                'jabatan' => $item->jabatan_nama,
                'unit_kerja' => $item->unor_nama,
            ];
        })->toArray();
    }

    private function calculateAgeDetail($tanggalLahir)
    {
        if (empty($tanggalLahir)) {
            return [
                'tahun' => 0,
                'bulan' => 0,
                'hari' => 0,
                'sudah_lewat_60' => false,
                'tanggal_formatted' => '-',
                'tgl_genap_60' => '-',
                'hari_setelah_60' => 0
            ];
        }

        try {
            // Coba parse format DD-MM-YYYY
            $date = null;
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggalLahir)) {
                $date = Carbon::createFromFormat('d-m-Y', $tanggalLahir);
            }
            // Format DD/MM/YYYY
            elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalLahir)) {
                $date = Carbon::createFromFormat('d/m/Y', $tanggalLahir);
            }
            // Format YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
                $date = Carbon::parse($tanggalLahir);
            }
            else {
                return [
                    'tahun' => 0,
                    'bulan' => 0,
                    'hari' => 0,
                    'sudah_lewat_60' => false,
                    'tanggal_formatted' => $tanggalLahir,
                    'tgl_genap_60' => '-',
                    'hari_setelah_60' => 0
                ];
            }

            $today = Carbon::now();
            $usia = $date->age;
            $tglGenap60 = $date->copy()->addYears(60);
            $hariSetelah60 = $today->diffInDays($tglGenap60, false);

            // Sudah lewat 60 tahun jika hariSetelah60 > 0 (positif)
            $sudahLewat60 = $hariSetelah60 > 0;

            return [
                'tahun' => $usia,
                'bulan' => $date->diffInMonths($today),
                'hari' => $date->diffInDays($today),
                'sudah_lewat_60' => $sudahLewat60,
                'tanggal_formatted' => $date->format('d/m/Y'),
                'tgl_genap_60' => $tglGenap60->format('d/m/Y'),
                'hari_setelah_60' => $hariSetelah60 > 0 ? $hariSetelah60 : 0
            ];

        } catch (\Exception $e) {
            return [
                'tahun' => 0,
                'bulan' => 0,
                'hari' => 0,
                'sudah_lewat_60' => false,
                'tanggal_formatted' => '-',
                'tgl_genap_60' => '-',
                'hari_setelah_60' => 0
            ];
        }
    }

    private function formatGender($jenisKelamin)
    {
        if (preg_match('/[mMlL]/', $jenisKelamin)) {
            return 'Laki-laki';
        } elseif (preg_match('/[fFpP]/', $jenisKelamin)) {
            return 'Perempuan';
        }
        return 'Tidak Diketahui';
    }

    public function loadData()
    {
        // Ambil semua data pegawai dengan tanggal lahir
        $pegawai = DB::table('staging_import')
            ->select('tanggal_lahir', 'kedudukan_hukum_id', 'jenis_kelamin')
            ->get();

        // Hitung usia untuk setiap pegawai
        $pegawaiWithAge = $pegawai->map(function($item) {
            $usia = $this->calculateAge($item->tanggal_lahir);
            return (object)[
                'usia' => $usia,
                'kedudukan_hukum_id' => $item->kedudukan_hukum_id,
                'jenis_kelamin' => $item->jenis_kelamin
            ];
        });

        // Definisikan range usia
        $ageRanges = [
            '< 25' => ['min' => 0, 'max' => 24, 'label' => '< 25 tahun'],
            '25 - 29' => ['min' => 25, 'max' => 29, 'label' => '25 - 29 tahun'],
            '30 - 34' => ['min' => 30, 'max' => 34, 'label' => '30 - 34 tahun'],
            '35 - 39' => ['min' => 35, 'max' => 39, 'label' => '35 - 39 tahun'],
            '40 - 44' => ['min' => 40, 'max' => 44, 'label' => '40 - 44 tahun'],
            '45 - 49' => ['min' => 45, 'max' => 49, 'label' => '45 - 49 tahun'],
            '50 - 54' => ['min' => 50, 'max' => 54, 'label' => '50 - 54 tahun'],
            '55 - 59' => ['min' => 55, 'max' => 59, 'label' => '55 - 59 tahun'],
            '=>60' => ['min' => 60, 'max' => 999, 'label' => '> 60 tahun']
        ];

        $result = [];
        foreach ($ageRanges as $key => $range) {
            // Filter berdasarkan range usia
            $filtered = $pegawaiWithAge->filter(function($item) use ($range) {
                return $item->usia >= $range['min'] && $item->usia <= $range['max'];
            });

            $pns_l = $filtered->where('kedudukan_hukum_id', '01')
                              ->filter(function($item) {
                                  return preg_match('/[mMlL]/', $item->jenis_kelamin);
                              })->count();

            $pns_p = $filtered->where('kedudukan_hukum_id', '01')
                              ->filter(function($item) {
                                  return preg_match('/[fFpP]/', $item->jenis_kelamin);
                              })->count();

            $pppk_l = $filtered->where('kedudukan_hukum_id', '71')
                               ->filter(function($item) {
                                   return preg_match('/[mMlL]/', $item->jenis_kelamin);
                               })->count();

            $pppk_p = $filtered->where('kedudukan_hukum_id', '71')
                               ->filter(function($item) {
                                   return preg_match('/[fFpP]/', $item->jenis_kelamin);
                               })->count();

            $pppk_pw_l = $filtered->where('kedudukan_hukum_id', '101')
                                  ->filter(function($item) {
                                      return preg_match('/[mMlL]/', $item->jenis_kelamin);
                                  })->count();

            $pppk_pw_p = $filtered->where('kedudukan_hukum_id', '101')
                                  ->filter(function($item) {
                                      return preg_match('/[fFpP]/', $item->jenis_kelamin);
                                  })->count();

            $result[] = (object)[
                'range' => $range['label'],
                'pns_l' => $pns_l,
                'pns_p' => $pns_p,
                'pns_total' => $pns_l + $pns_p,
                'pppk_l' => $pppk_l,
                'pppk_p' => $pppk_p,
                'pppk_total' => $pppk_l + $pppk_p,
                'pppk_pw_l' => $pppk_pw_l,
                'pppk_pw_p' => $pppk_pw_p,
                'pppk_pw_total' => $pppk_pw_l + $pppk_pw_p,
                'total' => $filtered->count()
            ];
        }

        $this->data = $result;
    }

    private function calculateAge($tanggalLahir)
    {
        if (empty($tanggalLahir)) {
            return 0;
        }

        try {
            $date = null;

            // Format DD-MM-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggalLahir)) {
                $date = Carbon::createFromFormat('d-m-Y', $tanggalLahir);
            }
            // Format DD/MM/YYYY
            elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalLahir)) {
                $date = Carbon::createFromFormat('d/m/Y', $tanggalLahir);
            }
            // Format YYYY-MM-DD
            elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
                $date = Carbon::parse($tanggalLahir);
            }
            else {
                return 0;
            }

            return $date->age;

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function exportPdf()
    {
        $totals = [
            'pns_l' => collect($this->data)->sum('pns_l'),
            'pns_p' => collect($this->data)->sum('pns_p'),
            'pns_total' => collect($this->data)->sum('pns_total'),
            'pppk_l' => collect($this->data)->sum('pppk_l'),
            'pppk_p' => collect($this->data)->sum('pppk_p'),
            'pppk_total' => collect($this->data)->sum('pppk_total'),
            'pppk_pw_l' => collect($this->data)->sum('pppk_pw_l'),
            'pppk_pw_p' => collect($this->data)->sum('pppk_pw_p'),
            'pppk_pw_total' => collect($this->data)->sum('pppk_pw_total'),
            'total' => collect($this->data)->sum('total')
        ];

        $pdf = Pdf::loadView('filament.pages.exports.statistik-usia-pdf', [
            'data' => $this->data,
            'totals' => $totals,
            'totalPegawai' => $this->totalPegawai,
            'pegawaiUsiaDiatas60' => $this->pegawaiUsiaDiatas60,
            'totalUsiaDiatas60' => $this->totalUsiaDiatas60,
            'date' => now()->format('d F Y H:i:s')
        ]);

        $pdf->setPaper('folio', 'portrait');

        return Response::streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'statistik-usia-' . now()->format('Y-m-d') . '.pdf');
    }

    // Reset pagination saat search/filter berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // Getter untuk data pegawai yang sudah difilter
    public function getFilteredPegawaiProperty()
    {
        $collection = collect($this->pegawaiUsiaDiatas60);

        // Filter berdasarkan search
        if (!empty($this->search)) {
            $collection = $collection->filter(function($item) {
                return stripos($item->nama, $this->search) !== false ||
                       stripos($item->nip_baru, $this->search) !== false;
            });
        }

        // Filter berdasarkan status
        if (!empty($this->statusFilter)) {
            $collection = $collection->filter(function($item) use ($collection) {
                return $item->status_id == $this->statusFilter;
            });
        }

        return $collection->values();
    }
}
