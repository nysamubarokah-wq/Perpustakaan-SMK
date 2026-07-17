<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->decimal('total_denda', 12, 0)->nullable()->after('denda');
            $table->enum('status_denda', ['belum_dibayar', 'lunas'])->nullable()->after('total_denda');
            $table->date('tanggal_bayar')->nullable()->after('status_denda');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['total_denda', 'status_denda', 'tanggal_bayar']);
        });
    }
};
