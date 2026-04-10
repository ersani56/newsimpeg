<?php

namespace App\Filament\Resources\Pegawais;

use App\Filament\Resources\PegawaiResource\Pages\ViewPegawai;
use App\Filament\Resources\Pegawais\Pages\ListPegawais;
use App\Models\Pegawai;
use BackedEnum;
use DB;
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
use UnitEnum;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pegawai';
    protected static ?string $modelLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Data Pegawai';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Data Pegawai')
                    ->tabs([

                        Tab::make('Data Pribadi')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('nip_baru')
                                            ->label('NIP')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('nama')
                                            ->required(),
                                        TextInput::make('gelar_depan'),
                                        TextInput::make('gelar_belakang'),
                                        TextInput::make('kedudukan_hukum_nama')
                                            ->label('Jenis Pegawai')
                                            ->disabled(),
                                        Select::make('jenis_kelamin')
                                            ->options([
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                            ]),
                                        TextInput::make('nik'),

                                        TextInput::make('tempat_lahir'),

                                        DatePicker::make('tanggal_lahir'),

                                        Select::make('agama_id')
                                            ->relationship('agama','nama')
                                            ->searchable(),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Kepegawaian')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Select::make('jenis_pegawai_id')
                                            ->relationship('jenisPegawai','nama')
                                            ->searchable(),

                                        Select::make('kedudukan_hukum_id')
                                            ->relationship('kedudukanHukum','nama')
                                            ->searchable(),

                                        Select::make('golongan_id')
                                            ->relationship('golongan','nama')
                                            ->searchable(),

                                        Select::make('jabatan_id')
                                            ->relationship('jabatan','nama')
                                            ->searchable(),

                                        Select::make('unit_kerja_id')
                                            ->relationship('unitKerja','nama')
                                            ->searchable(),

                                        DatePicker::make('tmt_cpns')
                                            ->label('TMT CPNS'),

                                        DatePicker::make('tmt_pns')
                                            ->label('TMT PNS'),
                                    ])
                            ])
                            ->columnSpanFull(),

                        Tab::make('Pendidikan')
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

                        Tab::make('Kontak')
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
                ->sortable(),
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
            TextColumn::make('riwayatJabatan.jabatan.jabatan_nama')
                ->label('JABATAN')
                ->searchable()
                ->sortable(),
            TextColumn::make('unitKerja.nama')
                ->label('UNIT ORGANISASI')
                ->searchable()
                ->sortable(),
        ])
        ->actions([
            EditAction::make(),
        ])
        ->headerActions([
            Action::make('clearStaging')
                ->label('Kosongkan Staging')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn() => DB::table('staging_import')->truncate()),

            Action::make('importCsv')
                ->label('Import CSV SIASN')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    FileUpload::make('file')
                        ->label('Upload File CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->disk('public')
                        ->directory('imports')
                        ->preserveFilenames()
                        ->required(),
                ])
                ->action(function (array $data) {

                    $path = storage_path('app/public/' . $data['file']);

                    if (!file_exists($path)) {
                        Notification::make()
                            ->title('File tidak ditemukan')
                            ->danger()
                            ->send();
                        return;
                    }

                    $handle = fopen($path, 'r');

                    // Ambil header
                    $header = fgetcsv($handle, 0, '|');
                    $header = array_map(function ($h) {
                        $h = trim($h);
                        $h = strtolower($h);
                        $h = str_replace(' ', '_', $h); // 🔥 ini penting
                        return $h;
                    }, $header);

                    DB::beginTransaction();

                    try {

                        while (($row = fgetcsv($handle, 0, '|')) !== false) {

                        if (count($row) < count($header)) {
                            $row = array_pad($row, count($header), null);
                        }

                            $dataRow = array_combine($header, $row);

                            DB::table('staging_import')->insert([
                                'pns_id' => $dataRow['pns_id'] ?? null,
                                'nip_baru' => $dataRow['nip_baru'] ?? null,
                                'nip_lama' => $dataRow['nip_lama'] ?? null,
                                'nama' => $dataRow['nama'] ?? null,
                                'gelar_depan' => $dataRow['gelar_depan'] ?? null,
                                'gelar_belakang' => $dataRow['gelar_belakang'] ?? null,
                                'tempat_lahir_id' => $dataRow['tempat_lahir_id'] ?? null,
                                'tempat_lahir_nama' => $dataRow['tempat_lahir_nama'] ?? null,
                                'tanggal_lahir' => $dataRow['tanggal_lahir'] ?? null,
                                'jenis_kelamin' => $dataRow['jenis_kelamin'] ?? null,
                                'agama_id' => $dataRow['agama_id'] ?? null,
                                'agama_nama' => $dataRow['agama_nama'] ?? null,
                                'jenis_kawin_id' => $dataRow['jenis_kawin_id'] ?? null,
                                'jenis_kawin_nama' => $dataRow['jenis_kawin_nama'] ?? null,
                                'nik' => $dataRow['nik'] ?? null,
                                'nomor_hp' => $dataRow['nomor_hp'] ?? null,
                                'email' => $dataRow['email'] ?? null,
                                'email_gov' => $dataRow['email_gov'] ?? null,
                                'alamat' => $dataRow['alamat'] ?? null,
                                'npwp_nomor' => $dataRow['npwp_nomor'] ?? null,
                                'bpjs' => $dataRow['bpjs'] ?? null,
                                'jenis_pegawai_id' => $dataRow['jenis_pegawai_id'] ?? null,
                                'jenis_pegawai_nama' => $dataRow['jenis_pegawai_nama'] ?? null,
                                'kedudukan_hukum_id' => $dataRow['kedudukan_hukum_id'] ?? null,
                                'kedudukan_hukum_nama' => $dataRow['kedudukan_hukum_nama'] ?? null,
                                'status_cpns_pns' => $dataRow['status_cpns_pns'] ?? null,
                                'kartu_asn_virtual' => $dataRow['kartu_asn_virtual'] ?? null,
                                'nomor_sk_cpns' => $dataRow['nomor_sk_cpns'] ?? null,
                                'tanggal_sk_cpns' => $dataRow['tanggal_sk_cpns'] ?? null,
                                'tmt_cpns' => $dataRow['tmt_cpns'] ?? null,
                                'nomor_sk_pns' => $dataRow['nomor_sk_pns'] ?? null,
                                'tanggal_sk_pns' => $dataRow['tanggal_sk_pns'] ?? null,
                                'tmt_pns' => $dataRow['tmt_pns'] ?? null,
                                'gol_awal_id' => $dataRow['gol_awal_id'] ?? null,
                                'gol_awal_nama' => $dataRow['gol_awal_nama'] ?? null,
                                'gol_akhir_id' => $dataRow['gol_akhir_id'] ?? null,
                                'gol_akhir_nama' => $dataRow['gol_akhir_nama']  ?? null,
                                'tmt_golongan' => $dataRow['tmt_golongan'] ?? null,
                                'mk_tahun' => $dataRow['mk_tahun'] ?? null,
                                'mk_bulan' => $dataRow['mk_bulan'] ?? null,
                                'jenis_jabatan_id' => $dataRow['jenis_jabatan_id'] ?? null,
                                'jenis_jabatan_nama' => $dataRow['jenis_jabatan_nama'] ?? null,
                                'jabatan_id' => $dataRow['jabatan_id'] ?? null,
                                'jabatan_nama' => $dataRow['jabatan_nama'] ?? null,
                                'tmt_jabatan' => $dataRow['tmt_jabatan'] ?? null,
                                'tingkat_pendidikan_id' => $dataRow['tingkat_pendidikan_id'] ?? null,
                                'tingkat_pendidikan_nama' => $dataRow['tingkat_pendidikan_nama'] ?? null,
                                'pendidikan_id' => $dataRow['pendidikan_id'] ?? null,
                                'pendidikan_nama' => $dataRow['pendidikan_nama'] ?? null,
                                'tahun_lulus' => $dataRow['tahun_lulus'] ?? null,
                                'kpkn_id' => $dataRow['kpkn_id'] ?? null,
                                'kpkn_nama' => $dataRow['kpkn_nama'] ?? null,
                                'lokasi_kerja_id' => $dataRow['lokasi_kerja_id'] ?? null,
                                'lokasi_kerja_nama' => $dataRow['lokasi_kerja_nama'] ?? null,
                                'unor_id' => $dataRow['unor_id'] ?? null,
                                'unor_nama' => $dataRow['unor_nama'] ?? null,
                                'instansi_induk_id' => $dataRow['instansi_induk_id'] ?? null,
                                'instansi_induk_nama' => $dataRow['instansi_induk_nama'] ?? null,
                                'instansi_kerja_id' => $dataRow['instansi_kerja_id'] ?? null,
                                'instansi_kerja_nama' => $dataRow['instansi_kerja_nama'] ?? null,
                                'satuan_kerja_induk_id' => $dataRow['satuan_kerja_induk_id'] ?? null,
                                'satuan_kerja_induk_nama' => $dataRow['satuan_kerja_induk_nama'] ?? null,
                                'satuan_kerja_kerja_id' => $dataRow['satuan_kerja_kerja_id'] ?? null,
                                'satuan_kerja_kerja_nama' => $dataRow['satuan_kerja_kerja_nama'] ?? null,
                                'is_valid_nik' => $dataRow['is_valid_nik'] ?? null,
                                'nama_sekolah' => $dataRow['nama_sekolah'] ?? null,
                                'flag_ikd' => $dataRow['flag_ikd'] ?? null,
                            ]);
                        }

                        fclose($handle);

                        DB::commit();

                        Notification::make()
                            ->title('Import berhasil')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {

                        DB::rollBack();

                        Notification::make()
                            ->title('Import gagal: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('sinkronPegawai')
                ->label('Sinkron Pegawai')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(fn() => DB::table('pegawais')->truncate())
                ->action(function () {
                    DB::statement("
                    INSERT INTO pegawais (
                    pns_id,
                    nip_baru,
                    nip_lama,
                    nik,
                    nama,
                    gelar_depan,
                    gelar_belakang,
                    tempat_lahir_id,
                    tanggal_lahir,
                    jenis_kelamin,
                    agama_id,
                    jenis_kawin_id,
                    pendidikan_id,
                    unor_id,
                    nomor_hp,
                    email,
                    email_gov,
                    alamat,
                    npwp_nomor,
                    bpjs,
                    jenis_pegawai_id,
                    kedudukan_hukum_id,
                    kartu_asn_virtual,
                    is_valid_nik,
                    created_at,
                    updated_at
                )
                SELECT
                    s.pns_id,
                    s.nip_baru,
                    s.nip_lama,
                    s.nik,
                    s.nama,
                    s.gelar_depan,
                    s.gelar_belakang,
                    s.tempat_lahir_id,
                    STR_TO_DATE(NULLIF(s.tanggal_lahir,''), '%d-%m-%Y'),
                    s.jenis_kelamin,
                    s.agama_id,
                    s.jenis_kawin_id,
                    s.pendidikan_id,
                    s.unor_id,
                    s.nomor_hp,
                    s.email,
                    s.email_gov,
                    s.alamat,
                    s.npwp_nomor,
                    s.bpjs,
                    s.jenis_pegawai_id,
                    s.kedudukan_hukum_id,
                    s.kartu_asn_virtual,
                    1,
                    NOW(),
                    NOW()
                FROM staging_import s

                ON DUPLICATE KEY UPDATE
                    nama = VALUES(nama),
                    gelar_depan = VALUES(gelar_depan),
                    gelar_belakang = VALUES(gelar_belakang),
                    tanggal_lahir = VALUES(tanggal_lahir),
                    jenis_kelamin = VALUES(jenis_kelamin),
                    agama_id = VALUES(agama_id),
                    jenis_kawin_id = VALUES(jenis_kawin_id),
                    pendidikan_id = VALUES(pendidikan_id),
                    unor_id = VALUES(unor_id),
                    nomor_hp = VALUES(nomor_hp),
                    email = VALUES(email),
                    jenis_pegawai_id = VALUES(jenis_pegawai_id),
                    kedudukan_hukum_id = VALUES(kedudukan_hukum_id),
                    updated_at = NOW();
                    ");

                    Notification::make()
                        ->title('Sinkron berhasil')
                        ->success()
                        ->send();
                }),
            Action::make('sinkronPangkat')
                ->label('Sinkron Pangkat')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn() => DB::table('r_pangkats')->truncate())
                ->action(function () {

                    $affected = \DB::affectingStatement("
                        INSERT INTO r_pangkats (
                            pns_id,
                            golongan_id,
                            tmt_golongan,
                            mk_tahun,
                            mk_bulan,
                            created_at,
                            updated_at
                        )
                        SELECT
                            p.pns_id,
                            s.gol_akhir_id,

                            STR_TO_DATE(NULLIF(s.tmt_golongan, ''), '%d-%m-%Y'),

                            CAST(NULLIF(s.mk_tahun, '') AS UNSIGNED),
                            CAST(NULLIF(s.mk_bulan, '') AS UNSIGNED),

                            NOW(),
                            NOW()

                        FROM staging_import s
                        JOIN pegawais p ON p.nip_baru = s.nip_baru

                        LEFT JOIN (
                            SELECT r1.*
                            FROM r_pangkats r1
                            JOIN (
                                SELECT pns_id, MAX(tmt_golongan) as max_tmt
                                FROM r_pangkats
                                GROUP BY pns_id
                            ) r2
                            ON r1.pns_id = r2.pns_id
                            AND r1.tmt_golongan = r2.max_tmt
                        ) last
                        ON last.pns_id = p.pns_id

                        WHERE
                            s.tmt_golongan IS NOT NULL
                            AND s.tmt_golongan != ''

                            AND (
                                last.pns_id IS NULL
                                OR last.golongan_id != s.gol_akhir_id
                                OR last.tmt_golongan != STR_TO_DATE(NULLIF(s.tmt_golongan, ''), '%d-%m-%Y')
                            )
                    ");

                    \Filament\Notifications\Notification::make()
                        ->title('Sinkron pangkat selesai')
                        ->body("Data baru ditambahkan: {$affected}")
                        ->success()
                        ->send();
                }),

                Action::make('sinkronPendidikan')
                    ->label('Sinkron Pendidikan')
                    ->icon('heroicon-o-academic-cap')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn() => DB::table('r_pends')->truncate())
                    ->action(function () {

                            $affected = DB::affectingStatement("
                            INSERT INTO r_pends (
                                pns_id,
                                nip,
                                tingkat_pendidikan_id,
                                pendidikan_id,
                                nama_sekolah,
                                tahun_lulus
                            )
                            SELECT
                                p.pns_id,
                                s.nip_baru,
                                s.tingkat_pendidikan_id,
                                s.pendidikan_id,
                                s.nama_sekolah,
                                CAST(NULLIF(s.tahun_lulus, '') AS UNSIGNED)

                            FROM staging_import s
                            JOIN pegawais p ON p.nip_baru = s.nip_baru

                            LEFT JOIN (
                                SELECT r1.*
                                FROM r_pends r1
                                JOIN (
                                    SELECT pns_id, MAX(tahun_lulus) as max_tahun
                                    FROM r_pends
                                    GROUP BY pns_id
                                ) r2
                                ON r1.pns_id = r2.pns_id
                                AND r1.tahun_lulus = r2.max_tahun
                            ) last
                            ON last.pns_id = p.pns_id

                            WHERE
                                s.tahun_lulus IS NOT NULL
                                AND s.tahun_lulus != ''

                                AND (
                                    last.id IS NULL
                                    OR last.tingkat_pendidikan_id != s.tingkat_pendidikan_id
                                    OR last.pendidikan_id != s.pendidikan_id
                                    OR last.tahun_lulus != CAST(NULLIF(s.tahun_lulus, '') AS UNSIGNED)
                                    OR last.nama_sekolah != s.nama_sekolah
                                );
                            ");

                            \Filament\Notifications\Notification::make()
                                ->title('Sinkron pendidikan selesai')
                                ->body("Data baru: {$affected}")
                                ->success()
                                ->send();
                        }),
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
