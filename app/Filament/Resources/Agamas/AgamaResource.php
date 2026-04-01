<?php

namespace App\Filament\Resources\Agamas;

use App\Filament\Resources\Agamas\Pages\CreateAgama;
use App\Filament\Resources\Agamas\Pages\EditAgama;
use App\Filament\Resources\Agamas\Pages\ListAgamas;
use App\Filament\Resources\Agamas\Tables\AgamasTable;
use App\Models\Agama;
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

class AgamaResource extends Resource
{
    protected static ?string $model = Agama::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'agama';
    protected static ?string $modelLabel = 'Agama';
    protected static ?string $pluralModelLabel = 'Agama';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('agama')
                    ->label('Nama Agama')
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
                ->label('Nama Agama')
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
            'index' => ListAgamas::route('/'),
            'create' => CreateAgama::route('/create'),
            'edit' => EditAgama::route('/{record}/edit'),
        ];
    }
}
