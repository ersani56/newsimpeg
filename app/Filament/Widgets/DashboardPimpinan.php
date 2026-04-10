<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardPimpinan extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '01')
            ->orWhere('kedudukan_hukum_id', '02')
            ->orWhere('kedudukan_hukum_id', '03')
            ->orWhere('kedudukan_hukum_id', '13')
            ->orWhere('kedudukan_hukum_id', '15')
            ->orWhere('kedudukan_hukum_id', '04')
            ->orWhere('kedudukan_hukum_id', '71')
            ->orWhere('kedudukan_hukum_id', '101')
            ->count();
        $totalpns = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '01')
            ->count();
        $totalpppk = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '71')
            ->count();
        $totalpppkpw = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '101')
            ->count();
        $cltn = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '02')
            ->orWhere('kedudukan_hukum_id', '13')
            ->count();
        $tubel = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '03')
            ->count();
        $hukdis = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '15')
            ->count();
        $pemberhentiansementara = DB::table('staging_import')
            ->where('kedudukan_hukum_id', '04')
            ->count();

        return [
            Stat::make('Total Pegawai (ASN)', $total),
            Stat::make('Total Pegawai (PNS)', $totalpns),
            Stat::make('Total Pegawai (PPPK)', $totalpppk),
            Stat::make('Total Pegawai (PPPK PW)', $totalpppkpw),
            Stat::make('CLTN', $cltn),
            Stat::make('Tugas Belajar', $tubel),
            Stat::make('Hukuman Disiplin', $hukdis),
            Stat::make('Pemberhentian Sementara', $pemberhentiansementara),
        ];
    }
}
