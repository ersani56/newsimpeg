<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('golongans', function (Blueprint $table) {
            Schema::table('golongans', function (Blueprint $table) {
            $table->timestamps(); // otomatis created_at & updated_at
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('golongans', function (Blueprint $table) {
            Schema::table('golongans', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        });
    }
};
