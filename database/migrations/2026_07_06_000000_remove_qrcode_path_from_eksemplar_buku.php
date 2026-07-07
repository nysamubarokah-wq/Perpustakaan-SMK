<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eksemplar_buku', function (Blueprint $table) {
            $table->dropColumn('qrcode_path');
        });
    }

    public function down(): void
    {
        Schema::table('eksemplar_buku', function (Blueprint $table) {
            $table->string('qrcode_path')->nullable();
        });
    }
};
