<?php

namespace App\Filament\Resources\RiwayatPendidikans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiwayatPendidikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pns_id')
                    ->required(),
                TextInput::make('pendidikan_id')
                    ->required(),
                TextInput::make('nama_sekolah'),
                TextInput::make('tahun_lulus'),
            ]);
    }
}
