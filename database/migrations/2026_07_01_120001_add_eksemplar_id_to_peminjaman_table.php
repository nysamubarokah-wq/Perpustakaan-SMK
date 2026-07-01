<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreignId('eksemplar_id')->nullable()->after('buku_id')->constrained('eksemplar_buku')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['eksemplar_id']);
            $table->dropColumn('eksemplar_id');
        });
    }
};
