<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('jenis_jabatan_id', 10)->nullable()->after('jabatan_id');
            $table->string('jenis_jabatan_nama', 100)->nullable()->after('jenis_jabatan_id');
            $table->string('jabatan_nama', 200)->nullable()->after('jenis_jabatan_nama');
            $table->string('jabatan_eselon', 5)->nullable()->after('jabatan_nama');
            $table->string('jabatan_jenjang', 30)->nullable()->after('jabatan_eselon');
            $table->date('tmt_jabatan')->nullable()->after('jabatan_jenjang');
            $table->string('pendidikan_nama', 200)->nullable()->after('pendidikan_id');
            $table->unsignedTinyInteger('tingkat_pendidikan_id')->nullable()->after('pendidikan_nama');
            $table->string('tingkat_pendidikan_nama', 100)->nullable()->after('tingkat_pendidikan_id');
            $table->string('nama_sekolah', 200)->nullable()->after('tingkat_pendidikan_nama');
            $table->unsignedSmallInteger('tahun_lulus')->nullable()->after('nama_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_jabatan_id', 'jenis_jabatan_nama', 'jabatan_nama',
                'jabatan_eselon', 'jabatan_jenjang', 'tmt_jabatan',
                'pendidikan_nama', 'tingkat_pendidikan_id',
                'tingkat_pendidikan_nama', 'nama_sekolah', 'tahun_lulus',
            ]);
        });
    }
};
