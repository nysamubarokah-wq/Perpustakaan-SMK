<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // GANTI DI SINI: dari 'peminjamans' jadi 'peminjaman'
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->integer('denda')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        // GANTI DI SINI JUGA: dari 'peminjamans' jadi 'peminjaman'
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('denda');
        });
    }
};