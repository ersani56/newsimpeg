<?php

namespace App\Filament\Resources\Jabatans;

use App\Filament\Resources\Jabatans\Pages\CreateJabatan;
use App\Filament\Resources\Jabatans\Pages\EditJabatan;
use App\Filament\Resources\Jabatans\Pages\ListJabatans;
use App\Filament\Resources\Jabatans\Schemas\JabatanForm;
use App\Filament\Resources\Jabatans\Tables\JabatansTable;
use App\Models\Jabatan;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                TextInput::make('unor_nama')
                    ->label('Unit Organisasi'),
                TextInput::make('jabatan_nama')
                    ->label('Nama Jabatan'),
                Select::make('eselon')
                    ->label('Eselon')
                    ->options([
                        'I/a' => 'I/a',
                        'I/b' => 'I/b',
                        'II/a' => 'II/a',
                        'II/b' => 'II/b',
                        'III/a' => 'III/a',
                        'III/b' => 'III/b',
                        'IV/a' => 'IV/a',
                        'IV/b' => 'IV/b',
                    ])
                    ->placeholder('Non Eselon')
                    ->native(false), // Tampilan lebih modern
                TextInput::make('bup')
                    ->label('BUP')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('jenjang')
                    ->label('Jenjang'),
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
            TextColumn::make('kel_jab')
                ->label('Kelompok Jabatan')
                ->searchable()
                ->sortable(),
            TextColumn::make('unor_nama')
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
            TextColumn::make('bup')
                ->label('BUP')
                ->searchable()
                ->sortable(),
            TextColumn::make('jenjang')
                ->label('Jenjang')
                ->searchable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y H:i')
                ->sortable(),
        ])
        ->actions([
            EditAction::make(),
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
