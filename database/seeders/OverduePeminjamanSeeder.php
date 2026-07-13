<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\EksemplarBuku;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OverduePeminjamanSeeder extends Seeder
{
    public function run()
    {
        $anggota = Anggota::first();
        if (!$anggota) {
            echo "No anggota found\n";
            return;
        }

        $bukuList = Buku::with('eksemplar')->get();
        $created = 0;

        foreach ($bukuList as $buku) {
            if ($created >= 3) break;

            $eksemplar = $buku->eksemplar->where('status', 'tersedia')->first();
            if (!$eksemplar) continue;

            $eksemplar->update(['status' => 'dipinjam']);

            $hariTerlambat = rand(3, 15);
            $tanggalPinjam = Carbon::now()->subDays($hariTerlambat + 7);
            $tanggalKembali = Carbon::now()->subDays($hariTerlambat);

            Peminjaman::create([
                'anggota_id' => $anggota->id,
                'buku_id' => $buku->id,
                'eksemplar_id' => $eksemplar->id,
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_kembali' => $tanggalKembali,
                'status' => 'dipinjam',
                'tipe_konfirmasi' => 'pinjam',
            ]);

            $created++;
            echo "Created overdue peminjaman for: {$buku->judul} (terlambat {$hariTerlambat} hari)\n";
        }
    }
}
