<?php

namespace App\Filament\Resources\JenisPegawais;

use App\Filament\Resources\JenisPegawais\Pages\CreateJenisPegawai;
use App\Filament\Resources\JenisPegawais\Pages\EditJenisPegawai;
use App\Filament\Resources\JenisPegawais\Pages\ListJenisPegawais;
use App\Filament\Resources\JenisPegawais\Schemas\JenisPegawaiForm;
use App\Filament\Resources\JenisPegawais\Tables\JenisPegawaisTable;
use App\Models\JenisPegawai;
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

class JenisPegawaiResource extends Resource
{
    protected static ?string $model = JenisPegawai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jenis pegawai';
    protected static UnitEnum|string|null $navigationGroup = 'Tabel Refensi';

    public static function form(Schema $schema): Schema
    {
            return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Jenis Pegawai')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: PNS'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('nama')
                ->label('Jenis Pegawai')
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
            'index' => ListJenisPegawais::route('/'),
            'create' => CreateJenisPegawai::route('/create'),
            'edit' => EditJenisPegawai::route('/{record}/edit'),
        ];
    }
}
