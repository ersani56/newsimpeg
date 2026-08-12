<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->json('menu_permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();
        });

        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'description' => 'Bisa membuka semua menu',
            'is_admin' => true,
            'menu_permissions' => json_encode([
                'manajemen_user',
                'pegawai',
                'fungsional_pelaksana',
                'pejabat_struktural',
                'statistik_golongan',
                'statistik_jabatan',
                'statistik_format_jabatan',
                'statistik_agama',
                'statistik_pendidikan',
                'statistik_jenis_kelamin',
                'statistik_usia',
                'riwayat_pangkat',
                'riwayat_jabatan',
                'riwayat_pendidikan',
                'referensi_agama',
                'referensi_golongan',
                'referensi_jabatan',
                'referensi_jenis_jabatan',
                'referensi_kedudukan_hukum',
                'referensi_pendidikan',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')
            ->whereNull('role_id')
            ->update(['role_id' => $adminRoleId]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
