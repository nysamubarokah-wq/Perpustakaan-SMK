<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eksemplar_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->string('kode_buku')->unique();
            $table->string('qrcode_path')->nullable();
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang', 'maintenance'])->default('tersedia');
            $table->string('kondisi')->nullable()->comment('baik, sedang, rusak_ringan, rusak_berat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eksemplar_buku');
    }
};
