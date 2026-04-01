<?php

namespace App\Filament\Resources\KedudukanHukums\Pages;

use App\Filament\Resources\KedudukanHukums\KedudukanHukumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKedudukanHukum extends EditRecord
{
    protected static string $resource = KedudukanHukumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
