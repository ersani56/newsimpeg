<?php

namespace App\Filament\Resources\RiwayatPendidikans;

use App\Filament\Resources\RiwayatPendidikans\Pages\CreateRiwayatPendidikan;
use App\Filament\Resources\RiwayatPendidikans\Pages\EditRiwayatPendidikan;
use App\Filament\Resources\RiwayatPendidikans\Pages\ListRiwayatPendidikans;
use App\Filament\Resources\RiwayatPendidikans\Schemas\RiwayatPendidikanForm;
use App\Filament\Resources\RiwayatPendidikans\Tables\RiwayatPendidikansTable;
use App\Models\RiwayatPendidikan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RiwayatPendidikanResource extends Resource
{
    protected static ?string $model = RiwayatPendidikan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RIwayat Pendidikan';
    protected static string|UnitEnum|null $navigationGroup = 'Data Riwayat';

    public static function form(Schema $schema): Schema
    {
        return RiwayatPendidikanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiwayatPendidikansTable::configure($table);
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
            'index' => ListRiwayatPendidikans::route('/'),
            'create' => CreateRiwayatPendidikan::route('/create'),
            'edit' => EditRiwayatPendidikan::route('/{record}/edit'),
        ];
    }
}
