<?php

namespace App\Filament\Resources\RiwayatJabatans\Pages;

use App\Filament\Resources\RiwayatJabatans\RiwayatJabatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatJabatans extends ListRecords
{
    protected static string $resource = RiwayatJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
