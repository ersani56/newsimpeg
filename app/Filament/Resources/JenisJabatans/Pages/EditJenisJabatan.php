<?php

namespace App\Filament\Resources\JenisJabatans\Pages;

use App\Filament\Resources\JenisJabatans\JenisJabatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJenisJabatan extends EditRecord
{
    protected static string $resource = JenisJabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
