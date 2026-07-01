<?php

// Nama file: database/migrations/2024_01_01_000001_update_status_enum_peminjaman.php
// Jalankan: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status untuk menambah nilai baru
        // tanpa menghapus data yang sudah ada
        DB::statement("
            ALTER TABLE peminjaman 
            MODIFY COLUMN status ENUM(
                'dipinjam',
                'dikembalikan',
                'menunggu_konfirmasi',
                'menunggu_pengembalian'
            ) NOT NULL DEFAULT 'dipinjam'
        ");
    }

    public function down(): void
    {
        // Rollback: kembalikan ke enum semula
        // Catatan: data dengan status baru akan error jika masih ada
        DB::statement("
            ALTER TABLE peminjaman 
            MODIFY COLUMN status ENUM(
                'dipinjam',
                'dikembalikan'
            ) NOT NULL DEFAULT 'dipinjam'
        ");
    }
};