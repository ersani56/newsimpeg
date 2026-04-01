<?php

namespace App\Filament\Resources\KedudukanHukums\Pages;

use App\Filament\Resources\KedudukanHukums\KedudukanHukumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKedudukanHukums extends ListRecords
{
    protected static string $resource = KedudukanHukumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
