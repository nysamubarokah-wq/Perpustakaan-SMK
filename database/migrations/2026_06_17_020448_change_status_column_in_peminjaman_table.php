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
        Schema::table('peminjaman', function (Blueprint $table) {
            // Mengubah tipe kolom status menjadi string biasa dengan default 'dipinjam'
            $table->string('status', 50)->default('dipinjam')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Kembalikan ke tipe data enum bawaanmu jika migrasi di-rollback (sesuaikan pilihan enum aslimu)
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam')->change();
        });
    }
};