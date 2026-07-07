<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $staging = DB::table('staging_import')
            ->where('pns_id', $this->record->pns_id)
            ->first();

        $unitKerja = DB::table('unors')
            ->where('unor_id', $this->record->unor_id)
            ->value('nama');

        $data['identitas_nama'] = trim(
            ($this->record->gelar_depan ? $this->record->gelar_depan . ' ' : '') .
            ($this->record->nama ?? '') .
            ($this->record->gelar_belakang ? ', ' . $this->record->gelar_belakang : '')
        );
        $data['identitas_nip'] = $this->record->nip_baru ?: '-';
        $data['identitas_unit_kerja'] = $unitKerja ?: ($staging?->unor_nama ?: '-');

        if ($staging) {
            $data['golongan_nama'] ??= $staging->gol_akhir_nama;
            $data['tmt_golongan'] ??= PegawaiResource::parseTanggal($staging->tmt_golongan);
            $data['mk_tahun'] ??= $staging->mk_tahun;
            $data['mk_bulan'] ??= $staging->mk_bulan;
            $data['jenis_jabatan_id'] ??= $staging->jenis_jabatan_id;
            $data['tmt_jabatan'] ??= PegawaiResource::parseTanggal($staging->tmt_jabatan);
            $data['tingkat_pendidikan_nama'] ??= $staging->tingkat_pendidikan_nama;
            $data['nama_sekolah'] ??= $staging->nama_sekolah;
            $data['tahun_lulus'] ??= $staging->tahun_lulus;
        }

        $jabatan = DB::table('jabatans')->where('jabatan_id', $data['jabatan_id'] ?? null)->first();
        $data['jabatan_eselon'] ??= $jabatan?->eselon;
        $data['jabatan_jenjang'] ??= $jabatan?->jenjang;

        $data['pangkat_nama'] = PegawaiResource::namaPangkat($data['golongan_nama'] ?? null);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['pangkat_nama'] = PegawaiResource::namaPangkat($data['golongan_nama'] ?? null);

        $golonganIds = [
            'I/a' => '11', 'I/b' => '12', 'I/c' => '13', 'I/d' => '14',
            'II/a' => '21', 'II/b' => '22', 'II/c' => '23', 'II/d' => '24',
            'III/a' => '31', 'III/b' => '32', 'III/c' => '33', 'III/d' => '34',
            'IV/a' => '41', 'IV/b' => '42', 'IV/c' => '43', 'IV/d' => '44', 'IV/e' => '45',
        ];

        if (isset($golonganIds[$data['golongan_nama'] ?? ''])) {
            $data['golongan_id'] = $golonganIds[$data['golongan_nama']];
        }

        $jabatan = DB::table('jabatans')->where('jabatan_id', $data['jabatan_id'] ?? null)->first();
        if ($jabatan) {
            $data['jabatan_nama'] = $jabatan->jabatan_nama;
        }

        $jenisJabatan = [
            '1' => 'Jabatan Struktural',
            '2' => 'Jabatan Fungsional',
            '4' => 'Jabatan Pelaksana',
        ];
        $data['jenis_jabatan_nama'] = $jenisJabatan[$data['jenis_jabatan_id'] ?? ''] ?? null;

        $pendidikan = DB::table('pendidikans as p')
            ->leftJoin('tingkat_pendidikans as tp', 'p.tingkat_pendidikan_id', '=', 'tp.tingkat_pendidikan_id')
            ->where('p.pendidikan_id', $data['pendidikan_id'] ?? null)
            ->select('p.nama', 'p.tingkat_pendidikan_id', 'tp.nama as tingkat_nama')
            ->first();

        if ($pendidikan) {
            $data['pendidikan_nama'] = $pendidikan->nama;
            $data['tingkat_pendidikan_id'] = $pendidikan->tingkat_pendidikan_id;
            $data['tingkat_pendidikan_nama'] = $pendidikan->tingkat_nama;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $kelompokJabatan = [
            '1' => 'Struktural',
            '2' => 'Fungsional',
            '4' => 'Pelaksana',
        ];

        if ($this->record->jabatan_id) {
            DB::table('jabatans')
                ->where('jabatan_id', $this->record->jabatan_id)
                ->update([
                    'kel_jab' => $kelompokJabatan[$this->record->jenis_jabatan_id] ?? 'Pelaksana',
                    'eselon' => $this->record->jabatan_eselon,
                    'jenjang' => $this->record->jabatan_jenjang,
                ]);
        }

        DB::table('staging_import')
            ->where('pns_id', $this->record->pns_id)
            ->update([
                'gol_akhir_id' => $this->record->golongan_id,
                'gol_akhir_nama' => $this->record->golongan_nama,
                'tmt_golongan' => $this->record->tmt_golongan?->format('d-m-Y'),
                'mk_tahun' => $this->record->mk_tahun,
                'mk_bulan' => $this->record->mk_bulan,
                'jabatan_id' => $this->record->jabatan_id,
                'jabatan_nama' => $this->record->jabatan_nama,
                'jenis_jabatan_id' => $this->record->jenis_jabatan_id,
                'jenis_jabatan_nama' => $this->record->jenis_jabatan_nama,
                'tmt_jabatan' => $this->record->tmt_jabatan?->format('d-m-Y'),
                'pendidikan_id' => $this->record->pendidikan_id,
                'pendidikan_nama' => $this->record->pendidikan_nama,
                'tingkat_pendidikan_id' => $this->record->tingkat_pendidikan_id,
                'tingkat_pendidikan_nama' => $this->record->tingkat_pendidikan_nama,
                'nama_sekolah' => $this->record->nama_sekolah,
                'tahun_lulus' => $this->record->tahun_lulus,
            ]);
    }

}
