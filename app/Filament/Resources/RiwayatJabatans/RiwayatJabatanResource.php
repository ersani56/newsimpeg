<?php

namespace App\Filament\Resources\RiwayatJabatans;

use App\Filament\Resources\RiwayatJabatans\Pages\CreateRiwayatJabatan;
use App\Filament\Resources\RiwayatJabatans\Pages\EditRiwayatJabatan;
use App\Filament\Resources\RiwayatJabatans\Pages\ListRiwayatJabatans;
use App\Filament\Resources\RiwayatJabatans\Schemas\RiwayatJabatanForm;
use App\Filament\Resources\RiwayatJabatans\Tables\RiwayatJabatansTable;
use App\Models\RiwayatJabatan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RiwayatJabatanResource extends Resource
{
    protected static ?string $model = RiwayatJabatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RiwayatPendidikan';
    protected static string|UnitEnum|null $navigationGroup = 'Data Riwayat';

    public static function form(Schema $schema): Schema
    {
        return RiwayatJabatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiwayatJabatansTable::configure($table);
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
            'index' => ListRiwayatJabatans::route('/'),
            'create' => CreateRiwayatJabatan::route('/create'),
            'edit' => EditRiwayatJabatan::route('/{record}/edit'),
        ];
    }
}
