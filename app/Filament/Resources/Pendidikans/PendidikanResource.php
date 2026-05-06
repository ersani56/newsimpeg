<?php

namespace App\Filament\Resources\Pendidikans;

use App\Filament\Resources\Pendidikans\Pages\CreatePendidikan;
use App\Filament\Resources\Pendidikans\Pages\EditPendidikan;
use App\Filament\Resources\Pendidikans\Pages\ListPendidikans;
use App\Filament\Resources\Pendidikans\Tables\PendidikansTable;
use App\Models\Pendidikan;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PendidikanResource extends Resource
{
    protected static ?string $model = Pendidikan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pendidikan';
    protected static UnitEnum|string|null $navigationGroup = 'Tabel Refensi';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('pendidikan_id')
                    ->label('ID Pendidikan')
                    ->required(),
                TextInput::make('tingkat_pendidikan_id')
                    ->label('ID Tingkat Pendidikan')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                TextInput::make('nama')
                    ->label('Nama Pendidikan')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: Pendidikan Agama Islam'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('pendidikan_id')
                ->label('ID Pendidikan')
                ->searchable()
                ->sortable(),
            TextColumn::make('tingkatPendidikan.nama')
                ->label('Tingkat Pendidikan')
                ->searchable()
                ->sortable(),
            TextColumn::make('nama')
                ->label('Nama Pendidikan')
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
            'index' => ListPendidikans::route('/'),
            'create' => CreatePendidikan::route('/create'),
            'edit' => EditPendidikan::route('/{record}/edit'),
        ];
    }
}
