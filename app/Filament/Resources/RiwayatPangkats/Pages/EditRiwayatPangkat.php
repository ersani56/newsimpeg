<?php

namespace App\Filament\Resources\RiwayatPangkats\Pages;

use App\Filament\Resources\RiwayatPangkats\RiwayatPangkatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPangkat extends EditRecord
{
    protected static string $resource = RiwayatPangkatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
