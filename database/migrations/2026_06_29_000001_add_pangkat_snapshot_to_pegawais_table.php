<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('golongan_nama', 20)->nullable()->after('golongan_id');
            $table->string('pangkat_nama', 100)->nullable()->after('golongan_nama');
            $table->date('tmt_golongan')->nullable()->after('pangkat_nama');
            $table->unsignedTinyInteger('mk_tahun')->nullable()->after('tmt_golongan');
            $table->unsignedTinyInteger('mk_bulan')->nullable()->after('mk_tahun');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'golongan_nama',
                'pangkat_nama',
                'tmt_golongan',
                'mk_tahun',
                'mk_bulan',
            ]);
        });
    }
};
