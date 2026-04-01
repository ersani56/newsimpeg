<?php

namespace App\Filament\Resources\KedudukanHukums;

use App\Filament\Resources\KedudukanHukums\Pages\CreateKedudukanHukum;
use App\Filament\Resources\KedudukanHukums\Pages\EditKedudukanHukum;
use App\Filament\Resources\KedudukanHukums\Pages\ListKedudukanHukums;
use App\Filament\Resources\KedudukanHukums\Schemas\KedudukanHukumForm;
use App\Filament\Resources\KedudukanHukums\Tables\KedudukanHukumsTable;
use App\Models\KedudukanHukum;
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

class KedudukanHukumResource extends Resource
{
    protected static ?string $model = KedudukanHukum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kedudukan';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Kedudukan Hukum Pegawai')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: Aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
            return $table
        ->columns([
            TextColumn::make('nama')
                ->label('Kedudukan Hukum Pegawai')
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
            'index' => ListKedudukanHukums::route('/'),
            'create' => CreateKedudukanHukum::route('/create'),
            'edit' => EditKedudukanHukum::route('/{record}/edit'),
        ];
    }
}
