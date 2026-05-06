<?php

namespace App\Filament\Resources\RiwayatPangkats\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiwayatPangkatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pns_id')
                    ->required(),
                TextInput::make('golongan_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('tmt_golongan'),
                TextInput::make('mk_tahun')
                    ->numeric(),
                TextInput::make('mk_bulan')
                    ->numeric(),
                TextInput::make('nomor_sk_gol'),
                DatePicker::make('tanggal_sk_gol'),
            ]);
    }
}
