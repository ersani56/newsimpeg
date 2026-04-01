<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;
    protected Width|string|null $maxContentWidth = Width::Full;
}
