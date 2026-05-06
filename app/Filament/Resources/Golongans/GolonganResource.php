<?php

namespace App\Filament\Resources\Golongans;

use App\Filament\Resources\Golongans\Pages\CreateGolongan;
use App\Filament\Resources\Golongans\Pages\EditGolongan;
use App\Filament\Resources\Golongans\Pages\ListGolongans;
use App\Models\Golongan;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
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
                Select::make('golru')
                    ->options([
                        'I/a' => 'I/a',
                        'I/b' => 'I/b',
                        'I/c' => 'I/c',
                        'I/d' => 'I/d',
                        'II/a' => 'II/a',
                        'II/b' => 'II/b',
                        'II/c' => 'II/c',
                        'II/d' => 'II/d',
                        'III/a' => 'III/a',
                        'III/b' => 'III/b',
                        'III/c' => 'III/c',
                        'III/d' => 'III/d',
                        'IV/a' => 'IV/a',
                        'IV/b' => 'IV/b',
                        'IV/c' => 'IV/c',
                        'IV/d' => 'IV/d',
                        'IV/e' => 'IV/e',
                    ])
                    ->label('Golongan Ruang')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $pangkat = [
                            'I/a' => 'Juru Muda',
                            'I/b' => 'Juru Muda Tingkat I',
                            'I/c' => 'Juru',
                            'I/d' => 'Juru Tingkat I',
                            'II/a' => 'Pengatur Muda',
                            'II/b' => 'Pengatur Muda Tingkat I',
                            'II/c' => 'Pengatur',
                            'II/d' => 'Pengatur Tingkat I',
                            'III/a' => 'Penata Muda',
                            'III/b' => 'Penata Muda Tingkat I',
                            'III/c' => 'Penata',
                            'III/d' => 'Penata Tingkat I',
                            'IV/a' => 'Pembina',
                            'IV/b' => 'Pembina Tingkat I',
                            'IV/c' => 'Pembina Utama Muda',
                            'IV/d' => 'Pembina Utama Madya',
                            'IV/e' => 'Pembina Utama',
                        ];
                        $set('pangkat', $pangkat[$state] ?? null);
                    })
                    ->unique(ignoreRecord: true),

                TextInput::make('pangkat')
                    ->label('Pangkat')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
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
