<?php

namespace App\Filament\Resources\JenisJabatans;

use App\Filament\Resources\JenisJabatans\Pages\CreateJenisJabatan;
use App\Filament\Resources\JenisJabatans\Pages\EditJenisJabatan;
use App\Filament\Resources\JenisJabatans\Pages\ListJenisJabatans;
use App\Filament\Resources\JenisJabatans\Schemas\JenisJabatanForm;
use App\Filament\Resources\JenisJabatans\Tables\JenisJabatansTable;
use App\Models\JenisJabatan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class JenisJabatanResource extends Resource
{
    protected static ?string $model = JenisJabatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'JenisJabatan';
    protected static UnitEnum|string|null $navigationGroup = 'Tabel Refensi';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Jenis Jabatan')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: Islam'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('nama')
                ->label('Jenis Jabatan')
                ->searchable()
                ->sortable(),
        ])
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
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
            'index' => ListJenisJabatans::route('/'),
            'create' => CreateJenisJabatan::route('/create'),
            'edit' => EditJenisJabatan::route('/{record}/edit'),
        ];
    }
}
