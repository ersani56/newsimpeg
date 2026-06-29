<?php

namespace App\Filament\Resources\Pegawais;

use App\Filament\Resources\PegawaiResource\Pages\ViewPegawai;
use App\Filament\Resources\Pegawais\Pages\ListPegawais;
use App\Filament\Resources\Pegawais\Pages\EditPegawai;
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

                        Tab::make('Pangkat')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('golongan_nama')
                                            ->label('Golongan/Ruang atau Jenjang PPPK')
                                            ->options(fn (): array => DB::table('staging_import')
                                                ->whereNotNull('gol_akhir_nama')
                                                ->where('gol_akhir_nama', '<>', '')
                                                ->distinct()
                                                ->orderBy('gol_akhir_nama')
                                                ->pluck('gol_akhir_nama', 'gol_akhir_nama')
                                                ->all())
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('pangkat_nama')
                                            ->label('Pangkat')
                                            ->disabled()
                                            ->dehydrated(false),
                                        DatePicker::make('tmt_golongan')
                                            ->label('TMT Golongan'),
                                        TextInput::make('mk_tahun')
                                            ->label('Masa Kerja Tahun')
                                            ->numeric()
                                            ->minValue(0),
                                        TextInput::make('mk_bulan')
                                            ->label('Masa Kerja Bulan')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(11),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Jabatan')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('jabatan_id')
                                            ->label('Nama Jabatan')
                                            ->relationship('jabatan', 'jabatan_nama')
                                            ->searchable()
                                            ->preload(false),
                                        Select::make('jenis_jabatan_id')
                                            ->label('Jenis Jabatan')
                                            ->options([
                                                '1' => 'Jabatan Struktural',
                                                '2' => 'Jabatan Fungsional',
                                                '4' => 'Jabatan Pelaksana',
                                            ]),
                                        Select::make('jabatan_eselon')
                                            ->label('Eselon')
                                            ->options([
                                                'I/a' => 'I/a', 'I/b' => 'I/b',
                                                'II/a' => 'II/a', 'II/b' => 'II/b',
                                                'III/a' => 'III/a', 'III/b' => 'III/b',
                                                'IV/a' => 'IV/a', 'IV/b' => 'IV/b',
                                            ])
                                            ->placeholder('Non Eselon'),
                                        TextInput::make('jabatan_jenjang')
                                            ->label('Jenjang Jabatan')
                                            ->placeholder('Utama, Madya, Muda, Pertama, dan seterusnya'),
                                        DatePicker::make('tmt_jabatan')
                                            ->label('TMT Jabatan'),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Pendidikan')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('pendidikan_id')
                                            ->label('Program/Jurusan Pendidikan')
                                            ->relationship('pendidikan', 'nama')
                                            ->searchable()
                                            ->preload(false),
                                        TextInput::make('tingkat_pendidikan_nama')
                                            ->label('Tingkat Pendidikan')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('nama_sekolah')
                                            ->label('Nama Sekolah/Perguruan Tinggi'),
                                        TextInput::make('tahun_lulus')
                                            ->label('Tahun Lulus')
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue((int) date('Y')),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Kontak')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('email')
                                            ->email(),

                                        TextInput::make('nomor_hp')
                                            ->label('Nomor HP'),

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
                        'lokasi_kerja_id', 'lokasi_kerja_nama', 'unor_id','lokasi_kerja_id',
                        'lokasi_kerja_nama', 'unor_id', 'unor_nama', 'instansi_induk_id', 'instansi_induk_nama',
                        'instansi_kerja_id', 'instansi_kerja_nama','instansi_kerja_id','instansi_kerja_nama',
                        'satuan_kerja_induk_id', 'satuan_kerja_induk_nama', 'satuan_kerja_kerja_id', 'satuan_kerja_kerja_nama',
                        'is_valid_nik','nama_sekolah', 'flag_ikd',
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
                $referensiJabatan = DB::table('jabatans')
                    ->select('jabatan_id', 'eselon', 'jenjang')
                    ->get()
                    ->keyBy('jabatan_id');

                $data = DB::table('staging_import')
                ->whereNotNull('pns_id')
                ->get()
                ->map(function ($row) use ($referensiJabatan) {
                    $jabatan = $referensiJabatan->get($row->jabatan_id);

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
                        'golongan_nama' => $row->gol_akhir_nama,
                        'pangkat_nama' => self::namaPangkat($row->gol_akhir_nama),
                        'tmt_golongan' => self::parseTanggal($row->tmt_golongan),
                        'mk_tahun' => $row->mk_tahun !== null && $row->mk_tahun !== '' ? (int) $row->mk_tahun : null,
                        'mk_bulan' => $row->mk_bulan !== null && $row->mk_bulan !== '' ? (int) $row->mk_bulan : null,
                        'jabatan_id' => $row->jabatan_id,
                        'jenis_jabatan_id' => $row->jenis_jabatan_id,
                        'jenis_jabatan_nama' => $row->jenis_jabatan_nama,
                        'jabatan_nama' => $row->jabatan_nama,
                        'jabatan_eselon' => $jabatan?->eselon,
                        'jabatan_jenjang' => $jabatan?->jenjang,
                        'tmt_jabatan' => self::parseTanggal($row->tmt_jabatan),
                        'pendidikan_id' => $row->pendidikan_id,
                        'pendidikan_nama' => $row->pendidikan_nama,
                        'tingkat_pendidikan_id' => $row->tingkat_pendidikan_id,
                        'tingkat_pendidikan_nama' => $row->tingkat_pendidikan_nama,
                        'nama_sekolah' => $row->nama_sekolah,
                        'tahun_lulus' => $row->tahun_lulus !== null && $row->tahun_lulus !== '' ? (int) $row->tahun_lulus : null,
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
                            'jenis_jabatan_id',
                            'jenis_jabatan_nama',
                            'jabatan_nama',
                            'jabatan_eselon',
                            'jabatan_jenjang',
                            'tmt_jabatan',
                            'golongan_id',
                            'golongan_nama',
                            'pangkat_nama',
                            'tmt_golongan',
                            'mk_tahun',
                            'mk_bulan',
                            'pendidikan_id',
                            'pendidikan_nama',
                            'tingkat_pendidikan_id',
                            'tingkat_pendidikan_nama',
                            'nama_sekolah',
                            'tahun_lulus',
                            'unor_id',
                            'kedudukan_hukum_id',
                            'updated_at'
                        ]
                    );
                }
                DB::table('pegawais')
                    ->whereNotNull('pns_id')
                    ->whereNotIn('pns_id', function ($query) {
                        $query->select('pns_id')
                            ->from('staging_import')
                            ->whereNotNull('pns_id');
                    })
                    ->delete();

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
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function namaPangkat(?string $golongan): ?string
    {
        return [
            'I/a' => 'Juru Muda',
            'I/b' => 'Juru Muda Tingkat I',
            'I/c' => 'Juru',
            'I/d' => 'Juru Tingkat I',
            'II/a' => 'Pengatur Muda',
            'II/b' => 'Pengatur Muda Tingkat I',
            'II/c' => 'Pengatur',
            'II/d' => 'Pengatur Tingkat I',
            'III/a' => 'Penata Muda',
            'III/b' => 'Penata Muda Tingkat I',
            'III/c' => 'Penata',
            'III/d' => 'Penata Tingkat I',
            'IV/a' => 'Pembina',
            'IV/b' => 'Pembina Tingkat I',
            'IV/c' => 'Pembina Utama Muda',
            'IV/d' => 'Pembina Utama Madya',
            'IV/e' => 'Pembina Utama',
        ][$golongan] ?? null;
    }

    public static function parseTanggal(?string $tanggal): ?string
    {
        if (! $tanggal) {
            return null;
        }

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $tanggal);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                // Coba format berikutnya.
            }
        }

        return null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPegawais::route('/'),
            'view' => ViewPegawai::route('/{record}'),
            //'create' => CreatePegawai::route('/create'),
            'edit' => EditPegawai::route('/{record}/edit'),
        ];
    }
}
