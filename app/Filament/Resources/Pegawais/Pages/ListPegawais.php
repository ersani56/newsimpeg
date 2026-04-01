<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Models\Pegawai;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTime;

class ListPegawais extends ListRecords
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    /**
     * Helper function untuk konversi tanggal
     */
    private function convertDate($date, $format = 'd/m/Y')
    {
        if (empty($date)) {
            return null;
        }

        // Coba format d/m/Y
        $dateTime = DateTime::createFromFormat('d/m/Y', $date);
        if ($dateTime) {
            return $dateTime->format('Y-m-d');
        }

        // Coba format Y-m-d
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        if ($dateTime) {
            return $dateTime->format('Y-m-d');
        }

        // Coba format lain
        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            Log::warning('Tidak dapat mengkonversi tanggal: ' . $date);
            return null;
        }
    }
}
