<?php

namespace App\Filament\Resources\Golongans;

use App\Filament\Resources\Golongans\Pages\CreateGolongan;
use App\Filament\Resources\Golongans\Pages\EditGolongan;
use App\Filament\Resources\Golongans\Pages\ListGolongans;
use App\Models\Golongan;
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

class GolonganResource extends Resource
{
    protected static ?string $model = Golongan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jabatan';
    protected static UnitEnum|string|null $navigationGroup = 'Tabel Refensi';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('golru')
                    ->label('Golongan Ruang')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('pangkat')
                    ->label('Pangkat')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            TextColumn::make('golru')
                ->label('Golongan Ruang')
                ->searchable()
                ->sortable(),
            TextColumn::make('pangkat')
                ->label('Pangkat')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Dibuat')
                ->dateTime('d M Y H:i')
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
            'index' => ListGolongans::route('/'),
            'create' => CreateGolongan::route('/create'),
            'edit' => EditGolongan::route('/{record}/edit'),
        ];
    }
}
