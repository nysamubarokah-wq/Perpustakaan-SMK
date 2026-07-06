<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerbit', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->unsignedBigInteger('penerbit_id')->nullable()->after('penerbit');
            $table->foreign('penerbit_id')->references('id')->on('penerbit')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropForeign(['penerbit_id']);
            $table->dropColumn('penerbit_id');
        });
        Schema::dropIfExists('penerbit');
    }
};
