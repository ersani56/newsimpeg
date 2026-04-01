<?php

/* namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatistikPendidikanChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Pendidikan Chart';

    protected function getData(): array
    {
        $data = DB::select("
            SELECT tp.nama as pendidikan, COUNT(*) as jumlah
            FROM pegawais p
            JOIN (
                SELECT r1.*
                FROM r_pends r1
                JOIN (
                    SELECT pns_id, MAX(tahun_lulus) as max_tahun
                    FROM r_pends
                    GROUP BY pns_id
                ) r2
                ON r1.pns_id = r2.pns_id
                AND r1.tahun_lulus = r2.max_tahun
            ) rp ON rp.pns_id = p.pns_id
            LEFT JOIN tingkat_pendidikans tp ON tp.id = rp.tingkat_pendidikan_id
            GROUP BY tp.nama
            ORDER BY tp.nama;

        ");
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => array_column($data, 'jumlah'),
                ],
            ],
            'labels' => array_column($data, 'pendidikan'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
 */
