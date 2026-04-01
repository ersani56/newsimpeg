<?php

namespace App\Filament\Resources\JenisJabatans\Pages;

use App\Filament\Resources\JenisJabatans\JenisJabatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJenisJabatans extends ListRecords
{
    protected static string $resource = JenisJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
