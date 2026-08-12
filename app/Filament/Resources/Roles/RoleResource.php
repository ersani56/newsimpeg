<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Models\Role;
use App\Support\MenuAccess;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Role';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Role';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return MenuAccess::can('manajemen_user');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
                Toggle::make('is_admin')
                    ->label('Admin penuh')
                    ->helperText('Jika aktif, role ini bisa membuka semua menu.'),
                CheckboxList::make('menu_permissions')
                    ->label('Menu yang boleh dibuka')
                    ->options(MenuAccess::options())
                    ->columns(2)
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Role $record): bool => $record->is_admin),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
