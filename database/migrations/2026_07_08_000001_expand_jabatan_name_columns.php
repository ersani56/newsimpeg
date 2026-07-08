<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->string('unor_nama', 500)->nullable()->change();
            $table->string('jabatan_nama', 500)->change();
        });
    }

    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->string('unor_nama', 150)->nullable()->change();
            $table->string('jabatan_nama', 255)->change();
        });
    }
};
