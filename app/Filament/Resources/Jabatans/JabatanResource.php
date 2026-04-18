<?php

namespace App\Filament\Resources\Jabatans;

use App\Filament\Resources\Jabatans\Pages\CreateJabatan;
use App\Filament\Resources\Jabatans\Pages\EditJabatan;
use App\Filament\Resources\Jabatans\Pages\ListJabatans;
use App\Filament\Resources\Jabatans\Schemas\JabatanForm;
use App\Filament\Resources\Jabatans\Tables\JabatansTable;
use App\Models\Jabatan;
use App\Models\Unor;
use BackedEnum;
use Dom\Text;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jabatan';
    protected static UnitEnum|string|null $navigationGroup = 'Tabel Refensi';
        protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('jabatan_id')
                    ->label('ID Jabatan')
                    ->required(),

                TextInput::make('jenis_jabatan_id')
                    ->label('ID Jenis Jabatan'),

                Select::make('kel_jab')
                    ->label('Kelompok Jabatan')
                    ->options([
                        'struktural' => 'Struktural',
                        'jf guru' => 'JF Guru',
                        'jf kesehatan' => 'JF Kesehatan',
                        'jf lainnya' => 'JF Lainnya',
                        'pelaksana' => 'Pelaksana',
                    ])
                    ->native(false), // Tampilan lebih modern
                TextInput::make('unor_id')
                    ->label('ID Unor')
                    ->live(onBlur: true) // Cek database saat kursor pindah
                    ->helperText(function (Get $get) {
                        $unorId = $get('unor_id');

                        if (!$unorId) {
                            return 'Masukkan ID Unor.';
                        }

                        // Mencari di tabel unors berdasarkan kolom unor_id
                        $unor = Unor::where('unor_id', $unorId)->first();

                        if ($unor) {
                            // Menampilkan unors.nama jika ditemukan
                            return new HtmlString("<span class='text-success-600 font-bold'> {$unor->nama}</span>");
                        }

                        return new HtmlString("<span class='text-danger-600'>ID Unor '{$unorId}' tidak ditemukan.</span>");
                    })
                    ->placeholder('Masukkan kode unor_id...'),
                TextInput::make('jabatan_nama')
                    ->label('Nama Jabatan'),
                Select::make('eselon')
                    ->label('Eselon')
                    ->options([
                        'II/a' => 'II/a',
                        'II/b' => 'II/b',
                        'III/a' => 'III/a',
                        'III/b' => 'III/b',
                        'IV/a' => 'IV/a',
                        'IV/b' => 'IV/b',
                        'NULL' => 'NULL',
                    ])
                    ->native(false), // Tampilan lebih modern
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('jabatan_id')
                ->label('ID Jabatan')
                ->searchable()
                ->sortable(),
            TextColumn::make('jenis_jabatan_id')
                ->label('ID Jabatan')
                ->searchable()
                ->sortable(),
            TextColumn::make('unor_id')
                ->label('Unit Organisasi')
                ->searchable()
                ->sortable(),
            TextColumn::make('jabatan_nama')
                ->label('Nama Jabatan')
                ->searchable()
                ->sortable(),
            TextColumn::make('eselon')
                ->label('Eselon')
                ->searchable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y H:i')
                ->sortable(),
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
            'index' => ListJabatans::route('/'),
            'create' => CreateJabatan::route('/create'),
            'edit' => EditJabatan::route('/{record}/edit'),
        ];
    }
}
