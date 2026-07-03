<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSiswaSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $kelasList = ['X IPA 1', 'X IPA 2', 'X IPA 3', 'X IPS 1', 'X IPS 2', 'XI IPA 1', 'XI IPA 2', 'XI IPA 3', 'XI IPS 1', 'XI IPS 2', 'XII IPA 1', 'XII IPA 2', 'XII IPA 3', 'XII IPS 1', 'XII IPS 2'];

        $jurusanList = ['IPA', 'IPS'];

        $jkList = ['Laki-laki', 'Perempuan'];

        $namaDepan = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gunawan', 'Hesti', 'Indra', 'Juli', 'Kartika', 'Lina', 'Mahmud', 'Nina', 'Oscar', 'Putri', 'Qori', 'Rina', 'Sari', 'Toni', 'Umbu', 'Vina', 'Wawan', 'Yuni', 'Zainal', 'Anisa', 'Bayu', 'Candra', 'Dian', 'Eri', 'Fajar', 'Gita', 'Hendra', 'Ika', 'Joko', 'Krisna', 'Lia', 'Mila', 'Naufal', 'Olivia', 'Pratiwi', 'Rudi', 'Sinta', 'Tia', 'Umar', 'Vera', 'Wahyu', 'Yanti', 'Zahra', 'Abdul', 'Bella', 'Chandra', 'Diah', 'Endah', 'Feri', 'Gilang', 'Hana', 'Iqbal', 'Jasmine', 'Kiki', 'Lutfi', 'Maya', 'Nabil', 'Octavia', 'Panji', 'Queen', 'Rizky', 'Salsa', 'Tari', 'Ulfah', 'Vicky', 'Winda', 'Yusuf', 'Zara', 'Adi', 'Bunga', 'Ciko', 'Dimas', 'Eka', 'Fira', 'Ghani', 'Hilda', 'Irfan', 'Jihan', 'Kamal', 'Nico', 'Okta', 'Puput', 'Qualis', 'Reza', 'Siti', 'Tyas', 'Ulya', 'Valdo', 'Weni', 'Yoga', 'Zizi'];

        $namaBelakang = ['Santoso', 'Wijaya', 'Kusuma', 'Pratiwi', 'Saputra', 'Nugroho', 'Wulandari', 'Permana', 'Rahman', 'Sari', 'Hidayat', 'Lestari', 'Susanto', 'Dewi', 'Prabowo', 'Kurniawan', 'Wahyuni', 'Hakim', 'Rahmawati', 'Setiawan', 'Andriani', 'Fauzi', 'Gultom', 'Hariansyah', 'Indrawan'];

        $alamatList = [
            'Jl. Merdeka No. 1',
            'Jl. Sudirman No. 15',
            'Jl. Gatot Subroto No. 22',
            'Jl. Ahmad Yani No. 8',
            'Jl. Diponegoro No. 33',
            'Jl. Pangeran Diponegoro No. 12',
            'Jl. Kalimantan No. 45',
            'Jl. Sulawesi No. 17',
            'Jl. Sumatra No. 28',
            'Jl. Jawa No. 9',
        ];

        $existingNis = DB::table('anggota')->pluck('nis')->toArray();
        $usedNis = array_merge($existingNis);

        $data = [];

        for ($i = 1; $i <= 100; $i++) {
            do {
                $nis = (string) rand(10000000, 99999999);
            } while (in_array($nis, $usedNis));
            $usedNis[] = $nis;

            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $kelas = $kelasList[array_rand($kelasList)];
            $jurusan = str_contains($kelas, 'IPA') ? 'IPA' : 'IPS';
            $jenisKelamin = $jkList[array_rand($jkList)];
            $email = strtolower(str_replace(' ', '.', $nama)) . $i . '@school.sch.id';
            $noTelepon = '08' . rand(11, 99) . rand(1000, 9999) . rand(1000, 9999);
            $alamat = $alamatList[array_rand($alamatList)] . ', Kota';
            $tanggalDaftar = date('Y-m-d', strtotime('-' . rand(1, 365) . ' days'));

            $data[] = [
                'nis' => $nis,
                'nama' => $nama,
                'kelas' => $kelas,
                'jurusan' => $jurusan,
                'jenis_kelamin' => $jenisKelamin,
                'email' => $email,
                'no_telepon' => $noTelepon,
                'alamat' => $alamat,
                'tanggal_daftar' => $tanggalDaftar,
                'status' => 'aktif',
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('anggota')->insert($data);
    }
}
