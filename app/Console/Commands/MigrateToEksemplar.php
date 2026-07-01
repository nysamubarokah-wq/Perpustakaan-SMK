<?php

namespace App\Console\Commands;

use App\Models\Buku;
use App\Models\EksemplarBuku;
use Illuminate\Console\Command;

class MigrateToEksemplar extends Command
{
    protected $signature = 'migrate:eksemplar {--dry-run : Tampilkan hasil tanpa menyimpan}';
    protected $description = 'Migrasi data buku lama ke sistem eksemplar. Setiap buku dengan stok > 0 akan dibuatkan eksemplar otomatis.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $bukuList = Buku::all();

        if ($bukuList->isEmpty()) {
            $this->warn('Tidak ada data buku.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$bukuList->count()} buku.");
        $this->newLine();

        $bar = $this->output->createProgressBar($bukuList->count());
        $bar->start();

        $totalEksemplar = 0;

        foreach ($bukuList as $buku) {
            $stok = (int) $buku->stok;

            if ($stok <= 0) {
                $bar->advance();
                continue;
            }

            // Cek apakah sudah ada eksemplar untuk buku ini
            $existingCount = EksemplarBuku::where('buku_id', $buku->id)->count();

            if ($existingCount >= $stok) {
                $bar->advance();
                continue;
            }

            $needCreate = $stok - $existingCount;

            if (!$dryRun) {
                for ($i = 0; $i < $needCreate; $i++) {
                    EksemplarBuku::create([
                        'buku_id' => $buku->id,
                        'kode_buku' => EksemplarBuku::generateKodeEksemplar(),
                        'status' => 'tersedia',
                        'kondisi' => 'baik',
                    ]);
                }
            }

            $totalEksemplar += $needCreate;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("[DRY RUN] Akan membuat {$totalEksemplar} eksemplar.");
        } else {
            $this->info("Berhasil membuat {$totalEksemplar} eksemplar beserta QR Code.");
        }

        return self::SUCCESS;
    }
}
