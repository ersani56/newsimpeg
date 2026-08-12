<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MenuAccess
{
    public static function options(): array
    {
        return [
            'manajemen_user' => 'Manajemen User & Role',
            'pegawai' => 'Data Pegawai',
            'fungsional_pelaksana' => 'Fungsional & Pelaksana',
            'pejabat_struktural' => 'Pejabat Struktural',
            'statistik_golongan' => 'Statistik Golongan',
            'statistik_jabatan' => 'Statistik Jabatan',
            'statistik_format_jabatan' => 'Statistik Format Jabatan',
            'statistik_agama' => 'Statistik Agama',
            'statistik_pendidikan' => 'Statistik Pendidikan',
            'statistik_jenis_kelamin' => 'Statistik Jenis Kelamin',
            'statistik_usia' => 'Statistik Usia',
            'riwayat_pangkat' => 'Riwayat Pangkat',
            'riwayat_jabatan' => 'Riwayat Jabatan',
            'riwayat_pendidikan' => 'Riwayat Pendidikan',
            'referensi_agama' => 'Referensi Agama',
            'referensi_golongan' => 'Referensi Golongan',
            'referensi_jabatan' => 'Referensi Jabatan',
            'referensi_jenis_jabatan' => 'Referensi Jenis Jabatan',
            'referensi_kedudukan_hukum' => 'Referensi Kedudukan Hukum',
            'referensi_pendidikan' => 'Referensi Pendidikan',
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::options());
    }

    public static function can(string $menuKey): bool
    {
        if (! Auth::check()) {
            return true;
        }

        if (! Schema::hasTable('roles') || ! Schema::hasColumn('users', 'role_id')) {
            return true;
        }

        $user = Auth::user()?->loadMissing('role');
        $role = $user?->role;

        if (! $role) {
            return false;
        }

        if ($role->is_admin) {
            return true;
        }

        return in_array($menuKey, $role->menu_permissions ?? [], true);
    }
}
