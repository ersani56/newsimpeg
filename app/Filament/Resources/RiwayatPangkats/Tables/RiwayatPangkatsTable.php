<?php

namespace App\Filament\Resources\RiwayatPangkats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RiwayatPangkatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pns_id')
                    ->searchable(),
                TextColumn::make('golongan_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tmt_golongan')
                    ->date()
                    ->sortable(),
                TextColumn::make('mk_tahun')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mk_bulan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nomor_sk_gol')
                    ->searchable(),
                TextColumn::make('tanggal_sk_gol')
                    ->date()
                    ->sortable(),
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
