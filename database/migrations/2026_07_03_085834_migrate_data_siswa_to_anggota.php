<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('data_siswa')) {
            return;
        }

        // Migrate data from data_siswa to anggota for students who already have accounts
        $dataSiswaList = DB::table('data_siswa')->get();

        foreach ($dataSiswaList as $siswa) {
            $anggota = DB::table('anggota')->where('nis', $siswa->nis)->first();

            if ($anggota) {
                // Update existing anggota with kelas from data_siswa
                DB::table('anggota')
                    ->where('id', $anggota->id)
                    ->update([
                        'kelas' => $anggota->kelas ?? $siswa->kelas,
                        'updated_at' => now(),
                    ]);
            }
        }

        // Drop data_siswa table
        Schema::dropIfExists('data_siswa');
    }

    public function down(): void
    {
        // Recreate data_siswa table for rollback
        if (!Schema::hasTable('data_siswa')) {
            Schema::create('data_siswa', function ($table) {
                $table->id();
                $table->string('nis')->unique();
                $table->string('nama');
                $table->string('kelas')->nullable();
                $table->timestamps();
            });
        }
    }
};
