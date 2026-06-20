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
        Schema::table('anggota', function (Blueprint $table) {
            // Kode ini menggantikan // yang tadi
            $table->string('role')->default('siswa')->after('tanggal_daftar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            // Kode ini menggantikan // yang tadi
            $table->dropColumn('role');
        });
    }
};