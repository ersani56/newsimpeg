<?php

namespace App\Filament\Resources\RiwayatPangkats;

use App\Filament\Resources\RiwayatPangkats\Pages\CreateRiwayatPangkat;
use App\Filament\Resources\RiwayatPangkats\Pages\EditRiwayatPangkat;
use App\Filament\Resources\RiwayatPangkats\Pages\ListRiwayatPangkats;
use App\Filament\Resources\RiwayatPangkats\Schemas\RiwayatPangkatForm;
use App\Filament\Resources\RiwayatPangkats\Tables\RiwayatPangkatsTable;
use App\Models\RiwayatPangkat;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RiwayatPangkatResource extends Resource
{
    protected static ?string $model = RiwayatPangkat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Riwayatpangkat';
    protected static string|UnitEnum|null $navigationGroup = 'Data Riwayat';

    public static function form(Schema $schema): Schema
    {
        return RiwayatPangkatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiwayatPangkatsTable::configure($table);
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
            'index' => ListRiwayatPangkats::route('/'),
            'create' => CreateRiwayatPangkat::route('/create'),
            'edit' => EditRiwayatPangkat::route('/{record}/edit'),
        ];
    }
}
