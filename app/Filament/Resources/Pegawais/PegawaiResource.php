<?php

namespace App\Filament\Resources\Pegawais;

use App\Filament\Resources\PegawaiResource\Pages\ViewPegawai;
use App\Filament\Resources\Pegawais\Pages\ListPegawais;
use App\Models\Pegawai;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use UnitEnum;


class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pegawai';
    protected static ?string $modelLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Pegawai';

    protected static string|UnitEnum|null $navigationGroup = 'Data Pegawai';
    protected static ?int $navigationSort = 10;
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Data Pegawai')
                    ->tabs([

                        Tab::make('Data Pokok Pegawai')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nip_baru')
                                            ->label('NIP')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('nama')
                                            ->required(),
                                        TextInput::make('gelar_depan'),
                                        TextInput::make('gelar_belakang'),
                                        Select::make('kedudukan_hukum_id')
                                            ->relationship('kedudukanHukum','nama')
                                            ->label('Kedudukan Hukum'),

                                        Select::make('jenis_kelamin')
                                            ->options([
                                                'm' => 'Laki-laki',
                                                'f' => 'Perempuan',
                                            ]),
                                        TextInput::make('nik'),
                                        DatePicker::make('tanggal_lahir'),
                                        Select::make('agama_id')
                                            ->relationship('agama','nama'),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Pangakat')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('golongan_id')
                                            ->relationship('Golongan','golru'),

                                        Select::make('golongan_id')
                                            ->relationship('Golongan','pangkat'),

                                        DatePicker::make('Golongan.tmt_golongan')
                                            ->label('TMT Golongan'),

                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Jabatan')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('pendidikan_id')
                                            ->relationship('pendidikan','nama')
                                            ->searchable(),

                                        TextInput::make('nama_sekolah'),

                                        TextInput::make('jurusan'),

                                        TextInput::make('tahun_lulus'),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Pendidikan')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('email')
                                            ->email(),

                                        TextInput::make('no_hp'),

                                        Textarea::make('alamat')
                                            ->columnSpanFull(),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Data Akun')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user','name')
                                            ->searchable()
                                            ->label('User Login'),
                                    ])
                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->searchable()
                ->sortable(),
            TextColumn::make('nip_baru')
                ->label('NIP')
                ->searchable()
                ->sortable(),
            TextColumn::make('nama')
                ->label('NAMA')
                ->searchable()
                ->sortable()
                ->formatStateUsing(function ($state, $record) {
                    return trim(
                        ($record->gelar_depan ? $record->gelar_depan . ' ' : '') .
                        $record->nama .
                        ($record->gelar_belakang ? ', ' . $record->gelar_belakang : '')
                    );
                }),
            TextColumn::make('kedudukanHukum.nama')
                ->label('KEDUDUKAN HUKUM')
                ->sortable()
                ->searchable(),
            TextColumn::make('nik')
                ->label('NIK')
                ->searchable()
                ->sortable(),
            TextColumn::make('agama.nama')
                ->label('AGAMA')
                ->searchable()
                ->sortable(),
            TextColumn::make('pendidikan.nama')
                ->label('PENDIDIKAN')
                ->searchable(),
            TextColumn::make('jabatan.jabatan_nama')
                ->label('JABATAN')
                ->searchable()
                ->sortable(),
        ])

        ->actions([
            EditAction::make(),
        ])

        ->headerActions([
            Action::make('importCsvStaging')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('file')
                        ->label('File CSV')
                        ->disk('local')
                        ->directory('imports/staging')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $columns = [
                        'pns_id', 'nip_baru', 'nip_lama', 'nama', 'gelar_depan', 'gelar_belakang',
                        'tempat_lahir_id', 'tempat_lahir_nama', 'tanggal_lahir', 'jenis_kelamin',
                        'agama_id', 'agama_nama', 'jenis_kawin_id', 'jenis_kawin_nama', 'nik',
                        'nomor_hp', 'email', 'email_gov', 'alamat', 'npwp_nomor', 'bpjs',
                        'jenis_pegawai_id', 'jenis_pegawai_nama', 'kedudukan_hukum_id',
                        'kedudukan_hukum_nama', 'status_cpns_pns', 'kartu_asn_virtual',
                        'nomor_sk_cpns', 'tanggal_sk_cpns', 'tmt_cpns', 'nomor_sk_pns',
                        'tanggal_sk_pns', 'tmt_pns', 'gol_awal_id', 'gol_awal_nama',
                        'gol_akhir_id', 'gol_akhir_nama', 'tmt_golongan', 'mk_tahun', 'mk_bulan',
                        'jenis_jabatan_id', 'jenis_jabatan_nama', 'jabatan_id', 'jabatan_nama',
                        'tmt_jabatan', 'tingkat_pendidikan_id', 'tingkat_pendidikan_nama',
                        'pendidikan_id', 'pendidikan_nama', 'tahun_lulus', 'kpkn_id', 'kpkn_nama',
                        'lokasi_kerja_id', 'lokasi_kerja_nama', 'unor_id',
                    ];

                    $path = Storage::disk('local')->path($data['file']);
                    $handle = fopen($path, 'r');

                    $firstLine = fgets($handle);
                    $delimiter = '|';

                    $headers = array_map(
                        fn ($header) => Str::of($header)
                            ->replace("\xEF\xBB\xBF", '')
                            ->trim()
                            ->lower()
                            ->replaceMatches('/[^a-z0-9]+/', '_')
                            ->trim('_')
                            ->toString(),
                        str_getcsv($firstLine, $delimiter)
                    );


                    $missingColumns = array_diff($columns, $headers);

                    if (! empty($missingColumns)) {
                        fclose($handle);

                        Notification::make()
                            ->title('Import gagal')
                            ->body('Kolom CSV tidak lengkap: ' . implode(', ', $missingColumns))
                            ->danger()
                            ->send();

                        return;
                    }

                    $indexes = array_flip($headers);
                    $rows = [];
                    $imported = 0;

                    while (($csvRow = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $row = [];

                        foreach ($columns as $column) {
                            $value = $csvRow[$indexes[$column]] ?? null;
                            $value = is_string($value) ? trim($value) : $value;
                            $value = is_string($value) ? ltrim($value, "'") : $value;

                            $row[$column] = $value === '' ? null : $value;
                        }

                        $rows[] = $row;

                        if (count($rows) >= 500) {
                            DB::table('staging_import')->insert($rows);
                            $imported += count($rows);
                            $rows = [];
                        }
                    }

                    if (! empty($rows)) {
                        DB::table('staging_import')->insert($rows);
                        $imported += count($rows);
                    }

                    fclose($handle);

                    Notification::make()
                        ->title('CSV berhasil diimport')
                        ->body($imported . ' baris masuk ke staging_import.')
                        ->success()
                        ->send();
                }),


           // ========================
            // 1. CLEAR STAGING
            // ========================
            Action::make('clearStaging')
                ->label('Kosongkan Staging')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn() => DB::table('staging_import')->truncate()),

            // ========================
            // 2. SINKRON PEGAWAI (AMAN)
            // ========================
            Action::make('sinkronPegawai')
            ->label('Sinkron Pegawai')
            ->color('success')
            ->action(function () {
                $data = DB::table('staging_import')
                ->whereNotNull('pns_id')
                ->get()
                ->map(function ($row) {
                    return [
                        'pns_id' => $row->pns_id,
                        'nip_baru' => trim($row->nip_baru),
                        'nip_lama' => trim($row->nip_lama),
                        'nik' => trim($row->nik),

                        'nama' => $row->nama,
                        'gelar_depan' => $row->gelar_depan,
                        'gelar_belakang' => $row->gelar_belakang,

                        'tempat_lahir_id' => $row->tempat_lahir_id,
                        'tanggal_lahir' => $row->tanggal_lahir
                            ? \Carbon\Carbon::createFromFormat('d-m-Y', $row->tanggal_lahir)
                            : null,

                        'tmt_cpns' => $row->tmt_cpns
                            ? \Carbon\Carbon::createFromFormat('d-m-Y', $row->tmt_cpns)
                            : null,

                        'jenis_kelamin' => strtolower($row->jenis_kelamin),

                        'agama_id' => $row->agama_id ? (int) $row->agama_id : null,
                        'golongan_id' => trim($row->gol_akhir_id),
                        'jabatan_id' => $row->jabatan_id,
                        'pendidikan_id' => $row->pendidikan_id,
                        'unor_id' => $row->unor_id,
                        'jenis_kawin_id' => $row->jenis_kawin_id ? (int) $row->jenis_kawin_id : null,

                        'nomor_hp' => $row->nomor_hp,
                        'email' => $row->email,
                        'email_gov' => $row->email_gov,
                        'alamat' => $row->alamat,

                        'npwp_nomor' => $row->npwp_nomor,
                        'bpjs' => $row->bpjs,

                        'kedudukan_hukum_id' => $row->kedudukan_hukum_id
                            ? (int) $row->kedudukan_hukum_id
                            : null,

                        'kartu_asn_virtual' => $row->kartu_asn_virtual,
                        'status_cpns_pns' => $row->status_cpns_pns,

                        'nomor_sk_cpns' => $row->nomor_sk_cpns,
                        'tanggal_sk_cpns' => $row->tanggal_sk_cpns
                            ? \Carbon\Carbon::createFromFormat('d-m-Y', $row->tanggal_sk_cpns)
                            : null,

                        'nomor_sk_pns' => $row->nomor_sk_pns,
                        'tanggal_sk_pns' => $row->tanggal_sk_pns
                            ? \Carbon\Carbon::createFromFormat('d-m-Y', $row->tanggal_sk_pns)
                            : null,

                        'is_valid_nik' => 1,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                // Chunk data and upsert in batches
                foreach (array_chunk($data, 500) as $chunk) {
                    DB::table('pegawais')->upsert(
                        $chunk,
                        ['pns_id'],
                        [
                            'nip_baru',
                            'nip_lama',
                            'nama',
                            'jabatan_id',
                            'golongan_id',
                            'pendidikan_id',
                            'unor_id',
                            'kedudukan_hukum_id',
                            'updated_at'
                        ]
                    );
                }

                Notification::make()
                    ->title('Pegawai berhasil disinkron')
                    ->success()
                    ->send();
            }),
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function (ListPegawais $livewire) {
                    $data = $livewire->getTableQueryForExport()
                        ->with(['kedudukanHukum', 'agama', 'pendidikan', 'jabatan'])
                        ->get()
                        ->map(function (Pegawai $pegawai) {
                            return (object) [
                                'nip_baru' => $pegawai->nip_baru ?: '-',
                                'nama_lengkap' => trim(
                                    ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') .
                                    ($pegawai->nama ?: '-') .
                                    ($pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '')
                                ),
                                'kedudukan_hukum' => $pegawai->kedudukanHukum?->nama ?: '-',
                                'nik' => $pegawai->nik ?: '-',
                                'agama' => $pegawai->agama?->nama ?: '-',
                                'pendidikan' => $pegawai->pendidikan?->nama ?: '-',
                                'jabatan' => $pegawai->jabatan?->jabatan_nama ?: '-',
                            ];
                        });

                    $pdf = Pdf::loadView('filament.pages.exports.pegawai-pdf', [
                        'data' => $data,
                        'date' => now()->translatedFormat('d F Y H:i'),
                        'search' => $livewire->getTableSearch() ?: '-',
                        'total' => $data->count(),
                    ]);

                    $pdf->setPaper('folio', 'landscape');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'daftar-pegawai-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                }),
            // ========================
            // 3. SINKRON JABATAN (WAJIB)
            // ========================
            /* Action::make('sinkronRJabatan')
            ->label('Sinkron RJabatan')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {

                DB::table('r_jabatans')->truncate();

                DB::statement("
                    INSERT INTO r_jabatans (
                        pegawai_id,
                        jabatan_id,
                        tmt_jabatan,
                        created_at,
                        updated_at
                    )
                    SELECT
                        p.id,
                        j.id,
                        STR_TO_DATE(NULLIF(s.tmt_jabatan, ''), '%d-%m-%Y'),
                        NOW(),
                        NOW()
                    FROM staging_import s
                    JOIN pegawais p ON s.pns_id = p.pns_id
                    LEFT JOIN jabatans j ON s.jabatan_id = j.id
                    WHERE s.jabatan_id IS NOT NULL
                ");
                Notification::make()
                    ->title('Jabatan berhasil disinkron')
                    ->success()
                    ->send();
            }),
 */

            // ========================
            // 4. SINKRON PENDIDIKAN
            // ========================
/*          Action::make('sinkronRPendidikan')
            ->label('Sinkron RPendidikan')
            ->color('info')
            ->requiresConfirmation()
            ->action(function () {

                DB::table('r_pends')->truncate();

                DB::statement("
                    INSERT INTO r_pends (
                        pegawai_id,
                        pendidikan_id,
                        tingkat_pendidikan_id,
                        nama_sekolah,
                        tahun_lulus,
                        created_at,
                        updated_at
                    )
                    SELECT
                        p.id,
                        pend.id,
                        tp.id,
                        s.nama_sekolah,
                        s.tahun_lulus,
                        NOW(),
                        NOW()
                    FROM staging_import s
                    JOIN pegawais p ON s.pns_id = p.pns_id
                    LEFT JOIN pendidikans pend ON s.pendidikan_id = pend.id
                    LEFT JOIN tingkat_pendidikans tp ON s.tingkat_pendidikan_id = tp.id
                ");

                Notification::make()
                    ->title('Pendidikan berhasil disinkron')
                    ->success()
                    ->send();
            }), */

            /* Action::make('sinkronJabatan')
                ->label('Sinkron Jabatan')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {

                    $rows = DB::table('staging_import')
                        ->select('jabatan_id', 'unor_nama', 'jabatan_nama', 'unor_id')
                        ->whereNotNull('jabatan_id')
                        ->whereNotNull('jabatan_nama')
                        ->distinct()
                        ->get();

                    $now = now();

                    $data = $rows->map(function ($row) use ($now) {
                        $nama = strtolower(trim(preg_replace('/\s+/', ' ', $row->jabatan_nama)));
                        $unor_nama = strtolower(trim(preg_replace('/\s+/', ' ', $row->unor_nama ?? '')));

                        // Catatan: Pastikan logika deteksi jenis ini sudah benar sesuai data staging Anda
                        // Jika 'unor_id' adalah tipe jabatan, gunakan itu.
                        $jenis = (int) $row->unor_id;

                        // =========================
                        // KELOMPOK JABATAN (kel_jab)
                        // =========================
                        $kel_jab = 'jf lainnya'; // Default

                        if ($jenis === 1) {
                            $kel_jab = 'struktural';
                        } elseif ($jenis === 2) {
                            if (str_contains($nama, 'guru')) {
                                $kel_jab = 'jf guru';
                            } elseif (preg_match('/dokter|dokter gigi|perawat|bidan|apoteker|psikolog|terapis|anestesi|epidemiolog|fisioterapis|nutrisionis|sanitasi|radiografer|laboratorium|perekam medis|okupasi|ortotis|teknisi|wicara|administrator kesehatan|entomolog/i', $nama)) {
                                $kel_jab = 'jf kesehatan';
                            } else {
                                $kel_jab = 'jf lainnya';
                            }
                        } elseif ($jenis === 4) {
                            $kel_jab = 'pelaksana';
                        }

                        // =========================
                        // ESELON (Logika disingkat untuk efisiensi)
                        // =========================
                        $eselon = null;
                        if ($jenis === 1) {
                            if (str_contains($nama, 'sekretaris daerah')) $eselon = 'II/a';
                            elseif (preg_match('/^(kepala dinas|kepala badan|inspektur|sekretaris dprd|staf ahli|asisten b|asisten p)/', $nama)) $eselon = 'II/b';
                            elseif (preg_match('/camat/i', $nama)) $eselon = 'III/a';
                            elseif (preg_match('/(kabid|kepala bidang|direktur rumah sakit)/i', $nama)) $eselon = 'III/b';
                            elseif (preg_match('/(kasi|kepala seksi|kasubbid|lurah)/i', $nama)) $eselon = 'IV/a';
                            elseif (preg_match('/(kasubbag|kepala sub bagian)/i', $nama)) $eselon = 'IV/a';
                        }

                        return [
                            'jabatan_id'   => $row->jabatan_id,
                            'jabatan_nama' => $row->jabatan_nama,
                            'unor_nama'    => $row->unor_nama,
                            'kel_jab'      => $kel_jab,
                            'eselon'       => $eselon,
                            // Kolom tambahan sesuai struktur tabel Anda
                            'bup'          => null, // Tambahkan logika jika ada data BUP
                            'jenjang'      => null, // Tambahkan logika jika ada data Jenjang
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ];
                    });

                    // Upsert dengan kolom yang sesuai struktur
                    $data->chunk(500)->each(function ($chunk) {
                        DB::table('jabatans')->upsert(
                            $chunk->toArray(),
                            ['jabatan_id'], // Primary Key
                            ['jabatan_nama', 'unor_nama', 'kel_jab', 'eselon', 'bup', 'jenjang', 'updated_at']
                        );
                    });

                    Notification::make()
                        ->title('Sinkronisasi Berhasil')
                        ->success()
                        ->send();
                }), */
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPegawais::route('/'),
            'view' => ViewPegawai::route('/{record}'),
            //'create' => CreatePegawai::route('/create'),
            //'edit' => EditPegawai::route('/{record}/edit'),
        ];
    }
}
