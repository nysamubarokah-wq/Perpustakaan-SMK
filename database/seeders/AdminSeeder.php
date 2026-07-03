<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $existingAdmin = DB::table('users')->where('role', 'admin')->first();

        if ($existingAdmin) {
            $this->command->info('Admin already exists: ' . $existingAdmin->name);
            return;
        }

        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin Perpustakaan',
            'nis' => '99999999',
            'email' => 'admin@perpustakaan.sch.id',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('anggota')->insert([
            'user_id' => $userId,
            'nama' => 'Admin Perpustakaan',
            'email' => 'admin@perpustakaan.sch.id',
            'nis' => '99999999',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Admin No. 1',
            'tanggal_daftar' => now()->toDateString(),
            'kelas' => '-',
            'jurusan' => '-',
            'jenis_kelamin' => 'Laki-laki',
            'status' => 'aktif',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin created successfully!');
        $this->command->info('NIS: 99999999');
        $this->command->info('Nama: Admin Perpustakaan');
    }
}
