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
            $table->string('kelas')->nullable()->after('nis');
            $table->string('jurusan')->nullable()->after('kelas');
            $table->string('jenis_kelamin')->nullable()->after('jurusan');
            $table->string('status')->default('aktif')->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jurusan', 'jenis_kelamin', 'status']);
        });
    }
};
