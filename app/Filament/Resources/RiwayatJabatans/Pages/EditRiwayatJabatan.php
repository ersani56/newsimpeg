<?php

namespace App\Filament\Resources\RiwayatJabatans\Pages;

use App\Filament\Resources\RiwayatJabatans\RiwayatJabatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatJabatan extends EditRecord
{
    protected static string $resource = RiwayatJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
