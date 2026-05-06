<?php

namespace App\Filament\Resources\RiwayatPangkats\Pages;

use App\Filament\Resources\RiwayatPangkats\RiwayatPangkatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPangkats extends ListRecords
{
    protected static string $resource = RiwayatPangkatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
