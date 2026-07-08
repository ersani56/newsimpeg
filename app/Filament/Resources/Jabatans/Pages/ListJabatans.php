<?php

namespace App\Filament\Resources\Jabatans\Pages;

use App\Filament\Resources\Jabatans\JabatanResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListJabatans extends ListRecords
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importReferensiSiasn')
                ->label('Import Referensi SIASN')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    Select::make('jenis')
                        ->label('Jenis Referensi')
                        ->options([
                            'struktural' => 'Jabatan Struktural',
                            'fungsional' => 'Jabatan Fungsional',
                            'pelaksana' => 'Jabatan Pelaksana',
                        ])
                        ->required()
                        ->native(false),
                    FileUpload::make('file')
                        ->label('File CSV Referensi SIASN')
                        ->disk('local')
                        ->directory('imports/jabatans')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $result = $this->importReferensiSiasn($data['jenis'], $data['file']);

                    if ($result['failed']) {
                        return;
                    }

                    Notification::make()
                        ->title('Referensi jabatan berhasil diimport')
                        ->body($result['imported'] . ' baris diproses. ' . $result['skipped'] . ' baris dilewati.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    private function importReferensiSiasn(string $jenis, string $file): array
    {
        $path = Storage::disk('local')->path($file);
        $handle = fopen($path, 'r');

        if (! $handle) {
            Notification::make()
                ->title('Import gagal')
                ->body('File tidak bisa dibaca.')
                ->danger()
                ->send();

            return ['imported' => 0, 'skipped' => 0, 'failed' => true];
        }

        $firstLine = fgets($handle);
        $delimiter = $this->detectDelimiter($firstLine);
        $headers = $this->normalizeHeaders(str_getcsv($firstLine, $delimiter));
        $requiredColumns = $this->requiredColumns($jenis);
        $missingColumns = array_diff($requiredColumns, $headers);

        if (! empty($missingColumns)) {
            fclose($handle);

            Notification::make()
                ->title('Import gagal')
                ->body('Kolom tidak lengkap: ' . implode(', ', $missingColumns))
                ->danger()
                ->send();

            return ['imported' => 0, 'skipped' => 0, 'failed' => true];
        }

        $indexes = array_flip($headers);
        $now = now();
        $rows = [];
        $imported = 0;
        $skipped = 0;

        while (($csvRow = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row = match ($jenis) {
                'struktural' => $this->mapStrukturalRow($csvRow, $indexes, $now),
                'fungsional' => $this->mapFungsionalRow($csvRow, $indexes, $now),
                'pelaksana' => $this->mapPelaksanaRow($csvRow, $indexes, $now),
            };

            if (! $row) {
                $skipped++;
                continue;
            }

            $rows[] = $row;

            if (count($rows) >= 500) {
                $this->upsertJabatans($rows);
                $imported += count($rows);
                $rows = [];
            }
        }

        if (! empty($rows)) {
            $this->upsertJabatans($rows);
            $imported += count($rows);
        }

        fclose($handle);

        return ['imported' => $imported, 'skipped' => $skipped, 'failed' => false];
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = ['|' => substr_count($line, '|'), ';' => substr_count($line, ';'), ',' => substr_count($line, ',')];

        arsort($delimiters);

        return array_key_first($delimiters);
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(
            fn ($header) => Str::of($header)
                ->replace("\xEF\xBB\xBF", '')
                ->trim()
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '_')
                ->trim('_')
                ->toString(),
            $headers
        );
    }

    private function requiredColumns(string $jenis): array
    {
        return match ($jenis) {
            'struktural' => ['id', 'nama_unor', 'nama_jabatan', 'eselon_id'],
            'fungsional' => ['id', 'nama', 'bup', 'jenjang'],
            'pelaksana' => ['id', 'nama'],
        };
    }

    private function mapStrukturalRow(array $csvRow, array $indexes, $now): ?array
    {
        $id = $this->csvValue($csvRow, $indexes, 'id');
        $namaJabatan = $this->csvValue($csvRow, $indexes, 'nama_jabatan');
        $eselon = $this->normalizeEselon($this->csvValue($csvRow, $indexes, 'eselon_id'));

        if (! $id || ! $namaJabatan) {
            return null;
        }

        return [
            'jabatan_id' => $id,
            'kel_jab' => $this->kelompokStruktural($namaJabatan, $eselon),
            'unor_nama' => $this->csvValue($csvRow, $indexes, 'nama_unor'),
            'jabatan_nama' => $namaJabatan,
            'eselon' => $eselon,
            'bup' => null,
            'jenjang' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function mapFungsionalRow(array $csvRow, array $indexes, $now): ?array
    {
        $id = $this->csvValue($csvRow, $indexes, 'id');
        $nama = $this->csvValue($csvRow, $indexes, 'nama');

        if (! $id || ! $nama) {
            return null;
        }

        return [
            'jabatan_id' => $id,
            'kel_jab' => $this->kelompokFungsional($nama),
            'unor_nama' => null,
            'jabatan_nama' => $nama,
            'eselon' => null,
            'bup' => $this->normalizeBup($this->csvValue($csvRow, $indexes, 'bup')),
            'jenjang' => $this->normalizeJenjang($this->csvValue($csvRow, $indexes, 'jenjang')),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function mapPelaksanaRow(array $csvRow, array $indexes, $now): ?array
    {
        $id = $this->csvValue($csvRow, $indexes, 'id');
        $nama = $this->csvValue($csvRow, $indexes, 'nama');

        if (! $id || ! $nama) {
            return null;
        }

        return [
            'jabatan_id' => $id,
            'kel_jab' => 'pelaksana',
            'unor_nama' => null,
            'jabatan_nama' => $nama,
            'eselon' => null,
            'bup' => null,
            'jenjang' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function csvValue(array $row, array $indexes, string $column): ?string
    {
        $value = $row[$indexes[$column]] ?? null;
        $value = is_string($value) ? trim($value) : $value;
        $value = is_string($value) ? ltrim($value, "'") : $value;

        return $value === '' ? null : $value;
    }

    private function normalizeEselon(?string $value): ?string
    {
        if (! $value || strtolower($value) === 'null' || $value === '99') {
            return null;
        }

        $normalized = Str::of($value)->lower()->replace(' ', '')->replace('-', '/')->toString();

        return [
            '11' => 'I/a',
            '12' => 'I/b',
            '21' => 'II/a',
            '22' => 'II/b',
            '31' => 'III/a',
            '32' => 'III/b',
            '41' => 'IV/a',
            '42' => 'IV/b',
            '1a' => 'I/a',
            '1b' => 'I/b',
            '2a' => 'II/a',
            '2b' => 'II/b',
            '3a' => 'III/a',
            '3b' => 'III/b',
            '4a' => 'IV/a',
            '4b' => 'IV/b',
            'i/a' => 'I/a',
            'i/b' => 'I/b',
            'ii/a' => 'II/a',
            'ii/b' => 'II/b',
            'iii/a' => 'III/a',
            'iii/b' => 'III/b',
            'iv/a' => 'IV/a',
            'iv/b' => 'IV/b',
        ][$normalized] ?? $value;
    }

    private function normalizeJenjang(?string $value): ?string
    {
        if (! $value || strtolower($value) === 'null') {
            return null;
        }

        $normalized = Str::of($value)->lower()->trim()->toString();

        return [
            'utama' => 'Utama',
            'madya' => 'Madya',
            'muda' => 'Muda',
            'pertama' => 'Pertama',
            'penyelia' => 'Penyelia',
            'mahir' => 'Mahir',
            'terampil' => 'Terampil',
            'pemula' => 'Pemula',
        ][$normalized] ?? Str::of($value)->title()->toString();
    }

    private function normalizeBup(?string $value): ?int
    {
        if (! $value || strtolower($value) === 'null') {
            return null;
        }

        preg_match('/\d+/', $value, $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function kelompokFungsional(string $nama): string
    {
        $lower = Str::of($nama)->lower()->toString();

        if (str_contains($lower, 'guru')) {
            return 'jf guru';
        }

        foreach (['dokter', 'perawat', 'bidan', 'apoteker', 'farmasi', 'sanitarian', 'nutrisionis', 'epidemiolog', 'radiografer', 'terapis gigi', 'rekam medis'] as $keyword) {
            if (str_contains($lower, $keyword)) {
                return 'jf kesehatan';
            }
        }

        return 'jf lainnya';
    }

    private function kelompokStruktural(string $namaJabatan, ?string $eselon): string
    {
        $lower = Str::of($namaJabatan)->lower()->toString();

        if (! $eselon && str_contains($lower, 'kepala sekolah')) {
            return 'jf guru';
        }

        return 'struktural';
    }

    private function upsertJabatans(array $rows): void
    {
        DB::table('jabatans')->upsert(
            $rows,
            ['jabatan_id'],
            ['kel_jab', 'unor_nama', 'jabatan_nama', 'eselon', 'bup', 'jenjang', 'updated_at']
        );
    }
}
