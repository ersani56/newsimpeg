<?php

namespace App\Filament\Resources\RiwayatJabatans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiwayatJabatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pns_id')
                    ->required(),
                TextInput::make('jabatan_id')
                    ->required(),
                DatePicker::make('tmt_jabatan'),
                TextInput::make('nomor_sk_jabatan'),
                DatePicker::make('tanggal_sk_jabatan'),
                TextInput::make('is_active')
                    ->required(),
            ]);
    }
}
