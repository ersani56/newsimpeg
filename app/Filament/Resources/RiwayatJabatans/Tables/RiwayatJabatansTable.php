<?php

namespace App\Filament\Resources\RiwayatJabatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RiwayatJabatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pns_id')
                    ->searchable(),
                TextColumn::make('jabatan_id')
                    ->searchable(),
                TextColumn::make('tmt_jabatan')
                    ->date()
                    ->sortable(),
                TextColumn::make('nomor_sk_jabatan')
                    ->searchable(),
                TextColumn::make('tanggal_sk_jabatan')
                    ->date()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
