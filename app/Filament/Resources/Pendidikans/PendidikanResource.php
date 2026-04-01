<?php

namespace App\Filament\Resources\Pendidikans;

use App\Filament\Resources\Pendidikans\Pages\CreatePendidikan;
use App\Filament\Resources\Pendidikans\Pages\EditPendidikan;
use App\Filament\Resources\Pendidikans\Pages\ListPendidikans;
use App\Filament\Resources\Pendidikans\Schemas\PendidikanForm;
use App\Filament\Resources\Pendidikans\Tables\PendidikansTable;
use App\Models\Pendidikan;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PendidikanResource extends Resource
{
    protected static ?string $model = Pendidikan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pendidikan';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('tk_pendidikan')
                    ->label('Tingkat Pendidikan')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: S1'),
                TextInput::make('jurusan')
                    ->label('Jususan')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: Pendidikan Agama Islam'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PendidikansTable::configure($table);
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
            'index' => ListPendidikans::route('/'),
            'create' => CreatePendidikan::route('/create'),
            'edit' => EditPendidikan::route('/{record}/edit'),
        ];
    }
}
